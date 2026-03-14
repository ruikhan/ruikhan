import { Router } from 'express';
import { getDocument, verifyDocument } from '../services/contractService.js';

const router = Router();

router.post('/', async (req, res) => {
    try {
        const { trackingCode, claimedHash } = req.body;

        if (!trackingCode || !claimedHash) {
            return res.status(400).json({ success: false, error: 'trackingCode and claimedHash required' });
        }

        console.log(`[VERIFY] Verifying document: ${trackingCode}`);
        const result = await verifyDocument({ trackingCode, claimedHash });

        return res.json({ success: true, ...result });

    } catch (error) {
        console.error('[VERIFY ERROR]', error.message);
        return res.status(500).json({ success: false, error: error.message });
    }
});

// GET full record — for admin panel
router.get('/:trackingCode', async (req, res) => {
    try {
        const doc = await getDocument(req.params.trackingCode);
        if (!doc) return res.status(404).json({ success: false, error: 'Document not found on chain' });
        return res.json({ success: true, document: doc });
    } catch (error) {
        return res.status(500).json({ success: false, error: error.message });
    }
});

export default router;