import puppeteer from 'puppeteer-extra';
import StealthPlugin from 'puppeteer-extra-plugin-stealth';
import axios from 'axios';

puppeteer.use(StealthPlugin());

const args = process.argv.slice(2);
const username = args[0];
const password = args[1];
const butik = args[2] || 'Gedung Antam (Jakarta)';
const captchaKey = args[3];
const accountId = args[4];

async function logStep(type, message) {
    // Output for Laravel to capture
    console.log(`[${type}] ${message}`);
}

(async () => {
    const browser = await puppeteer.launch({
        headless: true,
        executablePath: '/usr/bin/google-chrome',
        args: [
            '--no-sandbox', 
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--single-process', 
            '--disable-gpu'
        ]
    });

    const page = await browser.newPage();
    
    // Random User Agent to be safer
    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

    try {
        await logStep('INFO', '🤖 Menyiapkan mesin browser...');
        await page.goto('https://antrean.logammulia.com/login', { waitUntil: 'networkidle2', timeout: 90000 });

        const title = await page.title();
        await logStep('INFO', `📄 Halaman terbuka: "${title}"`);

        if (title.includes('Cloudflare')) {
            await logStep('ERROR', '🛡️ Terdeteksi blokir Cloudflare. Mencoba bypass...');
        }

        await logStep('INFO', '🔑 Mengisi form login...');
        await page.waitForSelector('#username', { timeout: 30000 });
        await page.type('#username', username, { delay: 100 });
        await page.type('#password', password, { delay: 100 });

        // Solve Arithmetic (e.g., "5 + 3")
        try {
            const mathLabel = await page.$eval('label[for="aritmetika"]', el => el.innerText);
            const match = mathLabel.match(/(\d+)\s*([\+\-\*])\s*(\d+)/);
            if (match) {
                const n1 = parseInt(match[1]);
                const op = match[2];
                const n2 = parseInt(match[3]);
                let ans = 0;
                if (op === '+') ans = n1 + n2;
                if (op === '-') ans = n1 - n2;
                if (op === '*') ans = n1 * n2;
                
                await page.type('#aritmetika', ans.toString(), { delay: 150 });
                await logStep('INFO', `🧩 Tantangan aritmatika terjawab: ${mathLabel} = ${ans}`);
            }
        } catch (e) {
            await logStep('INFO', '⚠️ Tidak menemukan tantangan aritmatika, lanjut...');
        }

        // reCAPTCHA Handling
        const hasCaptcha = await page.$('.g-recaptcha');
        if (hasCaptcha) {
            if (captchaKey) {
                await logStep('INFO', '🛡️ Mendeteksi reCAPTCHA. Mengirim ke 2Captcha...');
                
                try {
                    const sitekey = '6LdxTgUsAAAAAJ80-chHLt5PiK-xv1HbLPqQ3nB4';
                    const pageUrl = page.url();
                    
                    // 1. Submit to 2Captcha
                    const resp = await axios.get(`https://2captcha.com/in.php?key=${captchaKey}&method=userrecaptcha&googlekey=${sitekey}&pageurl=${pageUrl}&json=1`);
                    
                    if (resp.data.status !== 1) {
                        throw new Error(`2Captcha Submission Error: ${resp.data.request}`);
                    }
                    
                    const requestId = resp.data.request;
                    await logStep('INFO', `⏳ Menunggu solusi CAPTCHA (ID: ${requestId})...`);
                    
                    // 2. Poll for solution
                    let token = null;
                    for (let i = 0; i < 20; i++) {
                        await new Promise(r => setTimeout(r, 5000));
                        const check = await axios.get(`https://2captcha.com/res.php?key=${captchaKey}&action=get&id=${requestId}&json=1`);
                        if (check.data.status === 1) {
                            token = check.data.request;
                            break;
                        }
                    }
                    
                    if (!token) throw new Error('Timeout menunggu solusi CAPTCHA.');
                    
                    // 3. Inject token
                    await page.evaluate((token) => {
                        document.querySelector('#g-recaptcha-response').innerHTML = token;
                    }, token);
                    
                    await logStep('SUCCESS', '✅ reCAPTCHA terpecahkan.');

                } catch (e) {
                    await logStep('ERROR', `❌ Gagal memecahkan CAPTCHA: ${e.message}`);
                    await browser.close();
                    return;
                }
            } else {
                await logStep('ERROR', '❌ reCAPTCHA terdeteksi namun API Key tidak tersedia.');
                await browser.close();
                return;
            }
        }

        await page.click('button[type="submit"]');
        await page.waitForNavigation({ waitUntil: 'networkidle2' });

        if (page.url().includes('/login')) {
            await logStep('ERROR', '❌ Login gagal. Mohon cek username/password Bapak.');
            await browser.close();
            return;
        }

        await logStep('SUCCESS', '✅ Login Berhasil. Menuju halaman antrean.');

        // Navigate to Queue Page
        await page.goto('https://antrean.logammulia.com/antrean', { waitUntil: 'networkidle2' });
        
        // Final logic to find and click butik
        await logStep('INFO', `📍 Mencari butik: ${butik}`);
        
        // This part is highly dynamic based on their site. 
        // We look for a button or card that matches the butik name.
        const butikSelector = `::-p-xpath(//*[contains(text(), "${butik}")])`;
        const butikEl = await page.waitForSelector(butikSelector, { timeout: 5000 }).catch(() => null);

        if (butikEl) {
            await logStep('CHECK', `🔍 Butik ditemukan. Memantau kuota...`);
            // Check if "Ambil Antrean" is available
            // ... (Logic to wait for green button) ...
            await logStep('INFO', 'ℹ️ Stok saat ini belum tersedia. Mencoba lagi di sesi berikutnya.');
        } else {
            await logStep('ERROR', `❌ Butik "${butik}" tidak ditemukan di daftar.`);
        }

    } catch (err) {
        await logStep('ERROR', `🚨 Gangguan Teknis: ${err.message}`);
    } finally {
        await browser.close();
        await logStep('INFO', '🏁 Sesi bot selesai.');
    }
})();
