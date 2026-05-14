@extends('debitur.layouts.app')

@section('title', 'Dashboard | Debitur PT Pasada Indonesia')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════
   DESIGN TOKENS
═══════════════════════════════════════════ */
:root {
    --teal-900:   #0a3d3c;
    --teal-800:   #0f5b5a;
    --teal-700:   #167472;
    --teal-600:   #1b8c89;
    --teal-50:    #f0fafa;
    --teal-100:   #d4efee;

    --gold-500:   #e2a526;
    --gold-400:   #edb83f;
    --gold-100:   #fdf3db;
    --gold-50:    #fffbf0;

    --green-500:  #16a34a;
    --green-50:   #f0fdf4;
    --green-100:  #dcfce7;

    --red-500:    #dc2626;
    --red-50:     #fef2f2;

    --slate-900:  #0f172a;
    --slate-700:  #334155;
    --slate-500:  #64748b;
    --slate-300:  #cbd5e1;
    --slate-100:  #f1f5f9;
    --slate-50:   #f8fafc;

    --surface:    #ffffff;
    --bg:         #f3f6f8;
    --border:     #e2e8f0;

    --radius-sm:  8px;
    --radius:     14px;
    --radius-lg:  20px;
    --radius-xl:  28px;

    --shadow-xs:  0 1px 2px rgba(15,31,53,.05);
    --shadow-sm:  0 2px 8px rgba(15,31,53,.07), 0 1px 2px rgba(15,31,53,.04);
    --shadow-md:  0 8px 24px rgba(15,31,53,.10), 0 2px 6px rgba(15,31,53,.04);
    --shadow-lg:  0 20px 48px rgba(15,31,53,.14);

    --font-display: 'Fraunces', Georgia, serif;
    --font-body:    'Outfit', sans-serif;
}

/* ═══════════════════════════════════════════
   BASE
═══════════════════════════════════════════ */
body { font-family: var(--font-body); background: var(--bg); color: var(--slate-900); }

/* ═══════════════════════════════════════════
   PAGE WRAPPER
═══════════════════════════════════════════ */
.dash-wrapper {
    padding: 28px 32px 48px;
    max-width: 1280px;
    margin: 0 auto;
    animation: pageFadeIn .5s ease both;
}
@keyframes pageFadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ═══════════════════════════════════════════
   SECTION LABEL
═══════════════════════════════════════════ */
.section-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}
.section-label h6 {
    font-family: var(--font-body);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--slate-500);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-label h6 .dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--gold-500);
    display: inline-block;
}
.section-label a {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--teal-700);
    text-decoration: none;
    letter-spacing: .02em;
    transition: color .2s;
}
.section-label a:hover { color: var(--teal-900); }

/* ═══════════════════════════════════════════
   WELCOME BANNER
═══════════════════════════════════════════ */
.welcome-banner {
    background: var(--teal-800);
    border-radius: var(--radius-xl);
    padding: 36px 40px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}
/* Geometric decorations */
.welcome-banner::before {
    content: '';
    position: absolute;
    top: -70px; right: -70px;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
}
.welcome-banner::after {
    content: '';
    position: absolute;
    bottom: -40px; right: 160px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(226,165,38,.08);
}
.welcome-geo {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    overflow: hidden;
    pointer-events: none;
}
.welcome-geo span {
    position: absolute;
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 50%;
}
.welcome-geo span:nth-child(1) { width:300px; height:300px; top:-80px; right:80px; }
.welcome-geo span:nth-child(2) { width:180px; height:180px; bottom:-40px; right:200px; }
.welcome-geo span:nth-child(3) { width:60px; height:60px; top:30px; right:300px; }

.welcome-left { position: relative; z-index: 1; }
.welcome-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(226,165,38,.18);
    border: 1px solid rgba(226,165,38,.3);
    color: var(--gold-400);
    font-size: 11.5px;
    font-weight: 600;
    letter-spacing: .05em;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 20px;
    margin-bottom: 14px;
}
.welcome-eyebrow i { font-size: 12px; }
.welcome-heading {
    font-family: var(--font-display);
    font-size: 30px;
    font-weight: 600;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 10px;
}
.welcome-heading em {
    font-style: italic;
    color: var(--gold-400);
}
.welcome-desc {
    font-size: 13.5px;
    color: rgba(255,255,255,.55);
    line-height: 1.7;
    max-width: 400px;
    margin-bottom: 24px;
}
.welcome-actions { display: flex; align-items: center; gap: 12px; }
.btn-gold {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--gold-500);
    color: var(--teal-900);
    font-size: 13px;
    font-weight: 700;
    padding: 11px 22px;
    border-radius: var(--radius);
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all .25s;
    white-space: nowrap;
    letter-spacing: .01em;
}
.btn-gold:hover {
    background: var(--gold-400);
    color: var(--teal-900);
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(226,165,38,.4);
}
.btn-ghost-white {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    color: rgba(255,255,255,.7);
    font-size: 13px;
    font-weight: 600;
    padding: 11px 20px;
    border-radius: var(--radius);
    text-decoration: none;
    border: 1px solid rgba(255,255,255,.18);
    cursor: pointer;
    transition: all .25s;
}
.btn-ghost-white:hover {
    background: rgba(255,255,255,.08);
    color: #fff;
    border-color: rgba(255,255,255,.35);
}

