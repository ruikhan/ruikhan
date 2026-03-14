import cors from 'cors';
import dotenv from 'dotenv';
import express from 'express';
import helmet from 'helmet';
import morgan from 'morgan';

import anchorRouter from './src/routes/anchor.js';
import verifyRouter from './src/routes/verify.js';
import walletRouter from './src/routes/wallet.js';

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3001;

// ─── Middleware ───────────────────────────────────────────────────────────────
app.use(helmet());
app.use(cors({ origin: ['http://localhost:8000', 'http://127.0.0.1:8000'] }));
app.use(express.json());
app.use(morgan('dev'));

// ─── API Key Guard ────────────────────────────────────────────────────────────
app.use((req, res, next) => {
    // Skip for health check
    if (req.path === '/health') return next();
    const key = req.headers['x-api-key'];
    if (key !== process.env.API_SECRET) {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    next();
});

// ─── Routes ───────────────────────────────────────────────────────────────────
app.get('/health', (req, res) => res.json({
    status: 'ok',
    service: 'E-PILI Blockchain Bridge',
    network: process.env.NETWORK_NAME,
    contract: process.env.CONTRACT_ADDRESS || 'NOT DEPLOYED YET',
    timestamp: new Date().toISOString(),
}));

app.use('/anchor', anchorRouter);
app.use('/verify', verifyRouter);
app.use('/wallet', walletRouter);

// ─── 404 ─────────────────────────────────────────────────────────────────────
app.use((req, res) => res.status(404).json({ error: 'Route not found' }));

// ─── Start ────────────────────────────────────────────────────────────────────
app.listen(PORT, () => {
    console.log(`\n🔗 E-PILI Blockchain Bridge`);
    console.log(`   Running on  → http://localhost:${PORT}`);
    console.log(`   Network     → ${process.env.NETWORK_NAME}`);
    console.log(`   Contract    → ${process.env.CONTRACT_ADDRESS || '⚠️  Not set yet'}`);
    console.log(`   Health      → http://localhost:${PORT}/health\n`);
});