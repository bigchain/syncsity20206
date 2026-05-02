<?php
/**
 * Solutions / Voice Operations — full marketing page rebuilt from
 * SPA bundle data + design system. First of the SPA-route migrations.
 */

$page_path_prefix = '../';
$page_title       = 'AI Voice Agents | 24/7 Customer Service Automation | Syncsity';
$page_description = 'Deploy AI voice agents that handle unlimited conversations 24/7 in 40+ languages with 90% cost savings. Replace your call center with intelligent voice automation.';
$page_canonical   = 'https://syncsity.com/solutions/voice-solutions';
$page_breadcrumb  = [
    ['Home',      'https://syncsity.com/'],
    ['Solutions', 'https://syncsity.com/solutions'],
    ['AI Voice Operations', 'https://syncsity.com/solutions/voice-solutions'],
];

$page_extra_jsonld = <<<'JSONLD'

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "@id": "https://syncsity.com/solutions/voice-solutions#service",
  "name": "AI Voice Operations",
  "alternateName": "AI Voice Agents",
  "serviceType": "AI Voice Automation",
  "category": "Customer Service Automation",
  "description": "Voice agents that handle unlimited conversations 24/7 in 40+ languages with 90% cost savings. Replace your call center with intelligent voice automation that sounds more human than humans.",
  "provider": { "@id": "https://syncsity.com/#organization" },
  "areaServed": ["GB","US","AE","CA","AU"],
  "audience": { "@type": "BusinessAudience", "audienceType": "Enterprise" },
  "offers": {
    "@type": "Offer",
    "availability": "https://schema.org/InStock",
    "url": "https://syncsity.com/solutions/voice-solutions"
  },
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "AI Voice Capabilities",
    "itemListElement": [
      { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Customer Service Automation" } },
      { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Sales & Appointment Booking" } },
      { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Lead Qualification" } },
      { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Returns & Refunds Handling" } },
      { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Voice of Customer Programs" } }
    ]
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "How do your AI Voice Agents differ from traditional IVR systems?",
      "acceptedAnswer": { "@type": "Answer", "text": "Unlike IVR systems that use rigid menus and limited pathways, our AI Voice Agents engage in natural conversations. They understand context, remember details across the conversation, respond to interruptions, and can handle complex, multi-step interactions just like a human agent would." }
    },
    {
      "@type": "Question",
      "name": "How quickly can you deploy an AI Voice Agent for my business?",
      "acceptedAnswer": { "@type": "Answer", "text": "We can deploy AI Voice Agents in as little as three weeks from kickoff to go-live. The exact timeline depends on the complexity of the use case, availability of training materials, and required integrations. Our implementation process is designed to get you up and running with minimal delay." }
    },
    {
      "@type": "Question",
      "name": "What systems and platforms can your Voice Agents integrate with?",
      "acceptedAnswer": { "@type": "Answer", "text": "Our AI Voice Agents integrate with all major CRM platforms (Salesforce, HubSpot, etc.), ticketing systems (Zendesk, ServiceNow), scheduling tools (Calendly, Acuity), payment processors, and custom databases. We offer pre-built connectors for common systems and can develop custom integrations for proprietary platforms." }
    },
    {
      "@type": "Question",
      "name": "Is my data secure with your Voice Agents?",
      "acceptedAnswer": { "@type": "Answer", "text": "Absolutely. We maintain rigorous security standards including SOC 2 Type II compliance, end-to-end encryption, and regular penetration testing. We can deploy in your private cloud or on-premises for sensitive industries. All data processing complies with GDPR, CCPA, and industry-specific regulations such as HIPAA for healthcare or PCI DSS for payment processing." }
    },
    {
      "@type": "Question",
      "name": "Will AI Voice Agents replace human agents entirely?",
      "acceptedAnswer": { "@type": "Answer", "text": "Our AI Voice Agents handle most inquiries independently, but they're also designed to recognize when human intervention would be valuable. In these cases, they can seamlessly transfer the call to a human agent, providing a complete summary of the conversation and issues discussed so far, ensuring continuity and preventing customers from having to repeat information." }
    }
  ]
}
</script>
JSONLD;

include __DIR__ . '/../partials/site-head.php';
?>

<style>
/* ── Voice Operations page ─────────────────────────────────────────── */
:root { --voice-glow: rgba(51,133,223,0.20); }

/* Hero */
.voice-hero {
  position: relative; overflow: hidden;
  padding: 80px 0 96px; isolation: isolate;
  background:
    radial-gradient(circle at 20% 20%, var(--voice-glow), transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(252,163,17,0.08), transparent 50%),
    linear-gradient(180deg, #050814 0%, #0a1022 100%);
}
.voice-hero::before {
  content: ''; position: absolute; inset: 0; z-index: -1; opacity: 0.4;
  background:
    repeating-linear-gradient(0deg,  rgba(255,255,255,0.025) 0 1px, transparent 1px 96px),
    repeating-linear-gradient(90deg, rgba(255,255,255,0.025) 0 1px, transparent 1px 96px);
  mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
  -webkit-mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
}
.voice-hero__inner { max-width: 1100px; margin: 0 auto; padding: 0 24px; text-align: center; }
.voice-hero__eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 6px 14px;
  background: rgba(51,133,223,0.10); color: var(--blue-300);
  border: 1px solid rgba(51,133,223,0.24);
  border-radius: 9999px; font-size: 12px; font-weight: 600;
  letter-spacing: 0.06em; text-transform: uppercase;
}
.voice-hero h1 {
  font-size: clamp(36px, 5.4vw, 64px);
  font-weight: 700; line-height: 1.05; letter-spacing: -0.02em;
  color: #fff; margin: 20px auto 16px; max-width: 980px;
}
.voice-hero h1 .accent { color: var(--blue-400); display: block; }
.voice-hero__lead {
  color: rgba(229,231,235,0.88); font-size: 18px; line-height: 1.55;
  max-width: 780px; margin: 0 auto 32px;
}
.voice-hero__ctas { display: flex; gap: 14px; flex-wrap: wrap; justify-content: center; }
.voice-hero__metrics {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
  max-width: 920px; margin: 56px auto 0;
}
@media (max-width: 720px) { .voice-hero__metrics { grid-template-columns: repeat(2, 1fr); } }
.voice-metric {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px; padding: 20px;
  text-align: center;
}
.voice-metric__value { font-size: 28px; font-weight: 700; color: #fff; line-height: 1; margin-bottom: 6px; }
.voice-metric__value .unit { font-size: 18px; color: var(--blue-300); }
.voice-metric__label { font-size: 13px; color: var(--text-muted); }

/* Section heading shared */
.section-head { text-align: center; margin-bottom: 56px; }
.section-head__eyebrow {
  display: inline-block; padding: 4px 12px;
  background: rgba(51,133,223,0.14); color: var(--blue-300);
  border-radius: 9999px; font-size: 12px; font-weight: 600;
  letter-spacing: 0.06em; text-transform: uppercase;
}
.section-head h2 {
  font-size: clamp(28px, 3.6vw, 42px);
  font-weight: 700; color: #fff; letter-spacing: -0.02em;
  margin: 16px 0 12px; line-height: 1.15;
}
.section-head p { color: var(--text-muted); font-size: 17px; max-width: 720px; margin: 0 auto; line-height: 1.55; }

/* Capabilities grid */
.voice-capabilities { padding: 96px 0; background: #0a1022; }
.voice-capabilities__container { max-width: 1180px; margin: 0 auto; padding: 0 24px; }
.voice-cap__grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
}
@media (max-width: 980px) { .voice-cap__grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 540px) { .voice-cap__grid { grid-template-columns: 1fr; } }
.voice-cap {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 14px; padding: 28px;
  transition: transform 220ms ease, border-color 220ms ease, background 220ms ease;
}
.voice-cap:hover { transform: translateY(-3px); border-color: rgba(51,133,223,0.30); background: rgba(51,133,223,0.04); }
.voice-cap__icon {
  width: 44px; height: 44px; margin-bottom: 18px;
  display: inline-flex; align-items: center; justify-content: center;
  border-radius: 12px;
  background: linear-gradient(135deg, rgba(51,133,223,0.18), rgba(51,133,223,0.08));
  color: var(--blue-300);
}
.voice-cap__icon svg { width: 22px; height: 22px; }
.voice-cap h3 { color: #fff; font-size: 18px; font-weight: 700; margin: 0 0 10px; }
.voice-cap p { color: var(--text-muted); font-size: 14.5px; line-height: 1.55; margin: 0; }

/* Features section */
.voice-features { padding: 96px 0; background: linear-gradient(180deg, #0a1022 0%, #050814 100%); }
.voice-features__container { max-width: 1180px; margin: 0 auto; padding: 0 24px; }
.voice-feat__grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px;
}
@media (max-width: 980px) { .voice-feat__grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 540px) { .voice-feat__grid { grid-template-columns: 1fr; } }
.voice-feat {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px; padding: 24px;
  transition: transform 200ms ease, box-shadow 200ms ease;
}
.voice-feat:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.30); }
.voice-feat svg { width: 22px; height: 22px; color: var(--blue-300); margin-bottom: 14px; }
.voice-feat h4 { color: #fff; font-size: 15px; font-weight: 700; margin: 0 0 6px; }
.voice-feat p  { color: var(--text-muted); font-size: 13.5px; line-height: 1.5; margin: 0; }

/* Pricing Plans — light section */
.voice-pricing {
  padding: 96px 0;
  background: linear-gradient(180deg, #f5f7fa 0%, #fff 100%);
}
.voice-pricing__container { max-width: 1180px; margin: 0 auto; padding: 0 24px; }
.voice-pricing__head { text-align: center; margin-bottom: 56px; }
.voice-pricing__head h2 {
  font-size: clamp(28px, 3.6vw, 42px); font-weight: 700;
  margin: 0 0 12px; letter-spacing: -0.02em;
  background: linear-gradient(90deg, #14213D 0%, #0066D7 100%);
  -webkit-background-clip: text; background-clip: text;
  -webkit-text-fill-color: transparent;
}
.voice-pricing__head p { color: #475569; font-size: 16px; max-width: 640px; margin: 0 auto; }
.pricing-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
  align-items: stretch; max-width: 1100px; margin: 0 auto;
}
@media (max-width: 880px) { .pricing-grid { grid-template-columns: 1fr; max-width: 480px; } }
.pricing-tier {
  background: #fff; border: 1px solid #eef2f7;
  border-radius: 16px; padding: 32px;
  display: flex; flex-direction: column;
  position: relative;
  transition: transform 220ms ease, box-shadow 220ms ease;
}
.pricing-tier:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(20,33,61,0.10); }
.pricing-tier--popular {
  border-color: #14213D;
  box-shadow: 0 18px 40px rgba(20,33,61,0.12);
}
.pricing-tier__badge {
  position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
  background: #14213D; color: #fff;
  padding: 6px 16px; border-radius: 9999px;
  font-size: 12px; font-weight: 700; letter-spacing: 0.04em;
}
.pricing-tier h3 { color: #14213D; font-size: 22px; font-weight: 700; margin: 0 0 6px; }
.pricing-tier__intro { color: #475569; font-size: 14px; margin: 0 0 22px; line-height: 1.5; min-height: 42px; }
.pricing-tier__fee-label { color: #64748b; font-size: 13px; margin: 0 0 4px; }
.pricing-tier__price { font-size: 36px; font-weight: 700; color: #14213D; line-height: 1; margin: 0; letter-spacing: -0.02em; }
.pricing-tier__when { color: #64748b; font-size: 12px; margin: 4px 0 22px; }
.pricing-tier__incl-label { color: #64748b; font-size: 13px; margin: 0 0 4px; }
.pricing-tier__calls { color: #14213D; font-size: 16px; font-weight: 600; margin: 0 0 4px; }
.pricing-tier__per-call { color: #64748b; font-size: 12px; margin: 0 0 22px; }
.pricing-tier ul { list-style: none; padding: 0; margin: 0 0 28px; flex: 1; }
.pricing-tier li {
  font-size: 14px; color: #334155; line-height: 1.5;
  padding: 8px 0 8px 26px; position: relative;
}
.pricing-tier li::before {
  content: ''; position: absolute; left: 0; top: 12px;
  width: 16px; height: 16px; border-radius: 50%;
  background: #36B37E url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'><polyline points='20 6 9 17 4 12'/></svg>") center/10px no-repeat;
}
.pricing-tier__cta {
  display: inline-flex; align-items: center; justify-content: center;
  width: 100%; padding: 12px 20px;
  background: #fff; border: 1px solid #e5e7eb;
  border-radius: 9999px;
  font-weight: 600; font-size: 14px; color: #14213D;
  text-decoration: none;
  transition: background 150ms ease, border-color 150ms ease;
}
.pricing-tier__cta:hover { background: #f5f7fa; border-color: #cbd5e1; }
.pricing-tier--popular .pricing-tier__cta { background: #14213D; color: #fff; border-color: #14213D; }
.pricing-tier--popular .pricing-tier__cta:hover { background: #1a2c52; }
.pricing-extras {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  max-width: 760px; margin: 36px auto 0;
}
@media (max-width: 720px) { .pricing-extras { grid-template-columns: 1fr; } }
.pricing-extras__item {
  background: #ecfdf5; border: 1px solid #d1fae5;
  border-radius: 10px; padding: 12px 14px;
  font-size: 13.5px; color: #065f46; line-height: 1.5;
}
.pricing-extras__item strong { color: #047857; }
.pricing-guarantee {
  background: #f1f5f9; border: 1px solid #e2e8f0;
  border-radius: 10px; padding: 12px 16px;
  font-size: 13.5px; color: #334155; line-height: 1.5;
  max-width: 760px; margin: 12px auto 0; text-align: center;
}
.pricing-guarantee strong { color: #14213D; }
.pricing-cta-row {
  display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;
  margin-top: 32px;
}
.pricing-cta-row .btn--ghost-light {
  background: #fff; border: 1px solid #e5e7eb;
  color: #14213D; padding: 12px 22px; border-radius: 9999px;
  font-weight: 600; font-size: 14px; text-decoration: none;
}
.pricing-cta-row .btn--ghost-light:hover { background: #f5f7fa; }
.pricing-cta-row .btn--text {
  background: transparent; color: #14213D;
  padding: 12px 22px;
  font-weight: 600; font-size: 14px; text-decoration: none;
}

/* Implementation Process — light timeline section */
.voice-impl {
  padding: 96px 0;
  background: #f1f5f9;
  border-top: 1px solid #e5e7eb;
}
.voice-impl__container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
.voice-impl__head { text-align: center; margin-bottom: 56px; }
.voice-impl__head h2 {
  font-size: clamp(28px, 3.6vw, 42px); font-weight: 700;
  margin: 0 0 12px; letter-spacing: -0.02em;
  background: linear-gradient(90deg, #14213D 0%, #0066D7 100%);
  -webkit-background-clip: text; background-clip: text;
  -webkit-text-fill-color: transparent;
}
.voice-impl__head p { color: #475569; font-size: 16px; max-width: 720px; margin: 0 auto; }
.impl-phase { margin-bottom: 32px; position: relative; }
.impl-phase__pill {
  display: block; width: max-content; margin: 0 auto 18px;
  background: #14213D; color: #fff;
  padding: 8px 20px; border-radius: 9999px;
  font-size: 14px; font-weight: 600;
}
.impl-phase__cards {
  display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
  max-width: 880px; margin: 0 auto;
  position: relative;
}
.impl-phase__cards::before {
  content: ''; position: absolute;
  left: 50%; top: -32px; bottom: -32px; width: 1px;
  background: #cbd5e1; transform: translateX(-50%);
  z-index: 0;
}
@media (max-width: 720px) {
  .impl-phase__cards { grid-template-columns: 1fr; }
  .impl-phase__cards::before { display: none; }
}
.impl-card {
  background: #fff; border: 1px solid #e2e8f0;
  border-radius: 12px; padding: 22px 24px;
  position: relative; z-index: 1;
}
.impl-card__head {
  display: flex; align-items: center; gap: 10px; margin-bottom: 8px;
}
.impl-card__head svg { width: 18px; height: 18px; color: #36B37E; flex-shrink: 0; }
.impl-card h3 { color: #14213D; font-size: 16.5px; font-weight: 700; margin: 0; }
.impl-card p { color: #475569; font-size: 13.5px; line-height: 1.55; margin: 0; }
.impl-support {
  background: #fff; border: 1px solid #e2e8f0;
  border-radius: 14px; padding: 28px 32px;
  max-width: 560px; margin: 24px auto 0; text-align: center;
}
.impl-support h3 { color: #14213D; font-size: 17px; font-weight: 700; margin: 0 0 14px; }
.impl-support__grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 8px 18px;
  text-align: left;
}
@media (max-width: 540px) { .impl-support__grid { grid-template-columns: 1fr; } }
.impl-support__item {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: #475569;
}
.impl-support__item svg { width: 14px; height: 14px; color: #94a3b8; flex-shrink: 0; }

/* How it works — 4-step horizontal grid (light section, matches live) */
.voice-how { padding: 96px 0; background: #fff; border-top: 1px solid #e5e7eb; }
.voice-how__container { max-width: 1180px; margin: 0 auto; padding: 0 24px; }
.voice-how__head { text-align: center; margin-bottom: 56px; }
.voice-how__head h2 {
  font-size: clamp(28px, 3.6vw, 42px); font-weight: 700;
  margin: 0 0 12px; letter-spacing: -0.02em;
  background: linear-gradient(90deg, #14213D 0%, #0066D7 100%);
  -webkit-background-clip: text; background-clip: text;
  -webkit-text-fill-color: transparent;
}
.voice-how__head p { color: #475569; font-size: 16px; max-width: 760px; margin: 0 auto; line-height: 1.55; }
.voice-how__grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;
  max-width: 1080px; margin: 0 auto;
}
@media (max-width: 980px) { .voice-how__grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 540px) { .voice-how__grid { grid-template-columns: 1fr; } }
.how-step {
  background: #fff; border: 1px solid #e2e8f0;
  border-radius: 14px; padding: 28px 24px;
  text-align: center;
  transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
}
.how-step:hover { transform: translateY(-3px); box-shadow: 0 16px 32px rgba(20,33,61,0.08); border-color: #cbd5e1; }
.how-step__num {
  width: 44px; height: 44px; margin: 0 auto 16px;
  display: inline-flex; align-items: center; justify-content: center;
  background: #f1f5f9; color: #64748b;
  border-radius: 50%; font-weight: 600; font-size: 14px;
  font-family: 'JetBrains Mono', monospace;
}
.how-step h3 { color: #14213D; font-size: 17px; font-weight: 700; margin: 0 0 10px; }
.how-step p  { color: #475569; font-size: 13.5px; line-height: 1.55; margin: 0; }

/* Comparison */
.voice-compare { padding: 96px 0; background: linear-gradient(180deg, #0a1022 0%, #050814 100%); }
.voice-compare__container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
.compare-table {
  width: 100%; border-collapse: separate; border-spacing: 0;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 16px; overflow: hidden;
}
.compare-table th, .compare-table td {
  padding: 18px 22px; text-align: left;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.compare-table thead th {
  background: rgba(255,255,255,0.04);
  color: #fff; font-weight: 700; font-size: 14px;
}
.compare-table thead th:nth-child(2) { color: var(--blue-300); }
.compare-table tbody tr:last-child th, .compare-table tbody tr:last-child td { border-bottom: 0; }
.compare-table tbody th { color: var(--text-muted); font-weight: 500; font-size: 14px; }
.compare-table tbody td { color: #fff; font-size: 14px; }
.compare-table td:first-of-type { color: rgba(255,100,100,0.85); }
.compare-table td:last-of-type { color: rgba(54,179,126,0.95); font-weight: 600; }
@media (max-width: 720px) { .compare-table th, .compare-table td { padding: 12px 14px; font-size: 13px; } }

/* FAQ */
.voice-faq { padding: 96px 0; background: #0a1022; }
.voice-faq__container { max-width: 880px; margin: 0 auto; padding: 0 24px; }
.faq-item {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px; margin-bottom: 12px;
  transition: border-color 200ms ease;
}
.faq-item[open] { border-color: rgba(51,133,223,0.30); }
.faq-item summary {
  list-style: none; cursor: pointer;
  padding: 22px 24px;
  display: flex; align-items: center; justify-content: space-between; gap: 16px;
  font-weight: 600; color: #fff; font-size: 16.5px; line-height: 1.4;
}
.faq-item summary::-webkit-details-marker { display: none; }
.faq-item summary::after {
  content: ''; width: 12px; height: 12px; flex-shrink: 0;
  border-right: 2px solid currentColor; border-bottom: 2px solid currentColor;
  transform: rotate(45deg) translateY(-2px);
  transition: transform 200ms ease;
}
.faq-item[open] summary::after { transform: rotate(-135deg) translateY(0); }
.faq-item__body { padding: 0 24px 22px; color: var(--text-muted); font-size: 15px; line-height: 1.65; }

/* Bottom CTA */
.voice-cta { padding: 96px 0; background: linear-gradient(180deg, #050814 0%, #0a1022 100%); }
.voice-cta__container { max-width: 980px; margin: 0 auto; padding: 0 24px; }
.voice-cta__inner {
  background: linear-gradient(135deg, #0A1022 0%, #14213D 60%, #0066D7 140%);
  border-radius: 20px; padding: 56px 48px;
  text-align: center; position: relative; overflow: hidden;
  box-shadow: 0 24px 48px rgba(0,0,0,0.3);
}
.voice-cta__inner::before {
  content: ''; position: absolute; inset: 0; pointer-events: none;
  background: radial-gradient(circle at 100% 0%, rgba(51,133,223,0.30) 0%, transparent 50%);
}
.voice-cta__inner > * { position: relative; z-index: 1; }
.voice-cta h2 {
  color: #fff; font-size: clamp(26px, 3.4vw, 38px);
  font-weight: 700; margin: 0 0 14px; letter-spacing: -0.02em; line-height: 1.15;
}
.voice-cta p { color: rgba(255,255,255,0.85); font-size: 17px; line-height: 1.55; max-width: 640px; margin: 0 auto 28px; }
.voice-cta__buttons { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }

@media (max-width: 720px) {
  .voice-hero { padding: 56px 0 64px; }
  .voice-capabilities, .voice-features, .voice-how, .voice-compare, .voice-faq, .voice-cta { padding: 64px 0; }
  .section-head { margin-bottom: 36px; }
  .voice-cta__inner { padding: 36px 24px; }
}

/* Section reveal */
@keyframes voiceReveal { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.voice-capabilities, .voice-features, .voice-how, .voice-compare, .voice-faq, .voice-cta {
  animation: voiceReveal 0.6s ease-out both;
}
@media (prefers-reduced-motion: reduce) {
  .voice-capabilities, .voice-features, .voice-how, .voice-compare, .voice-faq, .voice-cta { animation: none; }
  .voice-cap, .voice-feat, .voice-step, .faq-item { transition: none; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/../partials/site-nav.php'; ?>

<main id="main" aria-label="AI Voice Operations">

  <!-- ── HERO ─────────────────────────────────────────────────────── -->
  <section class="voice-hero">
    <div class="voice-hero__inner">
      <span class="voice-hero__eyebrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/></svg>
        AI Voice Operations
      </span>
      <h1>Replace Your Entire Call Center With <span class="accent">AI That Sounds More Human Than Humans.</span></h1>
      <p class="voice-hero__lead">Handle unlimited conversations in 40+ languages, 24/7, with empathy and intelligence that deepens customer relationships.</p>
      <div class="voice-hero__ctas">
        <a href="/assess" class="btn btn--primary btn--lg">Start Free Voice Demo &rarr;</a>
        <a href="/booking.php" class="btn btn--ghost btn--lg">Request Voice Agent Demo</a>
      </div>

      <div class="voice-hero__metrics" aria-label="Key results">
        <div class="voice-metric"><div class="voice-metric__value">90<span class="unit">%</span></div><div class="voice-metric__label">Cost reduction</div></div>
        <div class="voice-metric"><div class="voice-metric__value">40<span class="unit">+</span></div><div class="voice-metric__label">Languages</div></div>
        <div class="voice-metric"><div class="voice-metric__value">24/7</div><div class="voice-metric__label">Always on</div></div>
        <div class="voice-metric"><div class="voice-metric__value">3<span class="unit">wk</span></div><div class="voice-metric__label">To go-live</div></div>
      </div>
    </div>
  </section>

  <!-- ── WHAT THEY DO ─────────────────────────────────────────────── -->
  <section class="voice-capabilities">
    <div class="voice-capabilities__container">
      <div class="section-head">
        <span class="section-head__eyebrow">Capabilities</span>
        <h2>What Our AI Voice Agents Do</h2>
        <p>Our AI Voice Agents handle a wide range of business functions with human-like intelligence and zero wait times.</p>
      </div>

      <div class="voice-cap__grid">
        <article class="voice-cap">
          <div class="voice-cap__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
          <h3>Customer Service Automation</h3>
          <p>Respond to comments, questions, and customer service requests with empathy and intelligence that builds genuine relationships at infinite scale.</p>
        </article>

        <article class="voice-cap">
          <div class="voice-cap__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
          <h3>Sales &amp; Appointment Booking</h3>
          <p>Book appointments directly into calendars, AI handles all booking logistics, then qualifies leads through natural conversation.</p>
        </article>

        <article class="voice-cap">
          <div class="voice-cap__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg></div>
          <h3>Lead Qualification</h3>
          <p>Qualify leads through natural conversation with dynamic flows based on prospect behaviour. Handles objections in real time.</p>
        </article>

        <article class="voice-cap">
          <div class="voice-cap__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h12"/></svg></div>
          <h3>Returns, Refunds &amp; Complaints</h3>
          <p>Process returns, refunds, and complaints empathetically. Recognises tone and escalates to humans only when truly needed.</p>
        </article>

        <article class="voice-cap">
          <div class="voice-cap__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20a8 8 0 1 0-8-8"/><polyline points="12 6 12 12 16 14"/></svg></div>
          <h3>Voice of Customer Programs</h3>
          <p>Analyse communication and feedback to understand the emotional responses your brand and offerings generate &mdash; at every touchpoint.</p>
        </article>

        <article class="voice-cap">
          <div class="voice-cap__icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v12H4z"/><path d="M2 20h20"/></svg></div>
          <h3>Cross-Channel Continuity</h3>
          <p>Personalised multi-channel conversation management. Voice, chat, email &mdash; one identity, full memory across channels.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- ── FEATURES ─────────────────────────────────────────────────── -->
  <section class="voice-features">
    <div class="voice-features__container">
      <div class="section-head">
        <span class="section-head__eyebrow">Why It Works</span>
        <h2>Experience the Future of Voice Technology</h2>
        <p>Our voice AI doesn't just answer calls &mdash; it builds relationships. With emotional intelligence that understands context, tone, and intent, it delivers genuinely human experiences at infinite scale.</p>
      </div>

      <div class="voice-feat__grid">
        <div class="voice-feat">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/></svg>
          <h4>Human-like Conversations</h4>
          <p>Natural cadence, interruptions, contextual memory. Customers don't realise they're talking to AI.</p>
        </div>
        <div class="voice-feat">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
          <h4>40+ Languages</h4>
          <p>Native fluency across the languages your customers speak. Same agent, same brand, every market.</p>
        </div>
        <div class="voice-feat">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg>
          <h4>Massive Concurrency</h4>
          <p>Handle thousands of conversations simultaneously. Scale to a Black Friday surge without hiring.</p>
        </div>
        <div class="voice-feat">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m19 5-7 7-7-7"/></svg>
          <h4>Custom Voice Design</h4>
          <p>A voice identity matched to your brand and use case &mdash; from warm and reassuring to crisp and efficient.</p>
        </div>
        <div class="voice-feat">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/><path d="M12 7v5l3 3"/></svg>
          <h4>Zero Wait Times</h4>
          <p>Every customer is answered immediately, every time. No queues. No "your call is important to us".</p>
        </div>
        <div class="voice-feat">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
          <h4>Seamless Handoff</h4>
          <p>When a human is needed, the AI passes a complete transcript and context. The customer never repeats themselves.</p>
        </div>
        <div class="voice-feat">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18"/><path d="M9 21V9"/></svg>
          <h4>Integrates with Your Stack</h4>
          <p>Salesforce, HubSpot, Zendesk, ServiceNow, Calendly, Acuity, custom databases. Pre-built &amp; bespoke connectors.</p>
        </div>
        <div class="voice-feat">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <h4>Enterprise-Grade Security</h4>
          <p>SOC 2 Type II, end-to-end encryption, GDPR / CCPA / HIPAA / PCI DSS compliant. Private cloud or on-prem available.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ── PRICING PLANS ─────────────────────────────────────────────── -->
  <section class="voice-pricing" id="pricing">
    <div class="voice-pricing__container">
      <div class="voice-pricing__head">
        <h2>Pricing Plans</h2>
        <p>Transparent pricing with plans to suit businesses at every stage of growth.</p>
      </div>

      <div class="pricing-grid">
        <article class="pricing-tier">
          <h3>Starter</h3>
          <p class="pricing-tier__intro">For small teams and proof-of-concept</p>
          <p class="pricing-tier__fee-label">Setup Fee</p>
          <p class="pricing-tier__price">&pound;3,500</p>
          <p class="pricing-tier__when">one-time</p>
          <p class="pricing-tier__incl-label">Included Calls</p>
          <p class="pricing-tier__calls">Pre-paid blocks (min. 250)</p>
          <p class="pricing-tier__per-call">Equivalent to &pound;1.95 per call</p>
          <ul>
            <li>24/7 availability</li>
            <li>Standard integrations</li>
            <li>Email support</li>
          </ul>
          <a href="/booking.php" class="pricing-tier__cta">Get Started</a>
        </article>

        <article class="pricing-tier pricing-tier--popular">
          <span class="pricing-tier__badge">Most Popular</span>
          <h3>Business</h3>
          <p class="pricing-tier__intro">For growing operations needing predictability</p>
          <p class="pricing-tier__fee-label">Setup Fee</p>
          <p class="pricing-tier__price">&pound;1,300</p>
          <p class="pricing-tier__when">one-time</p>
          <p class="pricing-tier__incl-label">Included Calls</p>
          <p class="pricing-tier__calls">5,000 calls per month</p>
          <p class="pricing-tier__per-call">Equivalent to &pound;0.50 per call</p>
          <ul>
            <li>All Starter features</li>
            <li>Advanced integrations</li>
            <li>Priority support</li>
          </ul>
          <a href="/booking.php" class="pricing-tier__cta">Get Started</a>
        </article>

        <article class="pricing-tier">
          <h3>Enterprise</h3>
          <p class="pricing-tier__intro">For large-scale deployments with custom needs</p>
          <p class="pricing-tier__fee-label">Setup Fee</p>
          <p class="pricing-tier__price">Custom</p>
          <p class="pricing-tier__when">tailored to your needs</p>
          <p class="pricing-tier__incl-label">Included Calls</p>
          <p class="pricing-tier__calls">Unlimited</p>
          <p class="pricing-tier__per-call">Contact us for pricing</p>
          <ul>
            <li>All Business features</li>
            <li>Custom integrations</li>
            <li>Dedicated account manager</li>
          </ul>
          <a href="/contact.php" class="pricing-tier__cta">Contact Sales</a>
        </article>
      </div>

      <div class="pricing-extras">
        <div class="pricing-extras__item"><strong>Transparent Billing:</strong> Know your committed spend upfront.</div>
        <div class="pricing-extras__item"><strong>Flex Up or Down:</strong> Adjust your block size or switch tiers anytime.</div>
      </div>
      <p class="pricing-guarantee"><strong>90-Day Performance Guarantee:</strong> See measurable cost reduction in three months or your next month is on us.</p>

      <div class="pricing-cta-row">
        <a href="/assess" class="btn btn--primary">Start Free Voice Demo &rarr;</a>
        <a href="/calculators" class="btn--ghost-light">Calculate Your Savings</a>
        <a href="/contact.php" class="btn--text">Speak to an Expert</a>
      </div>
    </div>
  </section>

  <!-- ── IMPLEMENTATION PROCESS — light timeline ─────────────────── -->
  <section class="voice-impl">
    <div class="voice-impl__container">
      <div class="voice-impl__head">
        <h2>Implementation Process</h2>
        <p>We'll have your AI Voice Agents up and running in as little as three weeks with our streamlined process.</p>
      </div>

      <div class="impl-phase">
        <span class="impl-phase__pill">Discovery &amp; Planning</span>
        <div class="impl-phase__cards">
          <div class="impl-card">
            <div class="impl-card__head">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
              <h3>Discovery Workshop</h3>
            </div>
            <p>We'll analyze your current call center operations, identify key use cases, and establish KPIs for measuring success.</p>
          </div>
          <div class="impl-card">
            <div class="impl-card__head">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
              <h3>Knowledge Collection</h3>
            </div>
            <p>We'll gather your existing resources&mdash;FAQs, call scripts, playbooks, and call recordings&mdash;to inform your Voice Agents.</p>
          </div>
        </div>
      </div>

      <div class="impl-phase">
        <span class="impl-phase__pill">Development &amp; Integration</span>
        <div class="impl-phase__cards">
          <div class="impl-card">
            <div class="impl-card__head">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
              <h3>Voice Agent Development</h3>
            </div>
            <p>Our team crafts your custom Voice Agents with the right personality, language capabilities, and domain expertise.</p>
          </div>
          <div class="impl-card">
            <div class="impl-card__head">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
              <h3>System Integration</h3>
            </div>
            <p>We connect your Voice Agents with your CRM, ticketing systems, knowledge base, and other critical business tools.</p>
          </div>
        </div>
      </div>

      <div class="impl-phase">
        <span class="impl-phase__pill">Testing &amp; Launch</span>
        <div class="impl-phase__cards">
          <div class="impl-card">
            <div class="impl-card__head">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
              <h3>Quality Assurance</h3>
            </div>
            <p>We rigorously test your Voice Agents across different scenarios and edge cases to ensure exceptional performance.</p>
          </div>
          <div class="impl-card">
            <div class="impl-card__head">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
              <h3>Go-Live &amp; Optimization</h3>
            </div>
            <p>We deploy your Voice Agents and begin the continuous improvement cycle based on real-world performance data.</p>
          </div>
        </div>
      </div>

      <div class="impl-support">
        <h3>Ongoing Support</h3>
        <p style="color:#64748b;font-size:13.5px;margin:0 0 16px;">After launch, we continue to optimize your Voice Agents with:</p>
        <div class="impl-support__grid">
          <div class="impl-support__item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>Weekly performance reports</div>
          <div class="impl-support__item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>Monthly optimization meetings</div>
          <div class="impl-support__item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>Regular knowledge updates</div>
          <div class="impl-support__item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>Continuous AI training</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── HOW OUR AI VOICE AGENTS WORK — 4-step grid ──────────────── -->
  <section class="voice-how">
    <div class="voice-how__container">
      <div class="voice-how__head">
        <h2>How Our AI Voice Agents Work</h2>
        <p>We combine cutting-edge natural language processing, voice synthesis, and conversational design to create voice agents that sound and respond just like humans.</p>
      </div>

      <div class="voice-how__grid">
        <article class="how-step">
          <span class="how-step__num">01</span>
          <h3>Custom Voice Design</h3>
          <p>We create a voice identity that matches your brand and use case requirements.</p>
        </article>
        <article class="how-step">
          <span class="how-step__num">02</span>
          <h3>Conversation Design</h3>
          <p>Our team designs conversation flows optimized for your specific business objectives.</p>
        </article>
        <article class="how-step">
          <span class="how-step__num">03</span>
          <h3>Integration &amp; Testing</h3>
          <p>We integrate the voice agent with your systems and rigorously test for quality and accuracy.</p>
        </article>
        <article class="how-step">
          <span class="how-step__num">04</span>
          <h3>Deployment &amp; Optimization</h3>
          <p>Launch your voice agents and continuously optimize based on performance data.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- ── COMPARISON TABLE ─────────────────────────────────────────── -->
  <section class="voice-compare">
    <div class="voice-compare__container">
      <div class="section-head">
        <span class="section-head__eyebrow">Compare</span>
        <h2>Traditional Call Centre vs. Syncsity AI Voice</h2>
        <p>See how our AI Voice solution compares to traditional call centres across key performance metrics.</p>
      </div>

      <table class="compare-table" aria-label="Comparison: Traditional call centre vs Syncsity AI Voice">
        <thead>
          <tr>
            <th scope="col">Metric</th>
            <th scope="col">Traditional Call Centre</th>
            <th scope="col">Syncsity AI Voice</th>
          </tr>
        </thead>
        <tbody>
          <tr><th scope="row">Operating cost</th><td>Full-cost agent + management overhead</td><td>Up to 90% lower</td></tr>
          <tr><th scope="row">Availability</th><td>Business hours, queues during peaks</td><td>24/7, zero queue</td></tr>
          <tr><th scope="row">Languages</th><td>Limited by hiring pool</td><td>40+ native</td></tr>
          <tr><th scope="row">Concurrent calls</th><td>1 per agent</td><td>Thousands simultaneously</td></tr>
          <tr><th scope="row">Quality consistency</th><td>Varies by shift, fatigue, churn</td><td>Identical every call</td></tr>
          <tr><th scope="row">Time to scale +50%</th><td>6 to 12 weeks of hiring &amp; training</td><td>Instant</td></tr>
          <tr><th scope="row">Time to deploy</th><td>3 to 6 months</td><td>3 weeks</td></tr>
        </tbody>
      </table>
    </div>
  </section>

  <!-- ── FAQ ──────────────────────────────────────────────────────── -->
  <section class="voice-faq">
    <div class="voice-faq__container">
      <div class="section-head">
        <span class="section-head__eyebrow">FAQ</span>
        <h2>Common Questions</h2>
        <p>Get answers to the most common questions about our AI Voice Agents.</p>
      </div>

      <details class="faq-item">
        <summary>How do your AI Voice Agents differ from traditional IVR systems?</summary>
        <div class="faq-item__body">Unlike IVR systems that use rigid menus and limited pathways, our AI Voice Agents engage in natural conversations. They understand context, remember details across the conversation, respond to interruptions, and can handle complex, multi-step interactions just like a human agent would.</div>
      </details>
      <details class="faq-item">
        <summary>How quickly can you deploy an AI Voice Agent for my business?</summary>
        <div class="faq-item__body">We can deploy AI Voice Agents in as little as three weeks from kickoff to go-live. The exact timeline depends on the complexity of the use case, availability of training materials, and required integrations. Our implementation process is designed to get you up and running with minimal delay.</div>
      </details>
      <details class="faq-item">
        <summary>What systems and platforms can your Voice Agents integrate with?</summary>
        <div class="faq-item__body">Our AI Voice Agents integrate with all major CRM platforms (Salesforce, HubSpot, etc.), ticketing systems (Zendesk, ServiceNow), scheduling tools (Calendly, Acuity), payment processors, and custom databases. We offer pre-built connectors for common systems and can develop custom integrations for proprietary platforms.</div>
      </details>
      <details class="faq-item">
        <summary>Is my data secure with your Voice Agents?</summary>
        <div class="faq-item__body">Absolutely. We maintain rigorous security standards including SOC 2 Type II compliance, end-to-end encryption, and regular penetration testing. We can deploy in your private cloud or on-premises for sensitive industries. All data processing complies with GDPR, CCPA, and industry-specific regulations such as HIPAA for healthcare or PCI DSS for payment processing.</div>
      </details>
      <details class="faq-item">
        <summary>Will AI Voice Agents replace human agents entirely?</summary>
        <div class="faq-item__body">Our AI Voice Agents handle most inquiries independently, but they're also designed to recognise when human intervention would be valuable. In these cases, they can seamlessly transfer the call to a human agent, providing a complete summary of the conversation and issues discussed so far, ensuring continuity and preventing customers from having to repeat information.</div>
      </details>
    </div>
  </section>

  <!-- ── CTA ──────────────────────────────────────────────────────── -->
  <section class="voice-cta">
    <div class="voice-cta__container">
      <div class="voice-cta__inner">
        <h2>Hear our AI Voice Agents in action.</h2>
        <p>Schedule a demo to hear our AI Voice Agents handle real conversations and discover how they can transform your business communications &mdash; or skip the call and start with a free 15-minute Aha! Assessment.</p>
        <div class="voice-cta__buttons">
          <a href="/assess" class="btn btn--primary btn--lg">Start Free 15-min Assessment &rarr;</a>
          <a href="/booking.php" class="btn btn--orange btn--lg">Book a Voice Demo</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include __DIR__ . '/../partials/site-footer.php'; ?>