/* Welcome illustration */
.welcome-right {
    position: relative; z-index: 1;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
}
.welcome-stat-pill {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 50px;
    padding: 10px 18px 10px 10px;
    min-width: 200px;
}
.ws-icon {
    width: 36px; height: 36px;
    border-radius: 50%;
    display: grid; place-items: center;
    flex-shrink: 0;
}
.ws-icon.gold { background: rgba(226,165,38,.25); color: var(--gold-400); }
.ws-icon.green { background: rgba(22,163,74,.25); color: #4ade80; }
.ws-label { font-size: 11px; color: rgba(255,255,255,.5); font-weight: 500; }
.ws-value { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; }

/* ═══════════════════════════════════════════
   STATS ROW
═══════════════════════════════════════════ */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 32px;
}
.stat-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    padding: 22px 24px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: box-shadow .25s, transform .25s;
    cursor: default;
    position: relative;
    overflow: hidden;
}
.stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    opacity: 0;
    transition: opacity .25s;
}
.stat-card.c-teal::after   { background: var(--teal-700); }
.stat-card.c-gold::after   { background: var(--gold-500); }
.stat-card.c-green::after  { background: var(--green-500); }
.stat-card.c-red::after    { background: var(--red-500); }
.stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
.stat-card:hover::after { opacity: 1; }

.stat-header { display: flex; align-items: center; justify-content: space-between; }
.stat-icon {
    width: 46px; height: 46px;
    border-radius: var(--radius);
    display: grid; place-items: center;
    font-size: 20px;
    flex-shrink: 0;
}
.stat-icon.c-teal  { background: var(--teal-100);  color: var(--teal-700); }
.stat-icon.c-gold  { background: var(--gold-100);  color: var(--gold-500); }
.stat-icon.c-green { background: var(--green-100); color: var(--green-500); }
.stat-icon.c-red   { background: var(--red-50);    color: var(--red-500); }

.stat-trend {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 20px;
    display: flex; align-items: center; gap: 3px;
}
.stat-trend.up   { background: var(--green-50); color: var(--green-500); }
.stat-trend.warn { background: var(--gold-50);  color: var(--gold-500); }
.stat-trend.neu  { background: var(--slate-100); color: var(--slate-500); }
.stat-trend.down { background: var(--red-50);   color: var(--red-500); }

.stat-body {}
.stat-value {
    font-family: var(--font-display);
    font-size: 26px;
    font-weight: 700;
    color: var(--slate-900);
    line-height: 1;
    margin-bottom: 4px;
}
.stat-label {
    font-size: 12.5px;
    color: var(--slate-500);
    font-weight: 500;
}

/* ═══════════════════════════════════════════
   MAIN GRID
═══════════════════════════════════════════ */
.main-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
    margin-bottom: 24px;
}

