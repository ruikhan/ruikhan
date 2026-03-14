import { ethers } from "ethers";
import hre from "hardhat";

async function main() {
  console.log("\n🚀 Deploying DocumentRegistry to Polygon Amoy...");

  // Get the provider and signer from Hardhat v3 runtime
  const provider = new ethers.JsonRpcProvider(
    "https://rpc-amoy.polygon.technology"
  );

  const privateKey = process.env.PRIVATE_KEY;
  if (!privateKey) throw new Error("PRIVATE_KEY not set in .env");

  const deployer = new ethers.Wallet(privateKey, provider);

  console.log("   Deployer :", deployer.address);

  const balance = await provider.getBalance(deployer.address);
  console.log("   Balance  :", ethers.formatEther(balance), "MATIC");

  if (balance === 0n) {
    throw new Error("❌ No MATIC! Get test MATIC from https://faucet.polygon.technology");
  }

  // Load compiled artifact
  const artifact = await hre.artifacts.readArtifact("DocumentRegistry");

  console.log("\n⏳ Deploying...");

  const factory = new ethers.ContractFactory(
    artifact.abi,
    artifact.bytecode,
    deployer
  );

  const contract = await factory.deploy();
  await contract.waitForDeployment();

  const address = await contract.getAddress();
  const deployTx = contract.deploymentTransaction();

  console.log("\n✅ DocumentRegistry deployed successfully!");
  console.log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
  console.log("   Contract Address :", address);
  console.log("   TX Hash          :", deployTx?.hash);
  console.log("   Explorer         :", `https://amoy.polygonscan.com/address/${address}`);
  console.log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
  console.log("\n📋 NOW DO THIS:");
  console.log("   1. Open  blockchain-bridge\\.env");
  console.log("   2. Set   CONTRACT_ADDRESS=" + address);
  console.log("   3. Restart bridge: node server.js\n");
}

main().catch((error) => {
  console.error("\n❌ Deployment failed:", error.message);
  process.exitCode = 1;
});