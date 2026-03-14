import { Router } from 'express';
import { getBalance } from '../services/walletService.js';

const router = Router();

// Health check + wallet balance — useful during dev
router.get('/balance', async (req, res) => {
    try {
        const info = await getBalance();
        return res.json({ success: true, ...info });
    } catch (error) {
        return res.status(500).json({ success: false, error: error.message });
    }
});

export default router;