/* ═══════════════════════════════════════════
   CARD BASE
═══════════════════════════════════════════ */
.card-panel {
    background: var(--surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.card-panel-header {
    padding: 20px 24px 0;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}
.card-panel-title {
    font-size: 14.5px;
    font-weight: 700;
    color: var(--slate-900);
    margin-bottom: 2px;
}
.card-panel-sub {
    font-size: 12px;
    color: var(--slate-500);
    font-weight: 400;
}
.card-panel-body { padding: 20px 24px 24px; }

/* ═══════════════════════════════════════════
   STATUS BADGE
═══════════════════════════════════════════ */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 20px;
    white-space: nowrap;
}
.status-badge i { font-size: 11px; }
.sb-warning  { background: var(--gold-100);  color: #92650a; }
.sb-success  { background: var(--green-100); color: var(--green-500); }
.sb-info     { background: var(--teal-100);  color: var(--teal-700); }
.sb-danger   { background: var(--red-50);    color: var(--red-500); }
.sb-muted    { background: var(--slate-100); color: var(--slate-500); }

/* ═══════════════════════════════════════════
   PROGRESS SECTION (inside card)
═══════════════════════════════════════════ */
.progress-section { padding: 20px 24px 24px; }
.progress-overview {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--border);
}
/* SVG Ring */
.ring-wrap {
    position: relative;
    width: 90px; height: 90px;
    flex-shrink: 0;
}
.ring-wrap svg { transform: rotate(-90deg); }
.ring-center {
    position: absolute;
    inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    line-height: 1.2;
}
.ring-pct {
    font-family: var(--font-display);
    font-size: 22px;
    font-weight: 700;
    color: var(--teal-800);
}
.ring-sub { font-size: 10px; color: var(--slate-500); font-weight: 500; }
.ring-info {}
.ring-info p {
    font-size: 13px;
    font-weight: 500;
    color: var(--slate-500);
    margin-bottom: 6px;
}
.ring-info strong {
    display: block;
    font-family: var(--font-display);
    font-size: 18px;
    font-weight: 600;
    color: var(--slate-900);
    margin-bottom: 8px;
}

/* Sub progress bars */
.sub-progress { display: flex; flex-direction: column; gap: 14px; }
.sp-item {}
.sp-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 7px;
}
.sp-label { font-size: 12.5px; font-weight: 500; color: var(--slate-700); }
.sp-count { font-size: 12px; font-weight: 700; color: var(--slate-500); }
.sp-bar {
    height: 7px;
    background: var(--slate-100);
    border-radius: 10px;
    overflow: hidden;
}
.sp-fill {
    height: 100%;
    border-radius: 10px;
    transition: width .8s cubic-bezier(.4,0,.2,1) .3s;
}
.sp-fill.teal  { background: linear-gradient(90deg, var(--teal-700), var(--teal-600)); }
.sp-fill.gold  { background: linear-gradient(90deg, var(--gold-500), var(--gold-400)); }
.sp-fill.green { background: linear-gradient(90deg, var(--green-500), #22c55e); }

/* ═══════════════════════════════════════════
   TIMELINE
═══════════════════════════════════════════ */
.timeline { padding: 4px 0; }
.tl-item {
    display: flex;
    gap: 14px;
    padding-bottom: 20px;
    position: relative;
}
.tl-item:last-child { padding-bottom: 0; }
.tl-item:last-child .tl-line { display: none; }

.tl-left { display: flex; flex-direction: column; align-items: center; }
.tl-dot {
    width: 28px; height: 28px;
    border-radius: 50%;
    display: grid; place-items: center;
    flex-shrink: 0;
    font-size: 12px;
    position: relative;
    z-index: 1;
}
.tl-dot.done    { background: var(--green-100); color: var(--green-500); }
.tl-dot.active  { background: var(--gold-100);  color: var(--gold-500); box-shadow: 0 0 0 4px var(--gold-50); }
.tl-dot.pending { background: var(--slate-100); color: var(--slate-400); }
.tl-line {
    width: 2px;
    flex: 1;
    margin-top: 4px;
    background: var(--border);
    border-radius: 2px;
    min-height: 16px;
}
.tl-line.done { background: var(--green-100); }

.tl-content { flex: 1; padding-top: 3px; }
.tl-title {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--slate-800);
    margin-bottom: 2px;
}
.tl-title.muted { color: var(--slate-400); }
.tl-desc { font-size: 12px; color: var(--slate-500); line-height: 1.5; margin-bottom: 4px; }
.tl-date { font-size: 11px; font-weight: 600; color: var(--slate-400); }
.tl-date.green { color: var(--green-500); }

/* spinner kecil */
.tl-spinner {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: var(--gold-500);
    font-weight: 600;
    margin-top: 4px;
}
.spin { animation: spin .9s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ═══════════════════════════════════════════
   DOCUMENT CHECKLIST
═══════════════════════════════════════════ */
.doc-list { display: flex; flex-direction: column; }
.doc-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
    transition: background .2s;
}
.doc-item:last-child { border-bottom: none; padding-bottom: 0; }
.doc-item:first-child { padding-top: 0; }
.doc-icon {
    width: 36px; height: 36px;
    border-radius: var(--radius-sm);
    display: grid; place-items: center;
    font-size: 15px;
    flex-shrink: 0;
}
.doc-icon.done    { background: var(--green-50);  color: var(--green-500); }
.doc-icon.pending { background: var(--gold-50);   color: var(--gold-500); }
.doc-icon.missing { background: var(--red-50);    color: var(--red-500); }

.doc-info { flex: 1; min-width: 0; }
.doc-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--slate-800);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.doc-note { font-size: 11.5px; color: var(--slate-500); }

