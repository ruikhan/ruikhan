import { ethers } from 'ethers';
import { readFileSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';
import { getProvider, getWallet } from './walletService.js';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ABI = JSON.parse(
    readFileSync(join(__dirname, '../contracts/DocumentRegistry.abi.json'), 'utf8')
);

function getContract(signerOrProvider = null) {
    const address = process.env.CONTRACT_ADDRESS;
    if (!address) throw new Error('CONTRACT_ADDRESS not set in .env');
    const runner = signerOrProvider || getWallet();
    return new ethers.Contract(address, ABI, runner);
}

// ─── WRITE: Register document on-chain ───────────────────────────────────────
export async function anchorDocument({ trackingCode, documentHash, documentType, department, issuedAt }) {
    const contract = getContract(getWallet());

    // Convert hex string to bytes32
    const hashBytes = ethers.zeroPadValue(ethers.getBytes('0x' + documentHash.replace('0x', '')), 32);

    const tx = await contract.registerDocument(
        trackingCode,
        hashBytes,
        documentType,
        department,
        BigInt(issuedAt)
    );

    console.log(`[ANCHOR] TX sent: ${tx.hash}`);
    const receipt = await tx.wait(1); // Wait for 1 confirmation
    console.log(`[ANCHOR] Confirmed in block: ${receipt.blockNumber}`);

    return {
        success: true,
        txHash: tx.hash,
        blockNumber: receipt.blockNumber,
        gasUsed: receipt.gasUsed.toString(),
        network: process.env.NETWORK_NAME,
        explorerUrl: `https://amoy.polygonscan.com/tx/${tx.hash}`,
    };
}

// ─── READ: Verify document against chain ─────────────────────────────────────
export async function verifyDocument({ trackingCode, claimedHash }) {
    const contract = getContract(getProvider());

    const hashBytes = ethers.zeroPadValue(ethers.getBytes('0x' + claimedHash.replace('0x', '')), 32);
    const result = await contract.verifyDocument(trackingCode, hashBytes);

    return {
        isAuthentic: result[0],
        isRevoked: result[1],
        issuedAt: result[2].toString(),
        issuedBy: result[3],
    };
}

// ─── READ: Get full document record ──────────────────────────────────────────
export async function getDocument(trackingCode) {
    const contract = getContract(getProvider());
    const doc = await contract.getDocument(trackingCode);

    if (doc.issuedAt === 0n) return null;

    return {
        documentHash: doc.documentHash,
        issuedBy: doc.issuedBy,
        issuedAt: doc.issuedAt.toString(),
        documentType: doc.documentType,
        department: doc.department,
        isRevoked: doc.isRevoked,
    };
}

// ─── WRITE: Revoke document ───────────────────────────────────────────────────
export async function revokeDocument(trackingCode) {
    const contract = getContract(getWallet());
    const tx = await contract.revokeDocument(trackingCode);
    const receipt = await tx.wait(1);
    return { success: true, txHash: tx.hash, blockNumber: receipt.blockNumber };
}