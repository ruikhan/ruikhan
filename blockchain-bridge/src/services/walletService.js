import dotenv from 'dotenv';
import { ethers } from 'ethers';
dotenv.config({ path: new URL('../../.env', import.meta.url).pathname });

// Shared provider and wallet — created once, reused
let _provider = null;
let _wallet = null;

export function getProvider() {
    if (!_provider) {
        _provider = new ethers.JsonRpcProvider(process.env.RPC_URL);
    }
    return _provider;
}

export function getWallet() {
    if (!_wallet) {
        if (!process.env.PRIVATE_KEY) {
            throw new Error('PRIVATE_KEY not set in .env');
        }
        _wallet = new ethers.Wallet(process.env.PRIVATE_KEY, getProvider());
    }
    return _wallet;
}

export async function getBalance() {
    const provider = getProvider();
    const wallet = getWallet();
    const balance = await provider.getBalance(wallet.address);
    return {
        address: wallet.address,
        balance: ethers.formatEther(balance),
        network: process.env.NETWORK_NAME,
    };
}