.doc-action { flex-shrink: 0; }
.btn-upload {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: var(--radius-sm);
    background: var(--gold-100);
    color: #92650a;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background .2s;
    white-space: nowrap;
}
.btn-upload:hover { background: var(--gold-500); color: #fff; }

/* ═══════════════════════════════════════════
   PROPERTY CARDS
═══════════════════════════════════════════ */
.property-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}
.prop-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    overflow: hidden;
    transition: box-shadow .25s, transform .25s;
    cursor: pointer;
}
.prop-card:hover { box-shadow: var(--shadow-md); transform: translateY(-4px); }
.prop-img {
    height: 160px;
    position: relative;
    overflow: hidden;
}
.prop-img-bg {
    width: 100%; height: 100%;
    transition: transform .5s ease;
    background-size: cover;
    background-position: center;
}
.prop-card:hover .prop-img-bg { transform: scale(1.06); }
.prop-badge {
    position: absolute;
    top: 10px; left: 10px;
    font-size: 10.5px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    letter-spacing: .02em;
}
.prop-badge.gold   { background: var(--gold-500); color: var(--teal-900); }
.prop-badge.teal   { background: var(--teal-800); color: #fff; }
.prop-badge.green  { background: var(--green-500); color: #fff; }
.prop-fav {
    position: absolute;
    top: 10px; right: 10px;
    width: 30px; height: 30px;
    background: rgba(255,255,255,.9);
    border-radius: 50%;
    display: grid; place-items: center;
    font-size: 13px;
    color: var(--slate-400);
    transition: color .2s, background .2s;
    cursor: pointer;
}
.prop-fav:hover { color: var(--red-500); background: #fff; }

.prop-body { padding: 16px; }
.prop-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--slate-900);
    margin-bottom: 2px;
}
.prop-type { font-size: 12px; color: var(--slate-500); margin-bottom: 10px; }
.prop-price {
    font-family: var(--font-display);
    font-size: 17px;
    font-weight: 700;
    color: var(--teal-800);
    margin-bottom: 12px;
}
.prop-features {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.prop-feat {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    color: var(--slate-500);
    font-weight: 500;
}
.prop-actions { display: flex; gap: 8px; }
.btn-prop-detail {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 8px;
    border-radius: var(--radius-sm);
    background: var(--teal-50);
    color: var(--teal-700);
    text-decoration: none;
    border: 1px solid var(--teal-100);
    cursor: pointer;
    transition: background .2s;
}
.btn-prop-detail:hover { background: var(--teal-100); color: var(--teal-800); }
.btn-prop-sim {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 8px;
    border-radius: var(--radius-sm);
    background: var(--gold-500);
    color: var(--teal-900);
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background .2s;
}
.btn-prop-sim:hover { background: var(--gold-400); }

/* ═══════════════════════════════════════════
   BOTTOM ROW
═══════════════════════════════════════════ */
.bottom-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 24px;
}

/* Tips card */
.tips-card {
    background: var(--teal-50);
    border: 1px solid var(--teal-100);
    border-radius: var(--radius-lg);
    padding: 22px 24px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
.tips-icon {
    width: 44px; height: 44px;
    background: var(--teal-800);
    border-radius: var(--radius);
    display: grid; place-items: center;
    color: var(--gold-400);
    font-size: 20px;
    flex-shrink: 0;
}
.tips-title { font-size: 14px; font-weight: 700; color: var(--teal-900); margin-bottom: 10px; }
.tips-list { padding-left: 16px; margin: 0; }
.tips-list li {
    font-size: 12.5px;
    color: var(--slate-600);
    line-height: 1.6;
    margin-bottom: 4px;
}

/* CS card */
.cs-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 22px 24px;
    display: flex;
    gap: 16px;
    align-items: center;
}
.cs-avatar {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--teal-800), var(--teal-600));
    display: grid; place-items: center;
    color: var(--gold-400);
    font-size: 22px;
    flex-shrink: 0;
}
.cs-title { font-size: 14px; font-weight: 700; color: var(--slate-900); margin-bottom: 3px; }
.cs-sub   { font-size: 12px; color: var(--slate-500); margin-bottom: 14px; }
.cs-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.btn-wa {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12.5px; font-weight: 700;
    padding: 8px 16px; border-radius: var(--radius-sm);
    background: #25d366; color: #fff;
    text-decoration: none; border: none; cursor: pointer;
    transition: background .2s;
}
.btn-wa:hover { background: #1ebe5d; color: #fff; }
.btn-phone {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12.5px; font-weight: 600;
    padding: 8px 14px; border-radius: var(--radius-sm);
    background: var(--slate-100); color: var(--slate-700);
    text-decoration: none; border: 1px solid var(--border); cursor: pointer;
    transition: background .2s;
}
.btn-phone:hover { background: var(--slate-200); }

/* ═══════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════ */
@media (max-width: 1100px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); }
    .main-grid { grid-template-columns: 1fr; }
    .property-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .dash-wrapper { padding: 16px 16px 40px; }
    .welcome-banner { padding: 24px; flex-direction: column; align-items: flex-start; }
    .welcome-right { align-items: flex-start; width: 100%; }
    .welcome-stat-pill { min-width: unset; flex: 1; }
    .stats-row { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .property-grid { grid-template-columns: 1fr; }
    .bottom-row { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="dash-wrapper">

    {{-- ══════════════════════════════════════════
         WELCOME BANNER
    ══════════════════════════════════════════ --}}
    <div class="welcome-banner">
        <div class="welcome-geo">
            <span></span><span></span><span></span>
        </div>

        <div class="welcome-left">
            <div class="welcome-eyebrow">
                <i class="bi bi-gem"></i>
                Portal Debitur · PT Pasada Indonesia
            </div>
            <h1 class="welcome-heading">
                Selamat datang,<br><em>{{ Auth::user()->nama_lengkap ?? 'Calon Debitur' }}!</em>
            </h1>
            <p class="welcome-desc">
                Pantau progres pengajuan KPR Anda, lengkapi dokumen, dan temukan unit impian di sini.
            </p>
            <div class="welcome-actions">
                <a href="{{ route('debitur.pengajuan-kpr') }}" class="btn-gold">
                    <i class="bi bi-plus-lg"></i> Ajukan KPR Baru
                </a>
                <a href="{{ route('debitur.riwayat-pengajuan') }}" class="btn-ghost-white">
                    <i class="bi bi-list-ul"></i> Riwayat Saya
                </a>
            </div>
        </div>

        <div class="welcome-right">
            <div class="welcome-stat-pill">
                <div class="ws-icon gold"><i class="bi bi-file-earmark-text"></i></div>
                <div>
                    <div class="ws-label">Pengajuan Aktif</div>
                    <div class="ws-value">1 Pengajuan</div>
                </div>
            </div>
            <div class="welcome-stat-pill">
                <div class="ws-icon green"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div class="ws-label">Dokumen Terverifikasi</div>
                    <div class="ws-value">4 dari 6 Dokumen</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STATS ROW
    ══════════════════════════════════════════ --}}
    <div class="stats-row">
        <div class="stat-card c-teal">
            <div class="stat-header">
                <div class="stat-icon c-teal"><i class="bi bi-files"></i></div>
                <span class="stat-trend up"><i class="bi bi-arrow-up"></i> +2 tahun ini</span>
            </div>
            <div class="stat-body">
                <div class="stat-value">3</div>
                <div class="stat-label">Total Pengajuan</div>
            </div>
        </div>
        <div class="stat-card c-gold">
            <div class="stat-header">
                <div class="stat-icon c-gold"><i class="bi bi-hourglass-split"></i></div>
                <span class="stat-trend warn"><i class="bi bi-dot"></i> Dalam proses</span>
            </div>
            <div class="stat-body">
                <div class="stat-value">1</div>
                <div class="stat-label">Pengajuan Aktif</div>
            </div>
        </div>
        <div class="stat-card c-green">
            <div class="stat-header">
                <div class="stat-icon c-green"><i class="bi bi-cash-stack"></i></div>
                <span class="stat-trend up"><i class="bi bi-check2"></i> Disetujui</span>
            </div>
            <div class="stat-body">
                <div class="stat-value">Rp 1,2M</div>
                <div class="stat-label">Total Plafon Disetujui</div>
            </div>
        </div>
        <div class="stat-card c-red">
            <div class="stat-header">
                <div class="stat-icon c-red"><i class="bi bi-bell"></i></div>
                <span class="stat-trend down"><i class="bi bi-exclamation"></i> Perlu aksi</span>
            </div>
            <div class="stat-body">
                <div class="stat-value">2</div>
                <div class="stat-label">Notifikasi Baru</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MAIN GRID — Progress + Sidebar
    ══════════════════════════════════════════ --}}
    <div class="main-grid">

        {{-- KIRI: Progress Pengajuan + Timeline --}}
        <div style="display:flex; flex-direction:column; gap:20px;">

            {{-- Progress Card --}}
            <div class="card-panel">
                <div class="card-panel-header">
                    <div>
                        <div class="card-panel-title">Progress Pengajuan KPR</div>
                        <div class="card-panel-sub">Kode: <strong>KPR-2025-00001</strong> · Diajukan 10 Mei 2025</div>
                    </div>
                    <span class="status-badge sb-warning">
                        <i class="bi bi-hourglass-split"></i> Verifikasi Marketing
                    </span>
                </div>
                <div class="progress-section">
                    <div class="progress-overview">
                        <!-- SVG Ring -->
                        <div class="ring-wrap">
                            <svg width="90" height="90" viewBox="0 0 90 90">
                                <circle cx="45" cy="45" r="38" fill="none" stroke="#e2e8f0" stroke-width="7"/>
                                <circle cx="45" cy="45" r="38" fill="none"
                                    stroke="#0f5b5a" stroke-width="7"
                                    stroke-dasharray="238.76"
                                    stroke-dashoffset="155.19"
                                    stroke-linecap="round"/>
                            </svg>
                            <div class="ring-center">
                                <span class="ring-pct">35%</span>
                                <span class="ring-sub">Selesai</span>
                            </div>
                        </div>
                        <div class="ring-info">
                            <p>Unit yang dipilih</p>
                            <strong>Tipe 45/90 · Blok C-07<br>Cluster Pasada Harmoni</strong>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <span class="status-badge sb-muted"><i class="bi bi-house"></i> 45 m²</span>
                                <span class="status-badge sb-muted"><i class="bi bi-arrows-angle-expand"></i> 90 m²</span>
                                <span class="status-badge sb-info"><i class="bi bi-calendar3"></i> Tenor 180 bln</span>
                            </div>
                        </div>
                    </div>

                    <div class="sub-progress">
                        <div class="sp-item">
                            <div class="sp-header">
                                <span class="sp-label"><i class="bi bi-file-earmark-check" style="color:var(--teal-700); margin-right:5px;"></i>Kelengkapan Dokumen</span>
                                <span class="sp-count">4 / 6 dokumen</span>
                            </div>
                            <div class="sp-bar"><div class="sp-fill teal" style="width:67%"></div></div>
                        </div>
                        <div class="sp-item">
                            <div class="sp-header">
                                <span class="sp-label"><i class="bi bi-patch-check" style="color:var(--gold-500); margin-right:5px;"></i>Verifikasi Dokumen</span>
                                <span class="sp-count">2 / 4 terverifikasi</span>
                            </div>
                            <div class="sp-bar"><div class="sp-fill gold" style="width:50%"></div></div>
                        </div>
                        <div class="sp-item">
                            <div class="sp-header">
                                <span class="sp-label"><i class="bi bi-clipboard2-data" style="color:var(--green-500); margin-right:5px;"></i>Tahap Penilaian SPK</span>
                                <span class="sp-count">Menunggu</span>
                            </div>
                            <div class="sp-bar"><div class="sp-fill green" style="width:0%"></div></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="card-panel">
                <div class="card-panel-header">
                    <div>
                        <div class="card-panel-title">Timeline Pengajuan</div>
                        <div class="card-panel-sub">Riwayat perkembangan status pengajuan Anda</div>
                    </div>
                </div>
                <div class="card-panel-body">
                    <div class="timeline">
                        <div class="tl-item">
                            <div class="tl-left">
                                <div class="tl-dot done"><i class="bi bi-check-lg"></i></div>
                                <div class="tl-line done"></div>
                            </div>
                            <div class="tl-content">
                                <div class="tl-title">Pengajuan Dibuat</div>
                                <div class="tl-desc">Form pengajuan KPR berhasil disimpan dan disubmit ke sistem.</div>
                                <div class="tl-date green"><i class="bi bi-check-circle me-1"></i>10 Mei 2025</div>
                            </div>
                        </div>
                        <div class="tl-item">
                            <div class="tl-left">
                                <div class="tl-dot done"><i class="bi bi-check-lg"></i></div>
                                <div class="tl-line done"></div>
                            </div>
                            <div class="tl-content">
                                <div class="tl-title">Dokumen Diupload</div>
                                <div class="tl-desc">KTP, KK, dan Surat Keterangan Kerja telah diupload.</div>
                                <div class="tl-date green"><i class="bi bi-check-circle me-1"></i>12 Mei 2025</div>
                            </div>
                        </div>
                        <div class="tl-item">
                            <div class="tl-left">
                                <div class="tl-dot active"><i class="bi bi-eye"></i></div>
                                <div class="tl-line"></div>
                            </div>
                            <div class="tl-content">
                                <div class="tl-title">Verifikasi oleh Marketing</div>
                                <div class="tl-desc">Marketing sedang memverifikasi kelengkapan dan keabsahan dokumen Anda.</div>
                                <div class="tl-spinner">
                                    <i class="bi bi-arrow-repeat spin"></i> Sedang diproses · estimasi 3 hari
                                </div>
                            </div>
                        </div>
                        <div class="tl-item">
                            <div class="tl-left">
                                <div class="tl-dot pending"><i class="bi bi-clipboard2-data"></i></div>
                                <div class="tl-line"></div>
                            </div>
                            <div class="tl-content">
                                <div class="tl-title muted">Penilaian Admin (SPK SMART)</div>
                                <div class="tl-desc" style="color:var(--slate-400)">Admin akan menginput nilai kriteria dan menjalankan perhitungan kelayakan.</div>
                                <div class="tl-date">Menunggu tahap sebelumnya</div>
                            </div>
                        </div>
                        <div class="tl-item">
                            <div class="tl-left">
                                <div class="tl-dot pending"><i class="bi bi-trophy"></i></div>
                                <div class="tl-line"></div>
                            </div>
                            <div class="tl-content">
                                <div class="tl-title muted">Hasil Kelayakan KPR</div>
                                <div class="tl-desc" style="color:var(--slate-400)">Sistem akan menampilkan hasil penilaian: Layak, Pertimbangan, atau Tidak Layak.</div>
                                <div class="tl-date">Target 1 minggu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: Dokumen Checklist --}}
        <div>
            <div class="card-panel" style="position:sticky; top:80px;">
                <div class="card-panel-header">
                    <div>
                        <div class="card-panel-title">Checklist Dokumen</div>
                        <div class="card-panel-sub">Lengkapi untuk mempercepat proses</div>
                    </div>
                    <span class="status-badge sb-warning">4/6</span>
                </div>
                <div class="card-panel-body">
                    <div class="doc-list">
                        <div class="doc-item">
                            <div class="doc-icon done"><i class="bi bi-check-lg"></i></div>
                            <div class="doc-info">
                                <div class="doc-name">KTP Debitur</div>
                                <div class="doc-note" style="color:var(--green-500);">Terverifikasi</div>
                            </div>
                            <div class="doc-action">
                                <span class="status-badge sb-success"><i class="bi bi-check-lg"></i></span>
                            </div>
                        </div>
                        <div class="doc-item">
                            <div class="doc-icon done"><i class="bi bi-check-lg"></i></div>
                            <div class="doc-info">
                                <div class="doc-name">Kartu Keluarga</div>
                                <div class="doc-note" style="color:var(--green-500);">Terverifikasi</div>
                            </div>
                            <div class="doc-action">
                                <span class="status-badge sb-success"><i class="bi bi-check-lg"></i></span>
                            </div>
                        </div>
                        <div class="doc-item">
                            <div class="doc-icon done"><i class="bi bi-check-lg"></i></div>
                            <div class="doc-info">
                                <div class="doc-name">Surat Keterangan Kerja</div>
                                <div class="doc-note" style="color:var(--green-500);">Terverifikasi</div>
                            </div>
                            <div class="doc-action">
                                <span class="status-badge sb-success"><i class="bi bi-check-lg"></i></span>
                            </div>
                        </div>
                        <div class="doc-item">
                            <div class="doc-icon pending"><i class="bi bi-clock"></i></div>
                            <div class="doc-info">
                                <div class="doc-name">Rekening Koran</div>
                                <div class="doc-note">Diupload · menunggu verifikasi</div>
                            </div>
                            <div class="doc-action">
                                <span class="status-badge sb-warning"><i class="bi bi-hourglass-split"></i></span>
                            </div>
                        </div>
                        <div class="doc-item">
                            <div class="doc-icon missing"><i class="bi bi-upload"></i></div>
                            <div class="doc-info">
                                <div class="doc-name">Slip Gaji 3 Bulan</div>
                                <div class="doc-note" style="color:var(--red-500);">Belum diupload</div>
                            </div>
                            <div class="doc-action">
                                <a href="" class="btn-upload">
                                    <i class="bi bi-upload"></i> Upload
                                </a>
                            </div>
                        </div>
                        <div class="doc-item">
                            <div class="doc-icon missing"><i class="bi bi-upload"></i></div>
                            <div class="doc-info">
                                <div class="doc-name">NPWP</div>
                                <div class="doc-note">Opsional · mempercepat proses</div>
                            </div>
                            <div class="doc-action">
                                <a href="" class="btn-upload">
                                    <i class="bi bi-upload"></i> Upload
                                </a>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:20px; padding:14px; background:var(--gold-50); border:1px solid var(--gold-100); border-radius:var(--radius); display:flex; align-items:flex-start; gap:10px;">
                        <i class="bi bi-info-circle" style="color:var(--gold-500); margin-top:2px; flex-shrink:0;"></i>
                        <p style="font-size:12px; color:var(--slate-600); margin:0; line-height:1.6;">
                            Pastikan semua dokumen berformat <strong>PDF atau JPG</strong>, resolusi jelas, dan ukuran maksimal <strong>5 MB</strong> per file.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         REKOMENDASI PROPERTI
    ══════════════════════════════════════════ --}}
    <div>
        <div class="section-label">
            <h6><span class="dot"></span>Rekomendasi Unit PT Pasada Indonesia</h6>
            <a href="">Lihat Semua Unit <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="property-grid">
            {{-- Card 1 --}}
            <div class="prop-card">
                <div class="prop-img">
                    <div class="prop-img-bg" style="background:linear-gradient(135deg,#d4c5a9,#b8a98a);"></div>
                    <span class="prop-badge gold"><i class="bi bi-fire me-1"></i>Best Deal</span>
                    <div class="prop-fav"><i class="bi bi-heart"></i></div>
                </div>
                <div class="prop-body">
                    <div class="prop-name">Pasada Harmoni</div>
                    <div class="prop-type">Tipe 45/120 · Blok A · Kavling tersedia: 8</div>
                    <div class="prop-price">Rp 450.000.000</div>
                    <div class="prop-features">
                        <span class="prop-feat"><i class="bi bi-house-door"></i> 2 KT</span>
                        <span class="prop-feat"><i class="bi bi-droplet"></i> 1 KM</span>
                        <span class="prop-feat"><i class="bi bi-tree"></i> Taman</span>
                        <span class="prop-feat"><i class="bi bi-shield-check"></i> One Gate</span>
                    </div>
                    <div class="prop-actions">
                        <a href="#" class="btn-prop-detail"><i class="bi bi-info-circle"></i> Detail</a>
                        <a href="#" class="btn-prop-sim"><i class="bi bi-calculator"></i> Simulasi</a>
                    </div>
                </div>
            </div>
            {{-- Card 2 --}}
            <div class="prop-card">
                <div class="prop-img">
                    <div class="prop-img-bg" style="background:linear-gradient(135deg,#b8cebc,#93b09a);"></div>
                    <span class="prop-badge teal"><i class="bi bi-stars me-1"></i>Recommended</span>
                    <div class="prop-fav"><i class="bi bi-heart"></i></div>
                </div>
                <div class="prop-body">
                    <div class="prop-name">Pasada Mansion</div>
                    <div class="prop-type">Tipe 60/150 · Blok B · Kavling tersedia: 5</div>
                    <div class="prop-price">Rp 680.000.000</div>
                    <div class="prop-features">
                        <span class="prop-feat"><i class="bi bi-house-door"></i> 3 KT</span>
                        <span class="prop-feat"><i class="bi bi-droplet"></i> 2 KM</span>
                        <span class="prop-feat"><i class="bi bi-tree"></i> Taman</span>
                        <span class="prop-feat"><i class="bi bi-camera-video"></i> CCTV</span>
                    </div>
                    <div class="prop-actions">
                        <a href="#" class="btn-prop-detail"><i class="bi bi-info-circle"></i> Detail</a>
                        <a href="#" class="btn-prop-sim"><i class="bi bi-calculator"></i> Simulasi</a>
                    </div>
                </div>
            </div>
            {{-- Card 3 --}}
            <div class="prop-card">
                <div class="prop-img">
                    <div class="prop-img-bg" style="background:linear-gradient(135deg,#c8bba0,#a89778);"></div>
                    <span class="prop-badge green"><i class="bi bi-tag me-1"></i>Promo Terbatas</span>
                    <div class="prop-fav"><i class="bi bi-heart"></i></div>
                </div>
                <div class="prop-body">
                    <div class="prop-name">Pasada Residence</div>
                    <div class="prop-type">Tipe 36/90 · Blok C · Kavling tersedia: 12</div>
                    <div class="prop-price">Rp 380.000.000</div>
                    <div class="prop-features">
                        <span class="prop-feat"><i class="bi bi-house-door"></i> 2 KT</span>
                        <span class="prop-feat"><i class="bi bi-droplet"></i> 1 KM</span>
                        <span class="prop-feat"><i class="bi bi-tree"></i> Taman</span>
                        <span class="prop-feat"><i class="bi bi-shield-check"></i> One Gate</span>
                    </div>
                    <div class="prop-actions">
                        <a href="#" class="btn-prop-detail"><i class="bi bi-info-circle"></i> Detail</a>
                        <a href="#" class="btn-prop-sim"><i class="bi bi-calculator"></i> Simulasi</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         BOTTOM ROW — Tips + CS
    ══════════════════════════════════════════ --}}
    <div class="bottom-row">
        <div class="tips-card">
            <div class="tips-icon"><i class="bi bi-lightbulb"></i></div>
            <div>
                <div class="tips-title">Tips Mempercepat Persetujuan KPR</div>
                <ul class="tips-list">
                    <li>Lengkapi semua dokumen yang diperlukan sesegera mungkin</li>
                    <li>Pastikan foto/scan dokumen jelas, tidak buram, dan terbaca</li>
                    <li>Isi semua data diri sesuai dengan dokumen resmi (KTP, KK)</li>
                    <li>Rekening koran menunjukkan kondisi keuangan yang stabil</li>
                    <li>Hubungi marketing jika ada pertanyaan seputar persyaratan</li>
                </ul>
            </div>
        </div>

        <div class="cs-card">
            <div class="cs-avatar"><i class="bi bi-headset"></i></div>
            <div>
                <div class="cs-title">Butuh Bantuan?</div>
                <div class="cs-sub">Tim Customer Service PT Pasada Indonesia siap membantu Anda setiap saat.</div>
                <div class="cs-actions">
                    <a href="https://wa.me/6281234567890" class="btn-wa" target="_blank">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                    <a href="tel:02112345678" class="btn-phone">
                        <i class="bi bi-telephone"></i> (021) 1234-5678
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Animasi masuk staggered per card
    const cards = document.querySelectorAll(
        '.stat-card, .card-panel, .prop-card, .tips-card, .cs-card, .welcome-stat-pill'
    );
    cards.forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(16px)';
        el.style.transition = 'opacity .45s ease, transform .45s ease';
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 120 + i * 60);
    });

    // Progress bar animasi width
    document.querySelectorAll('.sp-fill').forEach(bar => {
        const target = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => { bar.style.width = target; }, 500);
    });

    // Favorite toggle
    document.querySelectorAll('.prop-fav').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const icon = this.querySelector('i');
            const active = icon.classList.contains('bi-heart-fill');
            icon.className = active ? 'bi bi-heart' : 'bi bi-heart-fill';
            this.style.color = active ? '' : 'var(--red-500)';
        });
    });
});
</script>
@endpush