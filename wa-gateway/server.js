const express = require('express');
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const QRCode = require('qrcode');
const cors = require('cors');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());

// Session storage
const sessions = new Map();

// Helper to get session directory
const getSessionDir = (accountId) => {
    return path.join(__dirname, 'sessions', `session-${accountId}`);
};

// Start a session
const startSession = async (accountId, res = null) => {
    const sessionDir = getSessionDir(accountId);

    // Ensure directory exists
    if (!fs.existsSync(sessionDir)) {
        fs.mkdirSync(sessionDir, { recursive: true });
    }

    const { state, saveCreds } = await useMultiFileAuthState(sessionDir);

    const sock = makeWASocket({
        auth: state,
        printQRInTerminal: true,
    });

    // Handle connection update
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            console.log(`QR for Account ${accountId} generated`);
            if (res && !res.headersSent) {
                // If this was triggered by an API call, return the QR directly
                try {
                    const qrUrl = await QRCode.toDataURL(qr);
                    res.json({ status: true, qr: qrUrl, message: 'Scan QR Code' });
                } catch (err) {
                    res.status(500).json({ status: false, message: 'QR Generation Failed' });
                }
            }
        }

        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect.error)?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log(`Connection closed for ${accountId}, reconnecting: ${shouldReconnect}`);

            if (shouldReconnect) {
                startSession(accountId);
            } else {
                // Clean up session if logged out
                sessions.delete(accountId);
                if (fs.existsSync(sessionDir)) {
                    fs.rmSync(sessionDir, { recursive: true, force: true });
                }
            }
        } else if (connection === 'open') {
            console.log(`Account ${accountId} connected`);
        }
    });

    sock.ev.on('creds.update', saveCreds);

    sessions.set(accountId, sock);
    return sock;
};

// --- Endpoints ---

// 1. Get QR / Start Session
app.post('/session/start', async (req, res) => {
    const { accountId } = req.body;
    if (!accountId) return res.status(400).json({ status: false, message: 'Account ID required' });

    // If session exists and is connected
    const existingSock = sessions.get(accountId);
    // Note: Baileys doesn't have a simple 'isConnected' property, but we can check existence
    // Real implementation would be more robust checking socket state

    try {
        await startSession(accountId, res);
        // Note: The response is handled inside the event listener for the first QR
        // This is a simplified approach. For robustness, we should handle timeouts.
    } catch (error) {
        console.error(error);
        if (!res.headersSent) res.status(500).json({ status: false, message: error.message });
    }
});

// 2. Get Status
app.get('/session/status/:accountId', (req, res) => {
    const { accountId } = req.params;
    const sessionDir = getSessionDir(accountId);

    // Simple check: if creds exist and we have an active memory session
    if (sessions.has(accountId) && fs.existsSync(sessionDir)) {
        // We could check `sessions.get(accountId).user` for details
        const user = sessions.get(accountId).user;
        if (user) {
            return res.json({
                status: 'connected',
                phone: user.id.split(':')[0],
                name: user.name || 'WhatsApp User'
            });
        }
    }

    return res.json({ status: 'waiting_scan' });
});

// 3. Send Message
app.post('/chat/send', async (req, res) => {
    const { accountId, to, message } = req.body;
    const sock = sessions.get(accountId);

    if (!sock) {
        return res.status(404).json({ status: false, message: 'Session not found or disconnected' });
    }

    try {
        const id = to.includes('@s.whatsapp.net') ? to : `${to}@s.whatsapp.net`;
        const sent = await sock.sendMessage(id, { text: message });
        res.json({ status: true, data: sent });
    } catch (error) {
        res.status(500).json({ status: false, message: error.message });
    }
});

// 4. Terminate / Logout
app.post('/session/terminate/:accountId', async (req, res) => {
    const { accountId } = req.params;
    const sock = sessions.get(accountId);

    if (sock) {
        try {
            await sock.logout();
            sessions.delete(accountId);
            res.json({ status: true, message: 'Logged out successfully' });
        } catch (error) {
            res.status(500).json({ status: false, message: error.message });
        }
    } else {
        res.status(404).json({ status: false, message: 'Session not found' });
    }
});

app.listen(PORT, () => {
    console.log(`WhatsApp Gateway running on port ${PORT}`);
});
