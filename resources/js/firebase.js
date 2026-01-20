import { initializeApp } from "firebase/app";
import { getDatabase } from "firebase/database";

// Firebase configuration
const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
    databaseURL: import.meta.env.VITE_FIREBASE_DATABASE_URL,
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
    appId: import.meta.env.VITE_FIREBASE_APP_ID
};

let database = null;

// Defensive check: only initialize if core values exist and are not empty strings/placeholders
const isConfigValid = firebaseConfig.apiKey && 
                     firebaseConfig.projectId && 
                     firebaseConfig.databaseURL && 
                     !firebaseConfig.apiKey.startsWith('YOUR_');

if (isConfigValid) {
    try {
        const app = initializeApp(firebaseConfig);
        // Explicitly pass the database URL to getDatabase to avoid auto-discovery issues
        database = getDatabase(app, firebaseConfig.databaseURL);
        console.log("[Operra] Firebase initialized successfully.");
    } catch (error) {
        console.error("[Operra] Firebase connection failed:", error.message);
    }
} else {
    console.warn("[Operra] Firebase configuration is missing or invalid. Real-time features are disabled.");
}

export { database };
