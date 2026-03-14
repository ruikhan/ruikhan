<script setup>
import { Head } from '@inertiajs/vue3';
import QRCode from 'qrcode';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    docRequest: Object,
    user:        Object,
    barangayProfile: Object, // from BarangayProfile model
});

// ── QR Code ───────────────────────────────────────────────────────
const qrDataUrl    = ref('');
const verifyUrl    = `${window.location.origin}/verify?code=${props.docRequest?.tracking_code}`;

const generateQr = async () => {
    try {
        qrDataUrl.value = await QRCode.toDataURL(verifyUrl, {
            width: 130, margin: 1,
            color: { dark: '#0a0a0a', light: '#ffffff' },
            errorCorrectionLevel: 'H',
        });
    } catch (e) { console.error('QR gen failed', e); }
};

// ── Derived data ──────────────────────────────────────────────────
const data = computed(() => props.docRequest?.data ?? {});

const fullName = computed(() => {
    const d = data.value;
    const parts = [
        d.applicant_first_name,
        d.applicant_middle_name ? d.applicant_middle_name + '.' : '',
        d.applicant_last_name,
    ].filter(Boolean);
    return parts.length ? parts.join(' ') : (props.user?.name ?? 'N/A');
});

const address = computed(() => {
    const d = data.value;
    return [d.purok_street, d.barangay, d.municipality, d.province]
        .filter(Boolean).join(', ') || props.user?.address || 'N/A';
});

const issuedDate = computed(() => {
    const d = props.docRequest?.admin_signature_date ?? props.docRequest?.updated_at;
    if (!d) return new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
    return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
});

const anchoredDate = computed(() => {
    const d = props.docRequest?.blockchain_anchored_at;
    if (!d) return null;
    return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
});

const shortTxHash = computed(() => {
    const h = props.docRequest?.blockchain_tx_hash;
    if (!h) return null;
    return h.slice(0, 12) + '...' + h.slice(-8);
});

// ── Document body content per type ───────────────────────────────
const documentBody = computed(() => {
    const d   = data.value;
    const name = fullName.value;
    const addr = address.value;
    const dept = props.docRequest?.document_type ?? '';

    const purpose   = d.purpose ?? 'whatever purpose it may serve';
    const civil     = d.civil_status ?? '';
    const age       = d.age ?? '';
    const sex       = d.sex ?? '';
    const dob       = d.date_of_birth ?? '';
    const residency = d.residency_duration ?? '';
    const standing  = d.community_standing ?? 'Good Standing, Law-Abiding Citizen';

    const templates = {
        'Barangay Clearance': `
            This is to certify that <strong>${name}</strong>, ${age} years old, ${civil.toLowerCase()},
            a resident of <strong>${addr}</strong>, is personally known to this office and has been a
            bona fide resident of this barangay for <strong>${residency}</strong>.
            <br/><br/>
            This further certifies that the above-named individual is of <strong>${standing}</strong>
            in the community, with no derogatory record filed in this office.
            <br/><br/>
            This Barangay Clearance is issued upon the request of the above-named person for
            <strong>${purpose}</strong> and for whatever legal purpose it may serve.
        `,
        'Certificate of Residency': `
            This is to certify that <strong>${name}</strong>, ${sex ? sex + ',' : ''} ${age} years old,
            born on ${dob}, is a bona fide resident of <strong>${addr}</strong>.
            <br/><br/>
            The above-named individual has been residing in this barangay for <strong>${residency}</strong>
            and is known to be a law-abiding and peace-loving member of the community.
            <br/><br/>
            This Certificate of Residency is issued upon the request of the afore-named individual for
            <strong>${purpose}</strong> purposes and for whatever legal intent it may serve.
        `,
        'Certificate of Indigency': `
            This is to certify that <strong>${name}</strong>, ${age} years old, a resident of
            <strong>${addr}</strong>, belongs to an indigent family in this barangay.
            <br/><br/>
            Based on the records of this office, the above-named individual qualifies for government
            assistance and social protection programs due to limited financial capacity.
            <br/><br/>
            This Certificate of Indigency is issued upon request for <strong>${purpose}</strong>
            and for whatever legal purpose it may serve.
        `,
        'Certificate of Good Moral Character': `
            This is to certify that <strong>${name}</strong>, ${age} years old, ${civil.toLowerCase()},
            a resident of <strong>${addr}</strong>, is personally known to us.
            <br/><br/>
            Based on records available in this office and to the best of our knowledge, the above-named
            person bears a good moral character, is a ${standing}, and has not been involved
            in any criminal or unethical activities within this barangay.
            <br/><br/>
            This certification is issued upon the request of the above-named individual for
            <strong>${purpose}</strong> purposes.
        `,
        'Business Clearance': `
            This is to certify that <strong>${d.business_owner ?? name}</strong>, owner/operator of
            <strong>${d.business_name ?? 'the business'}</strong>, located at
            <strong>${d.business_address ?? addr}</strong>, with nature of business as
            <strong>${d.business_nature ?? 'N/A'}</strong>, has been granted Barangay Clearance
            for business operation.
            <br/><br/>
            The proprietor is a known resident of this barangay and has complied with the
            requirements of this office for the issuance of this clearance.
            <br/><br/>
            This Business Clearance is issued for <strong>${purpose}</strong> purposes.
        `,
    };

    // Default fallback for any unlisted document type
    return templates[dept] ?? `
        This is to certify that <strong>${name}</strong>, a resident of <strong>${addr}</strong>,
        has requested and is hereby granted this <strong>${dept}</strong> for
        <strong>${purpose}</strong> purposes.
        <br/><br/>
        This certification is issued upon the request of the above-named individual and for
        whatever legal purpose it may serve.
    `;
});

