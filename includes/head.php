<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root {
    --bg-main: #e2e8f0;
    --app-bg: #f6f8fa;
    --card-bg: rgba(255, 255, 255, 0.95);
    --sidebar-bg: #0d1117;
    --text-dark: #0d1117;
    --text-muted: #8b8e98;
    --blue-accent: #059669; /* Emerald Green */
    --blue-light: #34d399; /* Light Emerald */
    --yellow-accent: #f59e0b;
    --red-accent: #ef4444;
    --radius-lg: 32px;
    --radius-md: 20px;
    --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.04);
}

* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
body { background: var(--bg-main); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
::-webkit-scrollbar { width: 4px; } ::-webkit-scrollbar-thumb { background: #d0d0d0; border-radius: 10px; }

/* APP WINDOW */
.app-window {
    background: var(--app-bg); width: 100%; max-width: 1280px; height: 820px;
    border-radius: 40px; display: flex; padding: 18px; box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    position: relative; overflow: hidden;
}

/* SIDEBAR */
.sidebar {
    width: 70px; background: var(--sidebar-bg); border-radius: 35px;
    display: flex; flex-direction: column; align-items: center; justify-content: space-between;
    padding: 24px 0; color: white; flex-shrink: 0; z-index: 10;
    box-shadow: 4px 0 20px rgba(0,0,0,0.1);
}
.sidebar-logo { width: 40px; height: 40px; background: white; color: var(--sidebar-bg); border-radius: 14px; display: grid; place-items: center; font-size: 1.2rem; margin-bottom: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
.nav-menu { display: flex; flex-direction: column; gap: 20px; width: 100%; align-items: center; }
.nav-item { width: 44px; height: 44px; border-radius: 50%; display: grid; place-items: center; color: #777; font-size: 1.1rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
.nav-item.active { background: white; color: var(--sidebar-bg); box-shadow: 0 4px 15px rgba(255,255,255,0.3); transform: scale(1.05);}
.nav-item:hover:not(.active) { color: white; transform: translateY(-2px); }

/* MAIN CONTENT */
.main-content { flex: 1; padding: 10px 30px; display: flex; flex-direction: column; overflow-y: auto; }

/* TOP HEADER */
.top-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.header-left .sub-brand { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;}
.header-left .sub-brand span { color: var(--text-dark); font-weight: 800; margin-left: 5px; }
.greeting { margin-top: 15px; }
.greeting h1 { font-size: 2.6rem; font-weight: 800; color: var(--text-dark); letter-spacing: -1px; }
.greeting h1 span { color: var(--blue-accent); }
.greeting p { color: var(--text-muted); font-size: 0.95rem; margin-top: 4px; font-weight: 500; }

.header-right { display: flex; align-items: center; gap: 16px; margin-top: 20px;}
.avatars-group { display: flex; align-items: center; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); padding: 6px 12px; border-radius: 30px; gap: -5px; box-shadow: var(--shadow-soft);}
.avatars-group img, .avatars-group .ava-circle { width: 32px; height: 32px; border-radius: 50%; border: 2px solid white; margin-left: -10px; background: #ddd; transition: transform 0.2s;}
.avatars-group .ava-circle:hover { transform: translateY(-3px); z-index: 5;}
.avatars-group .ava-circle:first-child { margin-left: 0; }
.avatars-group i { margin-left: 10px; color: var(--text-muted); font-size: 0.8rem; cursor: pointer; }
.icon-btn { width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border: 1.5px solid rgba(255,255,255,0.5); display: grid; place-items: center; color: var(--text-dark); cursor: pointer; position: relative; transition: all 0.3s; box-shadow: var(--shadow-soft);}
.icon-btn:hover { background: white; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.08);}
.icon-btn::after { content: ''; position: absolute; top: 10px; right: 12px; width: 8px; height: 8px; background: var(--blue-accent); border-radius: 50%; border: 2px solid var(--app-bg); }
.search-bar { display: flex; align-items: center; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border: 1.5px solid rgba(255,255,255,0.5); padding: 0 16px; height: 44px; border-radius: 22px; gap: 8px; width: 180px; transition: all 0.3s; box-shadow: var(--shadow-soft);}
.search-bar:focus-within { width: 220px; background: white; border-color: var(--blue-accent);}
.search-bar i { color: var(--text-muted); }
.search-bar input { border: none; background: transparent; outline: none; width: 100%; font-size: 0.85rem; }
.my-avatar { width: 44px; height: 44px; background: var(--sidebar-bg); border-radius: 50%; color: white; display: grid; place-items: center; font-weight: bold; cursor: pointer; transition: transform 0.2s; box-shadow: 0 4px 10px rgba(0,0,0,0.15);}
.my-avatar:hover { transform: scale(1.05); }

/* BENTO GRID */
.bento-grid { display: grid; grid-template-columns: 1fr 1.3fr 0.8fr; grid-template-rows: auto auto; gap: 20px; }
.card { background: var(--card-bg); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.5); border-radius: var(--radius-lg); padding: 28px; position: relative; box-shadow: var(--shadow-soft); transition: transform 0.3s, box-shadow 0.3s;}
.card:hover { box-shadow: 0 15px 35px rgba(0,0,0,0.06); transform: translateY(-2px);}
.card-title { font-size: 1.05rem; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;}
.card-title i { color: var(--text-muted); cursor: pointer; transition: color 0.2s; }
.card-title i:hover { color: var(--text-dark); }

/* BALANCE STATS */
.balance-card { display: flex; flex-direction: column; justify-content: space-between; background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6)); }
.balance-amount { font-size: 2.4rem; font-weight: 800; color: var(--text-dark); margin-top: 10px; display: flex; align-items: baseline;}
.balance-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; margin-left: 10px; text-transform: uppercase; letter-spacing: 0.5px;}
.chart-mockup { display: flex; align-items: flex-end; gap: 10px; margin-top: 24px; height: 40px; }
.chart-mockup .line-wave { flex: 1; height: 30px; border-bottom: 3px solid var(--blue-accent); border-radius: 50%; position: relative;}
.chart-mockup .badge-up { position: absolute; top: -18px; right: -5px; background: white; border: 1px solid #eee; font-size: 0.75rem; padding: 4px 8px; border-radius: 12px; font-weight: 700; box-shadow: 0 4px 10px rgba(0,0,0,0.08); color: #4caf50; display: flex; align-items: center; gap: 4px;}
.chart-mockup .bars { display: flex; gap: 6px; align-items: flex-end; height: 100%; }
.chart-mockup .bar { width: 14px; background: var(--blue-accent); border-radius: 10px; transition: height 0.5s ease; opacity: 0.8;}
.chart-mockup .bar:hover { opacity: 1; transform: scaleY(1.05); }
.months-label { display: flex; justify-content: flex-end; gap: 10px; font-size: 0.7rem; color: var(--text-muted); margin-top: 8px; font-weight: 700; text-transform: uppercase;}

/* BANK CARD */
.bank-card-container { background: linear-gradient(135deg, #a8bce6, #809cdd); border-radius: var(--radius-lg); padding: 28px; color: white; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 15px 30px rgba(128, 156, 221, 0.4); position: relative; overflow: hidden; transition: transform 0.3s;}
.bank-card-container:hover { transform: translateY(-5px); }
.bank-card-container::before { content: ''; position: absolute; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -50px; right: -50px;}
.bc-top { font-size: 0.8rem; font-weight: 700; letter-spacing: 1.5px; opacity: 0.9; text-transform: uppercase; z-index: 1;}
.bc-chip { width: 45px; height: 32px; background: linear-gradient(135deg, #e5cc8a, #d4b568); border-radius: 8px; margin: 24px 0 15px; position: relative; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 1;}
.bc-number { font-size: 1.35rem; font-weight: 600; letter-spacing: 4px; font-family: monospace; text-align: right; text-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 1;}
.bc-bottom { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 15px; z-index: 1;}
.bc-name { font-size: 1.15rem; font-weight: 700; margin-top: 5px; letter-spacing: 1px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);}
.bc-dates { font-size: 0.75rem; opacity: 0.9; letter-spacing: 1.5px; font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,0.1);}
.mc-circles { display: flex; }
.mc-circles div { width: 28px; height: 28px; border-radius: 50%; }
.mc-circles .red { background: #ea4335; opacity: 0.9; }
.mc-circles .yellow { background: #fbbc04; margin-left: -12px; opacity: 0.9; mix-blend-mode: multiply;}

/* ANALYTICS */
.analytics-card { display: flex; flex-direction: column; justify-content: space-between;}
.analytics-card .legend { list-style: none; font-size: 0.8rem; font-weight: 700; color: var(--text-dark); margin-bottom: 20px;}
.analytics-card .legend li { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.analytics-card .legend .dot { width: 10px; height: 10px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.1);}
.donut-container { position: relative; height: 120px; display: flex; justify-content: center; align-items: center; }
.donut-inner { position: absolute; text-align: center; }
.donut-inner h3 { font-size: 1.4rem; font-weight: 800; color: var(--text-dark);}
.donut-inner span { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;}

/* TRANSACTIONS */
.trx-card { grid-column: 1 / 3; }
.trx-list { display: flex; flex-direction: column; gap: 18px; max-height: 280px; overflow-y: auto; padding-right: 10px;}
.trx-item { display: flex; align-items: center; justify-content: space-between; padding: 10px; border-radius: 16px; transition: background 0.2s;}
.trx-item:hover { background: rgba(0,0,0,0.02); }
.trx-left { display: flex; align-items: center; gap: 16px; }
.trx-icon { width: 48px; height: 48px; border-radius: 16px; display: grid; place-items: center; font-size: 1.3rem; background: rgba(255,255,255,0.8); color: var(--sidebar-bg); box-shadow: 0 4px 10px rgba(0,0,0,0.05);}
.trx-icon.income { background: linear-gradient(135deg, var(--blue-light), var(--blue-accent)); color: white; }
.trx-icon.expense { background: linear-gradient(135deg, #333, #111); color: white; }
.trx-info h4 { font-size: 1.05rem; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;}
.trx-info p { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; }
.trx-right { display: flex; align-items: center; gap: 16px; }
.trx-amount { font-size: 1.1rem; font-weight: 800; color: var(--text-dark); }
.trx-amount.inc { color: #4caf50; }
.trx-action { color: var(--text-muted); cursor: pointer; background: none; border: none; padding: 8px; border-radius: 50%; transition: background 0.2s;}
.trx-action:hover { background: rgba(0,0,0,0.05); color: var(--red-accent);}

/* EXPENSES & INCOME */
.right-stack { display: flex; flex-direction: column; gap: 20px; grid-column: 3 / 4; }
.ei-stats { display: flex; justify-content: space-between; margin-bottom: 16px; }
.ei-stat h2 { font-size: 1.6rem; font-weight: 800; color: var(--text-dark); }
.ei-stat span { font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;}
.ei-bars { display: flex; gap: 8px; height: 18px; border-radius: 9px; overflow: hidden; background: rgba(0,0,0,0.05);}
.ei-bar { height: 100%; transition: width 0.5s ease; }

/* PREMIUM / ADD TRANSACTION BUTTON */
.premium-card { background: linear-gradient(135deg, var(--sidebar-bg), #3a3a3c); border-radius: var(--radius-lg); padding: 24px; color: white; display: flex; align-items: center; justify-content: space-between; gap: 16px; cursor: pointer; transition: all 0.3s; box-shadow: 0 15px 30px rgba(0,0,0,0.15);}
.premium-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
.premium-icon { font-size: 2.2rem; color: var(--yellow-accent); text-shadow: 0 4px 10px rgba(0,0,0,0.2);}
.premium-text h4 { font-size: 1rem; font-weight: 800; margin-bottom: 6px;}
.premium-text p { font-size: 0.75rem; color: #ccc; line-height: 1.4; font-weight: 500;}
.premium-btn { background: white; color: var(--sidebar-bg); border: none; padding: 10px 18px; border-radius: 24px; font-weight: 800; font-size: 0.8rem; cursor: pointer; white-space: nowrap; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 10px rgba(255,255,255,0.2);}
.premium-btn:hover { transform: scale(1.05); box-shadow: 0 6px 15px rgba(255,255,255,0.3);}

/* MODAL ADD TRANSACTION */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); display: flex; justify-content: center; align-items: center; z-index: 1000; opacity: 0; pointer-events: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.modal-overlay.show { opacity: 1; pointer-events: all; }
.modal-box { background: white; padding: 35px; border-radius: 30px; width: 420px; max-width: 90%; transform: scale(0.95) translateY(20px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 30px 60px rgba(0,0,0,0.15);}
.modal-overlay.show .modal-box { transform: scale(1) translateY(0); }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.modal-header h3 { font-size: 1.4rem; font-weight: 800; color: var(--text-dark);}
.close-btn { background: rgba(0,0,0,0.05); border: none; width: 36px; height: 36px; border-radius: 50%; font-size: 1.1rem; cursor: pointer; color: var(--text-dark); transition: background 0.2s; display: grid; place-items: center;}
.close-btn:hover { background: rgba(0,0,0,0.1); }
.form-group { margin-bottom: 18px; }
.form-group label { display: block; font-size: 0.85rem; font-weight: 800; margin-bottom: 8px; color: var(--text-dark); }
.form-group input, .form-group select { width: 100%; padding: 14px 16px; border: 2px solid #eee; border-radius: 16px; font-family: inherit; font-size: 1rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s; background: #fafafa;}
.form-group input:focus, .form-group select:focus { border-color: var(--blue-accent); background: white; box-shadow: 0 0 0 4px rgba(142, 165, 219, 0.1);}
.type-toggle { display: flex; gap: 12px; margin-bottom: 25px; }
.type-btn { flex: 1; padding: 12px; border: 2px solid #eee; background: white; border-radius: 16px; cursor: pointer; font-weight: 800; color: var(--text-muted); transition: all 0.2s; text-align: center; font-size: 0.95rem;}
.type-btn.active.inc { background: var(--blue-light); border-color: var(--blue-accent); color: var(--sidebar-bg); box-shadow: 0 4px 15px rgba(142, 165, 219, 0.3);}
.type-btn.active.exp { background: var(--sidebar-bg); border-color: var(--sidebar-bg); color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.2);}
.submit-btn { width: 100%; padding: 16px; background: var(--blue-accent); color: white; border: none; border-radius: 16px; font-weight: 800; font-size: 1.05rem; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; margin-top: 10px;}
.submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.15);}
.submit-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; box-shadow: none;}

#toast { position: fixed; bottom: 30px; left: 50%; transform: translate(-50%, 100px); background: #1c1c1e; color: white; padding: 14px 28px; border-radius: 30px; font-size: 0.95rem; font-weight: 700; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1001; box-shadow: 0 15px 30px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px;}
#toast.show { transform: translate(-50%, 0); }
#toast i { color: #4caf50; font-size: 1.2rem;}

/* OTHER PAGES (History, Wallet, Settings) */
.page-title-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;}
.page-title-header h2 { font-size: 2.2rem; font-weight: 800; color: var(--text-dark); letter-spacing: -0.5px;}
.page-title-header p { font-size: 0.95rem; color: var(--text-muted); font-weight: 500; margin-top: 5px;}

/* Empty State */
.empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); }
.empty-state i { font-size: 3rem; margin-bottom: 15px; opacity: 0.5; }
.empty-state p { font-size: 0.95rem; font-weight: 600; }

/* WALLET CARD */
.wallet-card-container { display: flex; flex-direction: column; justify-content: space-between; height: 100%;}
.wallet-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 10px; }
.wallet-item { display: flex; align-items: center; gap: 12px; background: rgba(0,0,0,0.03); padding: 12px; border-radius: 16px; transition: transform 0.2s, background 0.2s;}
.wallet-item:hover { transform: translateY(-2px); background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.05);}
.wallet-icon { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
.wallet-info { display: flex; flex-direction: column; }
.wallet-name { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;}
/* ═══════════════════════════════════════════
   RESPONSIVE BREAKPOINTS (DASHBOARD)
═══════════════════════════════════════════ */

/* ── Tablet & Small Desktop (max 1024px) ── */
@media (max-width: 1024px) {
    .bento-grid {
        grid-template-columns: 1fr 1fr;
    }
    .right-stack {
        grid-column: 1 / 3;
        flex-direction: row;
    }
    .right-stack .card, .premium-card {
        flex: 1;
    }
    .trx-card {
        grid-column: 1 / 3;
    }
}

/* ── Tablet Portrait & Mobile Landscape (max 768px) ── */
@media (max-width: 768px) {
    body { padding: 0; background: var(--app-bg); }
    .app-window {
        height: auto;
        min-height: 100vh;
        border-radius: 0;
        padding: 0 0 80px 0; /* Space for bottom nav */
        flex-direction: column;
        box-shadow: none;
    }
    
    /* SIDEBAR BECOMES BOTTOM NAV */
    .sidebar {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        width: 100%; height: 75px;
        flex-direction: row;
        border-radius: 24px 24px 0 0;
        padding: 0 15px;
        z-index: 1000;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.1);
        align-items: center;
        justify-content: space-between;
    }
    .sidebar-top { 
        display: flex; flex: 1; 
        flex-direction: row; align-items: center; 
    }
    .sidebar-logo { display: none; }
    .nav-menu { 
        flex-direction: row; 
        width: 100%; 
        justify-content: space-around; 
        gap: 5px; 
    }
    .sidebar > .nav-menu:last-child { 
        width: auto;
        justify-content: flex-end;
    }
    
    .main-content {
        padding: 20px 15px;
        overflow-y: visible;
    }
    
    .top-header {
        flex-direction: column;
        gap: 20px;
    }
    .header-right {
        width: 100%;
        justify-content: space-between;
        gap: 12px;
    }
    .search-bar { flex: 1; width: auto; }
    .search-bar:focus-within { width: auto; }
    .avatars-group { display: none; }
    
    .bento-grid {
        grid-template-columns: 1fr;
    }
    .trx-card, .right-stack {
        grid-column: 1 / 2;
    }
    .right-stack {
        flex-direction: column;
    }
    .wallet-grid {
        grid-template-columns: 1fr 1fr;
    }
}

/* ── Mobile Portrait (max 480px) ── */
@media (max-width: 480px) {
    .greeting h1 { font-size: 2.2rem; }
    .balance-amount { font-size: 2.2rem; flex-wrap: wrap; }
    .balance-label { width: 100%; margin-left: 0; margin-top: 5px; }
    
    .modal-box { padding: 25px 20px; width: 95%; max-height: 90vh; overflow-y: auto; }
    .type-toggle { flex-direction: column; }
    
    .ei-stats { flex-direction: column; gap: 10px; text-align: left; }
    .ei-stat { display: flex; align-items: center; gap: 10px; }
    
    .wallet-grid { grid-template-columns: 1fr; }
    
    /* Adjust trx item text size for small screen */
    .trx-info h4 { font-size: 0.95rem; }
    .trx-amount { font-size: 1rem; }
    .trx-icon { width: 40px; height: 40px; font-size: 1.1rem; }
    
    /* Other Pages Adjustments */
    .history-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    .history-container { padding: 20px; }
    .card-list { padding: 20px; }
    .settings-container { padding: 20px; }
    .setting-item { flex-direction: column; align-items: flex-start; gap: 10px; }
}
</style>
