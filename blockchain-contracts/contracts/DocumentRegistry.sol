// SPDX-License-Identifier: MIT
// E-PILI Document Registry — TEKNOFEST Blockchain Competition 2026
pragma solidity ^0.8.20;

contract DocumentRegistry {

    address public owner;
    mapping(address => bool) public authorizedIssuers;

    struct DocumentRecord {
        bytes32 documentHash;
        address issuedBy;
        uint256 issuedAt;
        string  documentType;
        string  department;
        bool    isRevoked;
    }

    mapping(string => DocumentRecord) private _documents;

    event DocumentRegistered(
        string indexed trackingCode,
        bytes32 documentHash,
        address issuedBy,
        uint256 issuedAt
    );
    event DocumentRevoked(string indexed trackingCode, uint256 revokedAt);
    event IssuerAdded(address indexed issuer);
    event IssuerRemoved(address indexed issuer);

    modifier onlyOwner() {
        require(msg.sender == owner, "Not owner");
        _;
    }

    modifier onlyAuthorized() {
        require(authorizedIssuers[msg.sender], "Not authorized issuer");
        _;
    }

    constructor() {
        owner = msg.sender;
        authorizedIssuers[msg.sender] = true;
        emit IssuerAdded(msg.sender);
    }

    function registerDocument(
        string  calldata trackingCode,
        bytes32          documentHash,
        string  calldata documentType,
        string  calldata department,
        uint256          issuedAt
    ) external onlyAuthorized {
        require(
            _documents[trackingCode].issuedAt == 0,
            "Document already registered"
        );
        _documents[trackingCode] = DocumentRecord({
            documentHash: documentHash,
            issuedBy:     msg.sender,
            issuedAt:     issuedAt,
            documentType: documentType,
            department:   department,
            isRevoked:    false
        });
        emit DocumentRegistered(trackingCode, documentHash, msg.sender, issuedAt);
    }

    function revokeDocument(string calldata trackingCode) external onlyAuthorized {
        require(_documents[trackingCode].issuedAt != 0, "Document not found");
        _documents[trackingCode].isRevoked = true;
        emit DocumentRevoked(trackingCode, block.timestamp);
    }

    function verifyDocument(
        string  calldata trackingCode,
        bytes32          claimedHash
    ) external view returns (
        bool    isAuthentic,
        bool    isRevoked,
        uint256 issuedAt,
        address issuedBy
    ) {
        DocumentRecord memory doc = _documents[trackingCode];
        if (doc.issuedAt == 0) {
            return (false, false, 0, address(0));
        }
        return (
            doc.documentHash == claimedHash && !doc.isRevoked,
            doc.isRevoked,
            doc.issuedAt,
            doc.issuedBy
        );
    }

    function getDocument(string calldata trackingCode)
        external view returns (DocumentRecord memory)
    {
        return _documents[trackingCode];
    }

    function isRegistered(string calldata trackingCode) external view returns (bool) {
        return _documents[trackingCode].issuedAt != 0;
    }

    function addIssuer(address issuer) external onlyOwner {
        authorizedIssuers[issuer] = true;
        emit IssuerAdded(issuer);
    }

    function removeIssuer(address issuer) external onlyOwner {
        authorizedIssuers[issuer] = false;
        emit IssuerRemoved(issuer);
    }

    function transferOwnership(address newOwner) external onlyOwner {
        require(newOwner != address(0), "Zero address");
        owner = newOwner;
    }
}