// ── Print lifecycle ───────────────────────────────────────────────
onMounted(async () => {
    await generateQr();
    // Small delay so QR renders before print dialog
    setTimeout(() => window.print(), 800);
});
</script>

<template>
    <Head :title="`${docRequest?.document_type} — ${docRequest?.tracking_code}`" />

    <!-- ══════════════════════════════════════════════════════
         PRINT DOCUMENT — renders to paper, no nav/layout
         ══════════════════════════════════════════════════════ -->
    <div class="print-root">

        <!-- ── Watermark (screen only, hidden on print) ───── -->
        <div class="screen-only watermark">
            <div class="watermark-inner">PREVIEW</div>
        </div>

        <!-- ── Print toolbar (screen only) ───────────────────── -->
        <div class="screen-only toolbar">
            <div class="toolbar-inner">
                <div class="toolbar-left">
                    <span class="toolbar-title">📄 Document Preview</span>
                    <span class="toolbar-code">{{ docRequest?.tracking_code }}</span>
                </div>
                <div class="toolbar-right">
                    <button onclick="window.print()" class="btn-print">🖨 Print / Save PDF</button>
                    <button onclick="window.close()" class="btn-close">✕ Close</button>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════
             PAPER PAGE — everything inside prints
             ══════════════════════════════════════════ -->
        <div class="paper">

            <!-- ── Decorative top bar ──────────────────────── -->
            <div class="top-bar"></div>

            <!-- ── Official Header ─────────────────────────── -->
            <header class="doc-header">
                <!-- Left seal placeholder -->
                <div class="seal seal-left">
                    <div class="seal-ring">
                        <div class="seal-inner">
                            <div class="seal-text-top">REPUBLIC OF THE</div>
                            <div class="seal-star">✦</div>
                            <div class="seal-text-bot">PHILIPPINES</div>
                        </div>
                    </div>
                </div>

                <!-- Center text -->
                <div class="header-center">
                    <p class="header-republic">Republic of the Philippines</p>
                    <p class="header-province">Province of Camarines Sur</p>
                    <p class="header-municipality">Municipality of Pili</p>
                    <div class="header-divider"></div>
                    <p class="header-office">{{ docRequest?.department }}</p>
                    <h1 class="header-doctype">{{ docRequest?.document_type }}</h1>
                </div>

                <!-- Right seal / LGU logo -->
                <div class="seal seal-right">
                    <div class="seal-ring">
                        <div class="seal-inner">
                            <div class="seal-text-top">E-PILI</div>
                            <div class="seal-star">⛓</div>
                            <div class="seal-text-bot">DIGITAL GOV</div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ── Document control strip ─────────────────── -->
            <div class="control-strip">
                <div class="control-item">
                    <span class="control-label">Tracking No.</span>
                    <span class="control-value">{{ docRequest?.tracking_code }}</span>
                </div>
                <div class="control-sep"></div>
                <div class="control-item">
                    <span class="control-label">Date Issued</span>
                    <span class="control-value">{{ issuedDate }}</span>
                </div>
                <div class="control-sep"></div>
                <div class="control-item">
                    <span class="control-label">Doc. Status</span>
                    <span class="control-value status-completed">✓ COMPLETED</span>
                </div>
            </div>

            <!-- ── TO WHOM IT MAY CONCERN ──────────────────── -->
            <section class="doc-body">
                <p class="salutation">TO WHOM IT MAY CONCERN:</p>

                <div class="body-text" v-html="documentBody"></div>

                <!-- Additional applicant details table -->
                <div class="details-grid" v-if="data">
                    <div class="details-row" v-if="data.civil_status">
                        <span class="details-label">Civil Status</span>
                        <span class="details-value">{{ data.civil_status }}</span>
                    </div>
                    <div class="details-row" v-if="data.date_of_birth">
                        <span class="details-label">Date of Birth</span>
                        <span class="details-value">{{ data.date_of_birth }}</span>
                    </div>
                    <div class="details-row" v-if="data.age">
                        <span class="details-label">Age</span>
                        <span class="details-value">{{ data.age }} years old</span>
                    </div>
                    <div class="details-row" v-if="data.cedula_number">
                        <span class="details-label">CTC / Cedula No.</span>
                        <span class="details-value">{{ data.cedula_number }}</span>
                    </div>
                    <div class="details-row" v-if="data.valid_id_type">
                        <span class="details-label">Valid ID Presented</span>
                        <span class="details-value">{{ data.valid_id_type }}</span>
                    </div>
                    <div class="details-row" v-if="data.purpose">
                        <span class="details-label">Purpose</span>
                        <span class="details-value">{{ data.purpose }}</span>
                    </div>
                </div>
            </section>

            <!-- ── Signature Block ─────────────────────────── -->
            <section class="sig-section">
                <!-- Applicant signature (left) -->
                <div class="sig-box">
                    <p class="sig-label">APPLICANT'S SIGNATURE</p>
                    <div class="sig-area">
                        <img v-if="data?.signature" :src="data.signature" class="sig-img" alt="Applicant signature" />
                        <div v-else class="sig-blank"></div>
                    </div>
                    <div class="sig-line"></div>
                    <p class="sig-name">{{ fullName }}</p>
                    <p class="sig-title">Applicant</p>
                </div>

                <!-- Spacer -->
                <div class="sig-spacer"></div>

                <!-- Official signature (right) -->
                <div class="sig-box sig-official">
                    <p class="sig-label">AUTHORIZED SIGNATORY</p>
                    <div class="sig-area">
                        <img
                            v-if="docRequest?.admin_signature"
                            :src="docRequest.admin_signature"
                            class="sig-img"
                            alt="Official signature"
                        />
                        <div v-else class="sig-blank"></div>
                    </div>
                    <div class="sig-line"></div>
                    <p class="sig-name">BARANGAY OFFICIAL</p>
                    <p class="sig-title">{{ docRequest?.department }}</p>
                    <p class="sig-date" v-if="docRequest?.admin_signature_date">
                        Signed: {{ issuedDate }}
                    </p>
                </div>
            </section>

            <!-- ── Blockchain + QR Footer ──────────────────── -->
            <footer class="doc-footer">
                <!-- QR code -->
                <div class="footer-qr">
                    <img v-if="qrDataUrl" :src="qrDataUrl" class="qr-img" alt="Verification QR" />
                    <div v-else class="qr-placeholder">QR</div>
                    <p class="qr-caption">Scan to verify</p>
                </div>

                <!-- Blockchain info -->
                <div class="footer-blockchain">
                    <div class="blockchain-badge">
                        <span class="blockchain-icon">⛓</span>
                        <span class="blockchain-label">BLOCKCHAIN VERIFIED</span>
                    </div>
                    <div class="blockchain-details">
                        <div class="bc-row" v-if="shortTxHash">
                            <span class="bc-key">TX Hash</span>
                            <span class="bc-val mono">{{ shortTxHash }}</span>
                        </div>
                        <div class="bc-row" v-if="docRequest?.blockchain_network">
                            <span class="bc-key">Network</span>
                            <span class="bc-val">
                                {{ docRequest.blockchain_network === 'polygon' ? 'Polygon Mainnet' : 'Polygon Amoy Testnet' }}
                            </span>
                        </div>
                        <div class="bc-row" v-if="anchoredDate">
                            <span class="bc-key">Anchored</span>
                            <span class="bc-val">{{ anchoredDate }}</span>
                        </div>
                        <div class="bc-row">
                            <span class="bc-key">Verify URL</span>
                            <span class="bc-val mono small">{{ verifyUrl }}</span>
                        </div>
                    </div>
                </div>

                <!-- Validity notice -->
                <div class="footer-notice">
                    <p class="notice-title">⚠ IMPORTANT NOTICE</p>
                    <p class="notice-text">
                        This document is valid only with the official signature and dry seal of the
                        issuing authority. Alterations or erasures render this document null and void.
                        Verify authenticity by scanning the QR code or visiting the verification URL above.
                    </p>
                    <p class="notice-text" style="margin-top: 4px;">
                        Issued by: E-PILI Digital Governance System &nbsp;|&nbsp; Anchored on Polygon Blockchain
                    </p>
                </div>
            </footer>

            <!-- ── Bottom bar ──────────────────────────────── -->
            <div class="bottom-bar">
                <span>{{ docRequest?.tracking_code }}</span>
                <span>{{ docRequest?.document_type }}</span>
                <span>Issued {{ issuedDate }}</span>
            </div>

        </div><!-- /paper -->
    </div><!-- /print-root -->
