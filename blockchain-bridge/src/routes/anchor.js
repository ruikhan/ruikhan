import { Router } from 'express';
import { anchorDocument } from '../services/contractService.js';

const router = Router();

router.post('/', async (req, res) => {
    try {
        const { trackingCode, documentHash, documentType, department, issuedAt } = req.body;

        // Validate required fields
        if (!trackingCode || !documentHash || !documentType || !department || !issuedAt) {
            return res.status(400).json({
                success: false,
                error: 'Missing required fields: trackingCode, documentHash, documentType, department, issuedAt'
            });
        }

        console.log(`[ANCHOR] Anchoring document: ${trackingCode}`);
        const result = await anchorDocument({ trackingCode, documentHash, documentType, department, issuedAt });

        return res.json(result);

    } catch (error) {
        console.error('[ANCHOR ERROR]', error.message);

        // Handle "already registered" revert
        if (error.message?.includes('already registered')) {
            return res.status(409).json({ success: false, error: 'Document already anchored on-chain' });
        }

        return res.status(500).json({ success: false, error: error.message });
    }
});

export default router;