</template>

<style>
/* ════════════════════════════════════════════════════
   SCREEN WRAPPER
   ════════════════════════════════════════════════════ */
* { box-sizing: border-box; margin: 0; padding: 0; }

.print-root {
    font-family: 'Georgia', 'Times New Roman', serif;
    background: #e8e8e8;
    min-height: 100vh;
    padding: 60px 20px 40px;
}

/* ── Screen-only toolbar ────────────────────────── */
.screen-only { display: block; }

.toolbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
    background: #1e293b;
    border-bottom: 1px solid #334155;
    height: 52px;
}
.toolbar-inner {
    max-width: 900px;
    margin: 0 auto;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
}
.toolbar-left { display: flex; align-items: center; gap: 12px; }
.toolbar-title { font-family: 'Helvetica Neue', sans-serif; font-size: 13px; font-weight: 600; color: #e2e8f0; }
.toolbar-code { font-family: monospace; font-size: 11px; color: #60a5fa; background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); padding: 2px 8px; border-radius: 4px; }
.toolbar-right { display: flex; gap: 8px; }
.btn-print {
    padding: 7px 16px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 8px;
    font-family: 'Helvetica Neue', sans-serif;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-print:hover { background: #1d4ed8; }
.btn-close {
    padding: 7px 12px;
    background: rgba(255,255,255,0.08);
    color: #94a3b8;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    font-family: 'Helvetica Neue', sans-serif;
    font-size: 12px;
    cursor: pointer;
}

/* Watermark preview label */
.watermark {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    pointer-events: none;
    z-index: 1;
}
.watermark-inner {
    font-size: 120px;
    font-weight: 900;
    color: rgba(0,0,0,0.04);
    letter-spacing: 0.2em;
    white-space: nowrap;
    font-family: 'Helvetica Neue', sans-serif;
}

/* ════════════════════════════════════════════════════
   PAPER PAGE
   ════════════════════════════════════════════════════ */
.paper {
    max-width: 816px; /* US Letter width */
    min-height: 1056px;
    margin: 0 auto;
    background: #ffffff;
    box-shadow: 0 8px 40px rgba(0,0,0,0.25);
    position: relative;
    overflow: hidden;
}

/* ── Top & Bottom decorative bars ───────────────── */
.top-bar {
    height: 8px;
    background: linear-gradient(to right, #1e3a5f, #2563eb, #1e3a5f);
}
.bottom-bar {
    height: 28px;
    background: linear-gradient(to right, #1e3a5f, #2563eb, #1e3a5f);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    font-family: 'Courier New', monospace;
    font-size: 9px;
    color: rgba(255,255,255,0.8);
    letter-spacing: 0.05em;
}

/* ── Header ─────────────────────────────────────── */
.doc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 32px 16px;
    border-bottom: 2px solid #1e3a5f;
    gap: 16px;
}

.header-center {
    text-align: center;
    flex: 1;
}
.header-republic  { font-size: 11px; color: #374151; letter-spacing: 0.05em; text-transform: uppercase; }
.header-province  { font-size: 11px; color: #374151; }
.header-municipality { font-size: 13px; font-weight: 700; color: #1e3a5f; }
.header-divider   { width: 60px; height: 1.5px; background: #1e3a5f; margin: 6px auto; }
.header-office    { font-size: 11px; color: #4b5563; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px; }
.header-doctype   { font-size: 20px; font-weight: 700; color: #1e3a5f; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1.2; }

/* ── Seals ──────────────────────────────────────── */
.seal { flex-shrink: 0; }
.seal-ring {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    border: 3px solid #1e3a5f;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.seal-ring::after {
    content: '';
    position: absolute;
    inset: 4px;
    border-radius: 50%;
    border: 1.5px dashed #2563eb;
}
.seal-inner {
    text-align: center;
    z-index: 1;
}
.seal-text-top, .seal-text-bot {
    font-family: 'Helvetica Neue', sans-serif;
    font-size: 6px;
    font-weight: 700;
    color: #1e3a5f;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    line-height: 1.4;
}
.seal-star { font-size: 14px; color: #2563eb; margin: 2px 0; }

/* ── Control strip ──────────────────────────────── */
.control-strip {
    display: flex;
    align-items: center;
    background: #f1f5f9;
    border-bottom: 1px solid #cbd5e1;
    padding: 8px 32px;
    gap: 0;
}
.control-item {
    display: flex;
    flex-direction: column;
    flex: 1;
    padding: 0 16px;
}
.control-item:first-child { padding-left: 0; }
.control-item:last-child  { padding-right: 0; }
.control-label {
    font-family: 'Helvetica Neue', sans-serif;
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #64748b;
    margin-bottom: 2px;
}
.control-value {
    font-family: 'Courier New', monospace;
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
}
.status-completed { color: #059669; }
.control-sep { width: 1px; background: #cbd5e1; height: 32px; }

/* ── Body ───────────────────────────────────────── */
.doc-body {
    padding: 28px 40px 16px;
}
.salutation {
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 16px;
}
.body-text {
    font-size: 13px;
    line-height: 1.85;
    color: #1e293b;
    text-align: justify;
    text-indent: 2em;
    margin-bottom: 20px;
}
.body-text strong { color: #1e3a5f; }

/* Applicant details mini-table */
.details-grid {
    margin: 16px 0;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}
.details-row {
    display: flex;
    border-bottom: 1px solid #f1f5f9;
}
.details-row:last-child { border-bottom: none; }
.details-label {
    font-family: 'Helvetica Neue', sans-serif;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    padding: 6px 12px;
    width: 40%;
    background: #f8fafc;
    border-right: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
}
.details-value {
    font-size: 11px;
    color: #1e293b;
    padding: 6px 12px;
    display: flex;
    align-items: center;
}

/* ── Signature section ──────────────────────────── */
.sig-section {
    display: flex;
    padding: 24px 40px;
    border-top: 1px solid #e2e8f0;
    gap: 32px;
}
.sig-spacer { flex: 1; }
.sig-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 180px;
}
.sig-official { min-width: 220px; }
.sig-label {
    font-family: 'Helvetica Neue', sans-serif;
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #64748b;
    margin-bottom: 8px;
}
.sig-area {
    height: 70px;
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    margin-bottom: 4px;
}
.sig-img {
    max-height: 70px;
    max-width: 200px;
    object-fit: contain;
    filter: contrast(1.2);
}
.sig-blank { width: 100%; height: 100%; }
.sig-line {
    width: 100%;
    height: 1px;
    background: #1e293b;
    margin-bottom: 4px;
}
.sig-name {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.sig-title {
    font-size: 10px;
    color: #64748b;
    text-align: center;
    margin-top: 2px;
}
.sig-date {
    font-size: 9px;
    color: #94a3b8;
    text-align: center;
    margin-top: 2px;
    font-style: italic;
}

/* ── Footer with QR + Blockchain ────────────────── */
.doc-footer {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px 32px;
    background: #f8fafc;
    border-top: 2px solid #1e3a5f;
}
.footer-qr {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}
.qr-img {
    width: 90px;
    height: 90px;
    border: 2px solid #1e3a5f;
    padding: 3px;
    background: white;
}
.qr-placeholder {
    width: 90px;
    height: 90px;
    border: 2px dashed #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #94a3b8;
}
.qr-caption {
    font-size: 8px;
    color: #64748b;
    text-align: center;
    margin-top: 4px;
    font-family: 'Helvetica Neue', sans-serif;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.footer-blockchain {
    flex: 1;
}
.blockchain-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    background: #1e3a5f;
    border-radius: 4px;
    margin-bottom: 8px;
}
.blockchain-icon { font-size: 10px; color: #60a5fa; }
.blockchain-label {
    font-family: 'Helvetica Neue', sans-serif;
    font-size: 8px;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}
.blockchain-details { display: flex; flex-direction: column; gap: 3px; }
.bc-row {
    display: flex;
    align-items: baseline;
    gap: 8px;
    font-size: 9px;
}
.bc-key {
    font-family: 'Helvetica Neue', sans-serif;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    min-width: 60px;
    font-size: 8px;
}
.bc-val { color: #1e293b; }
.mono    { font-family: 'Courier New', monospace; }
.small   { font-size: 8px; word-break: break-all; }

.footer-notice {
    flex-shrink: 0;
    width: 230px;
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background: #fff;
}
.notice-title {
    font-family: 'Helvetica Neue', sans-serif;
    font-size: 8px;
    font-weight: 700;
    color: #b45309;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 5px;
}
.notice-text {
    font-size: 8px;
    line-height: 1.6;
    color: #64748b;
    text-align: justify;
}

/* ════════════════════════════════════════════════════
   PRINT MEDIA — clean output to paper
   ════════════════════════════════════════════════════ */
@media print {
    @page {
        size: Letter;
        margin: 0;
    }

    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

    .screen-only { display: none !important; }
    .watermark   { display: none !important; }

    .print-root {
        background: white;
        padding: 0;
    }

    .paper {
        max-width: 100%;
        min-height: 100vh;
        box-shadow: none;
        margin: 0;
    }
}
</style>