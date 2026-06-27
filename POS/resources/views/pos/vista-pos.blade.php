<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Don Pulpo POS</title>
  <style>
    :root {
      --navy-950: #04152c; --navy-900: #061b36; --navy-800: #08294c;
      --aqua-600: #03bfc5; --aqua-500: #09d1d0; --aqua-100: #dffbfb;
      --coral-600: #ff6048; --coral-500: #ff735f;
      --amber-500: #ffd34d; --blue-600: #087ccb;
      --ink-900: #102033; --ink-700: #314057; --ink-500: #667085;
      --line: #dfe7ef; --surface: #ffffff; --surface-soft: #f6f9fc;
      --shadow: 0 18px 45px rgba(4,21,44,.10);
      --radius-xl: 26px; --radius-lg: 20px; --radius-md: 16px;
      --sidebar-w: 320px;
    }
    * { box-sizing: border-box; }
    html { height: 100%; -webkit-text-size-adjust: 100%; }
    body {
      margin: 0; min-height: 100%;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;
      color: var(--ink-900);
      background: radial-gradient(circle at 8% 5%, rgba(9,209,208,.20), transparent 28rem),
                  radial-gradient(circle at 88% 0%, rgba(255,96,72,.13), transparent 24rem),
                  linear-gradient(180deg, #f8fbff 0%, #eef5f8 100%);
      font-size: 17px; overflow-x: hidden;
    }
    button, input, textarea, select { font: inherit; }
    button { border: 0; touch-action: manipulation; user-select: none; -webkit-tap-highlight-color: transparent; }

    /* ── Sidebar toggle ── */
    .sidebar-toggle {
      position: fixed; top: max(16px,env(safe-area-inset-top)); left: max(16px,env(safe-area-inset-left));
      z-index: 70; width: 62px; height: 62px; display: grid; place-items: center;
      border-radius: 20px; background: linear-gradient(135deg,var(--navy-900),var(--navy-800));
      color: #fff; box-shadow: 0 16px 36px rgba(4,21,44,.25); cursor: pointer;
      transition: transform .18s ease;
    }
    .sidebar-toggle:active { transform: scale(.96); }
    .hamburger { width: 26px; display: grid; gap: 6px; }
    .hamburger span { height: 3px; border-radius: 999px; background: var(--aqua-500); transition: transform .2s ease, opacity .2s ease; }
    body.sidebar-open .hamburger span:nth-child(1) { transform: translateY(9px) rotate(45deg); }
    body.sidebar-open .hamburger span:nth-child(2) { opacity: 0; }
    body.sidebar-open .hamburger span:nth-child(3) { transform: translateY(-9px) rotate(-45deg); }

    .backdrop { position: fixed; inset: 0; z-index: 55; background: rgba(2,10,22,.42); backdrop-filter: blur(6px); opacity: 0; pointer-events: none; transition: opacity .22s ease; }
    body.sidebar-open .backdrop { opacity: 1; pointer-events: auto; }

    /* ── Sidebar ── */
    .sidebar {
      position: fixed; inset: 0 auto 0 0; z-index: 60;
      width: min(var(--sidebar-w),calc(100vw - 22px)); padding: 24px 18px 18px;
      background: radial-gradient(circle at 50% 5%, rgba(9,209,208,.22), transparent 13rem),
                  linear-gradient(180deg,var(--navy-950) 0%,#031024 100%);
      color: #fff; transform: translateX(calc(-100% - 8px)); transition: transform .25s ease;
      box-shadow: 25px 0 55px rgba(4,21,44,.26); display: flex; flex-direction: column; overflow-y: auto;
    }
    body.sidebar-open .sidebar { transform: translateX(0); }
    .brand { min-height: 112px; display: grid; place-items: center; text-align: center; margin-bottom: 16px; padding-top: 8px; }
    .brand-octo { width: 64px; height: 64px; display: grid; place-items: center; margin: 0 auto 4px; border-radius: 24px; background: rgba(9,209,208,.16); color: var(--aqua-500); font-size: 42px; box-shadow: inset 0 0 0 1px rgba(9,209,208,.28); }
    .brand h1 { margin: 0; font-size: 30px; letter-spacing: .05em; line-height: 1; }
    .brand p  { margin: 7px 0 0; color: var(--aqua-500); font-size: 13px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .nav-list { display: grid; gap: 10px; margin: 8px 0 auto; }
    .nav-link { min-height: 58px; display: flex; align-items: center; gap: 14px; padding: 0 16px; border-radius: 17px; color: rgba(255,255,255,.88); background: transparent; cursor: pointer; text-align: left; font-weight: 760; font-size: 16px; text-decoration: none; }
    .nav-link.active, .nav-link:hover { color: #fff; background: linear-gradient(135deg,rgba(3,191,197,.96),rgba(5,123,160,.92)); box-shadow: 0 12px 24px rgba(3,191,197,.22); }
    .nav-icon { width: 32px; height: 32px; display: grid; place-items: center; font-size: 21px; }
    .sidebar-art { min-height: 100px; margin: 18px 2px; border-radius: 24px; background: radial-gradient(circle at 20% 78%,rgba(255,96,72,.32),transparent 3.8rem), linear-gradient(160deg,rgba(255,255,255,.06),rgba(255,255,255,.02)); border: 1px solid rgba(255,255,255,.08); position: relative; overflow: hidden; }
    .sidebar-art::before { content: "〰️ 🐙 〰️"; position: absolute; inset: auto 0 20px; text-align: center; font-size: 44px; opacity: .55; }
    .user-card { min-height: 88px; display: flex; align-items: center; gap: 14px; padding: 14px; border-radius: 24px; background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.10); }
    .avatar { width: 54px; height: 54px; border-radius: 19px; display: grid; place-items: center; background: var(--aqua-500); color: var(--navy-950); font-weight: 900; }
    .user-card strong { display: block; font-size: 15px; }
    .user-card span   { display: block; color: rgba(255,255,255,.72); font-size: 13px; line-height: 1.45; }

    /* ── Layout principal ── */
    .app-shell { min-height: 100svh; padding: 18px 18px 22px; }
    .pos-page { width: min(1880px,100%); margin: 0 auto; }
    .topbar { min-height: 76px; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 8px 10px 8px 74px; }
    .branch { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .branch-badge { width: 48px; height: 48px; display: grid; place-items: center; flex: 0 0 auto; border-radius: 18px; background: var(--surface); box-shadow: var(--shadow); font-size: 26px; }
    .branch-text small  { display: block; color: var(--ink-500); font-weight: 750; font-size: 13px; }
    .branch-text strong { display: block; font-size: clamp(18px,2vw,23px); }
    .top-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .status-pill { min-height: 52px; border-radius: 18px; color: #027e85; background: var(--aqua-100); border: 1px solid rgba(9,209,208,.22); display: inline-flex; align-items: center; gap: 10px; padding: 0 17px; font-weight: 820; }
    .dot { width: 10px; height: 10px; border-radius: 50%; background: var(--aqua-600); box-shadow: 0 0 0 5px rgba(3,191,197,.12); }
    .time-chip { min-height: 52px; display: flex; align-items: center; padding: 0 16px; color: var(--ink-700); font-weight: 750; white-space: nowrap; }

    /* ── KPIs ── */
    .kpis { display: grid; grid-template-columns: repeat(4,minmax(180px,1fr)); gap: 14px; margin: 0 0 14px; }
    .kpi-card { min-height: 120px; display: flex; align-items: center; gap: 16px; padding: 18px; border-radius: var(--radius-lg); background: rgba(255,255,255,.90); border: 1px solid rgba(223,231,239,.78); box-shadow: var(--shadow); }
    .kpi-icon { width: 64px; height: 64px; flex: 0 0 auto; display: grid; place-items: center; border-radius: 23px; font-size: 30px; background: linear-gradient(135deg,var(--aqua-500),var(--aqua-600)); color: #fff; }
    .kpi-icon.coral { background: linear-gradient(135deg,var(--coral-500),var(--coral-600)); }
    .kpi-icon.blue  { background: linear-gradient(135deg,#12a9e9,#0577c8); }
    .kpi-icon.amber { background: linear-gradient(135deg,#ffd34d,#f59e0b); color: #92400e; }
    .kpi-card p      { margin: 0 0 4px; color: var(--ink-500); font-size: 14px; font-weight: 780; }
    .kpi-card strong { display: block; font-size: clamp(21px,2.25vw,29px); letter-spacing: -.035em; }

    /* ── Workspace ── */
    .workspace { display: grid; grid-template-columns: minmax(0,1fr) 440px; gap: 16px; align-items: start; }
    .panel { background: rgba(255,255,255,.92); border: 1px solid rgba(223,231,239,.82); border-radius: var(--radius-xl); box-shadow: var(--shadow); }
    .products-panel { padding: 16px; min-width: 0; }

    .tools-row { display: grid; grid-template-columns: minmax(240px,1fr) 120px; gap: 12px; margin-bottom: 14px; }
    .search-box { min-height: 62px; border-radius: 19px; border: 1px solid var(--line); background: var(--surface); display: flex; align-items: center; gap: 12px; padding: 0 16px; font-weight: 780; box-shadow: 0 8px 20px rgba(4,21,44,.045); }
    .search-box input { width: 100%; border: 0; outline: 0; color: var(--ink-900); background: transparent; font-size: 18px; }
    .search-box input::placeholder { color: #98a2b3; }
    .view-toggle { min-height: 62px; border-radius: 19px; border: 1px solid var(--line); background: var(--surface); display: flex; padding: 6px; gap: 6px; box-shadow: 0 8px 20px rgba(4,21,44,.045); }
    .view-toggle button { flex: 1; min-height: 50px; border-radius: 15px; background: transparent; color: var(--ink-500); font-size: 22px; cursor: pointer; }
    .view-toggle button.active { color: var(--aqua-600); background: var(--aqua-100); }

    .category-row { display: flex; gap: 10px; overflow-x: auto; padding: 2px 2px 14px; scrollbar-width: thin; scroll-snap-type: x proximity; }
    .cat-btn { min-height: 54px; padding: 0 18px; flex: 0 0 auto; border-radius: 17px; background: var(--surface); border: 1px solid var(--line); color: var(--ink-700); font-weight: 850; cursor: pointer; box-shadow: 0 7px 18px rgba(4,21,44,.04); scroll-snap-align: start; }
    .cat-btn.active { background: linear-gradient(135deg,var(--aqua-500),var(--aqua-600)); border-color: transparent; color: #fff; box-shadow: 0 12px 26px rgba(3,191,197,.25); }
    .cat-btn:active { transform: scale(.96); }

    .product-grid { display: grid; grid-template-columns: repeat(4,minmax(185px,1fr)); gap: 14px; }
    .product-card { min-height: 256px; border-radius: 21px; overflow: hidden; background: var(--surface); border: 1px solid rgba(223,231,239,.88); box-shadow: 0 10px 24px rgba(4,21,44,.06); display: flex; flex-direction: column; transition: transform .16s ease; }
    .product-card:active { transform: scale(.985); }
    .food-image { min-height: 118px; display: grid; place-items: center; color: #fff; font-size: 58px; background: linear-gradient(135deg,#035168,#09b6bb 52%,#ff745f); overflow: hidden; }
    .food-image img { width: 100%; height: 118px; object-fit: cover; }
    .product-body { display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: end; padding: 15px; flex: 1; }
    .product-name { margin: 0; font-size: 18px; line-height: 1.18; letter-spacing: -.02em; }
    .product-cat  { margin: 5px 0 12px; color: var(--ink-500); font-size: 13px; font-weight: 650; }
    .price { font-size: 18px; font-weight: 900; letter-spacing: -.025em; }
    .add-btn { width: 60px; height: 60px; border-radius: 20px; background: linear-gradient(135deg,var(--aqua-500),var(--aqua-600)); color: #fff; display: grid; place-items: center; font-size: 30px; cursor: pointer; box-shadow: 0 12px 25px rgba(3,191,197,.26); }
    .add-btn:active { transform: scale(.96); }

    /* ── Order panel ── */
    .order-panel { position: sticky; top: 16px; overflow: hidden; }
    .order-head { padding: 18px 18px 14px; border-bottom: 1px solid var(--line); }
    .order-head-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
    .order-head h2 { margin: 0 0 4px; font-size: 22px; letter-spacing: -.035em; }
    .order-head p  { margin: 0; color: var(--ink-500); font-weight: 770; font-size: 14px; }
    .orders-btn { min-height: 46px; padding: 0 14px; border-radius: 14px; background: var(--surface-soft); border: 1px solid var(--line); color: var(--ink-700); font-weight: 820; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; white-space: nowrap; }
    .orders-btn:active { transform: scale(.96); }

    .cart-list { padding: 10px 14px 2px; display: grid; gap: 4px; max-height: min(380px,42svh); overflow-y: auto; }
    .cart-empty { padding: 24px 14px; color: var(--ink-500); text-align: center; font-weight: 760; }
    .cart-item { min-height: 100px; display: grid; grid-template-columns: 56px minmax(0,1fr); gap: 12px; padding: 10px 6px; border-bottom: 1px solid rgba(223,231,239,.72); }
    .cart-thumb { width: 56px; height: 56px; border-radius: 16px; display: grid; place-items: center; color: #fff; font-size: 26px; background: linear-gradient(135deg,var(--navy-800),var(--aqua-600)); }
    .cart-main { min-width: 0; display: grid; gap: 8px; }
    .cart-title-row { display: flex; justify-content: space-between; gap: 10px; align-items: start; }
    .cart-title strong { display: block; font-size: 15px; line-height: 1.18; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cart-title span   { display: block; color: var(--ink-500); font-size: 13px; margin-top: 3px; }
    .trash-btn { width: 44px; height: 44px; flex: 0 0 auto; border-radius: 14px; background: #fff3f1; color: var(--coral-600); font-size: 20px; cursor: pointer; }
    .trash-btn:active { transform: scale(.96); }
    .cart-controls { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .qty-group { min-height: 48px; display: inline-flex; align-items: center; gap: 4px; padding: 4px; border-radius: 16px; border: 1px solid var(--line); background: #fff; }
    .qty-btn { width: 44px; height: 40px; border-radius: 12px; background: var(--surface-soft); color: var(--ink-900); cursor: pointer; font-size: 22px; font-weight: 900; }
    .qty-btn:active { transform: scale(.96); }
    .qty-number { min-width: 30px; text-align: center; font-weight: 900; font-size: 17px; }
    .line-total { font-weight: 900; font-size: 15px; white-space: nowrap; }

    /* ── Totals ── */
    .totals { padding: 12px 18px 16px; display: grid; gap: 10px; }
    .total-line { display: flex; justify-content: space-between; gap: 12px; color: var(--ink-700); font-weight: 780; }
    .total-line strong { color: var(--ink-900); }
    .iva-label { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
    .iva-toggle { width: 20px; height: 20px; accent-color: var(--aqua-600); cursor: pointer; }

    .tip-box p { margin: 2px 0 8px; color: var(--ink-700); font-weight: 820; font-size: 15px; }
    .tip-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; }
    .tip-btn { min-height: 46px; border-radius: 14px; background: var(--surface); border: 1px solid var(--line); color: var(--ink-700); font-weight: 900; cursor: pointer; font-size: 14px; }
    .tip-btn.active { background: linear-gradient(135deg,var(--aqua-500),var(--aqua-600)); color: #fff; border-color: transparent; box-shadow: 0 8px 18px rgba(3,191,197,.22); }
    .tip-btn:active { transform: scale(.96); }

    .grand-total { margin-top: 2px; padding-top: 12px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; gap: 10px; font-size: 19px; font-weight: 900; }
    .grand-total strong { font-size: 28px; letter-spacing: -.05em; }

    .notes { min-height: 80px; resize: none; width: 100%; border: 1px solid var(--line); border-radius: 16px; padding: 14px; outline: 0; color: var(--ink-900); background: #fff; font-size: 15px; }
    .notes:focus { border-color: var(--aqua-600); }

    .order-actions { display: grid; grid-template-columns: 1fr 1.35fr; gap: 10px; padding: 0 18px 18px; }
    .outline-btn, .pay-btn { min-height: 60px; border-radius: 18px; cursor: pointer; font-weight: 900; display: inline-flex; justify-content: center; align-items: center; gap: 8px; font-size: 15px; }
    .outline-btn { background: #fff; color: var(--aqua-600); border: 2px solid var(--aqua-500); }
    .pay-btn     { background: linear-gradient(135deg,var(--aqua-500),var(--aqua-600)); color: #fff; box-shadow: 0 12px 28px rgba(3,191,197,.26); }
    .pay-btn:active, .outline-btn:active { transform: scale(.96); }

    /* ── Toast ── */
    .toast-bar { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(100px); z-index: 300; padding: 14px 24px; border-radius: 16px; font-weight: 800; font-size: 16px; background: var(--navy-900); color: #fff; box-shadow: 0 20px 40px rgba(4,21,44,.30); transition: transform .3s ease, opacity .3s ease; opacity: 0; white-space: nowrap; pointer-events: none; }
    .toast-bar.show    { transform: translateX(-50%) translateY(0); opacity: 1; }
    .toast-bar.success { background: #065f46; }
    .toast-bar.error   { background: #991b1b; }

    /* ── Modal overlay ── */
    .modal-overlay { position: fixed; inset: 0; z-index: 200; background: rgba(2,10,22,.55); backdrop-filter: blur(6px); display: none; place-items: center; }
    .modal-overlay.open { display: grid; }
    .modal-box { background: #fff; border-radius: 28px; padding: 28px 28px 24px; width: min(480px,95vw); box-shadow: 0 32px 72px rgba(4,21,44,.22); max-height: 90svh; overflow-y: auto; }
    .modal-box h3 { margin: 0 0 20px; font-size: 22px; letter-spacing: -.03em; }
    .modal-box .field { margin-bottom: 14px; }
    .modal-box label { display: block; font-size: 14px; font-weight: 760; color: var(--ink-700); margin-bottom: 6px; }
    .modal-box input, .modal-box select, .modal-box textarea { width: 100%; border: 1.5px solid var(--line); border-radius: 14px; padding: 12px 14px; font-size: 16px; outline: 0; color: var(--ink-900); }
    .modal-box input:focus, .modal-box select:focus, .modal-box textarea:focus { border-color: var(--aqua-600); }
    .modal-actions { display: grid; grid-template-columns: 1fr 1.4fr; gap: 10px; margin-top: 20px; }
    .modal-cancel { min-height: 52px; border-radius: 15px; border: 1.5px solid var(--line); background: #fff; color: var(--ink-700); font-weight: 800; cursor: pointer; }
    .modal-confirm { min-height: 52px; border-radius: 15px; background: linear-gradient(135deg,var(--aqua-500),var(--aqua-600)); color: #fff; font-weight: 900; cursor: pointer; border: none; }
    .modal-confirm:active, .modal-cancel:active { transform: scale(.97); }

    /* ── Active orders modal ── */
    .orders-modal-box { width: min(640px,96vw); }
    .order-card { border: 1.5px solid var(--line); border-radius: 18px; padding: 16px 18px; margin-bottom: 10px; cursor: pointer; transition: background .15s, border-color .15s; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
    .order-card:hover  { background: var(--aqua-100); border-color: var(--aqua-500); }
    .order-card:active { transform: scale(.985); }
    .order-card-info strong { display: block; font-size: 16px; }
    .order-card-info span  { color: var(--ink-500); font-size: 13px; font-weight: 650; }
    .order-card-total { font-size: 20px; font-weight: 900; letter-spacing: -.03em; color: var(--ink-900); white-space: nowrap; }
    .order-card-badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 800; background: #fef3c7; color: #92400e; }
    .orders-empty { text-align: center; padding: 32px; color: var(--ink-500); font-weight: 760; }

    /* Mesa badge en orden activa */
    .order-mesa-badge { display: inline-flex; align-items: center; gap: 6px; background: var(--aqua-100); color: var(--aqua-600); border-radius: 10px; padding: 3px 10px; font-size: 13px; font-weight: 800; }

    /* ── Modal de pago múltiple ── */
    .pay-modal-box { width: min(540px,96vw); }
    .pay-modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
    .pay-modal-header h3 { margin: 0; }
    .pay-total-chip { text-align: right; }
    .pay-total-chip span   { display: block; font-size: 12px; color: var(--ink-500); font-weight: 780; }
    .pay-total-chip strong { font-size: 28px; font-weight: 900; letter-spacing: -.04em; color: var(--ink-900); }
    .pay-section-label { font-size: 12px; font-weight: 840; color: var(--ink-500); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 10px; }
    .pay-row { display: grid; grid-template-columns: 1fr 1fr 40px; gap: 10px; margin-bottom: 10px; align-items: center; }
    .pay-method-sel, .pay-amount-inp { border: 1.5px solid var(--line); border-radius: 14px; padding: 12px 14px; color: var(--ink-900); outline: 0; width: 100%; background: #fff; font-size: 15px; }
    .pay-method-sel:focus, .pay-amount-inp:focus { border-color: var(--aqua-600); }
    .pay-row-del { width: 40px; height: 40px; border-radius: 12px; background: #fff3f1; color: var(--coral-600); font-size: 15px; cursor: pointer; border: none; display: grid; place-items: center; }
    .pay-row-del:active { transform: scale(.94); }
    .add-pay-row-btn { width: 100%; min-height: 48px; border-radius: 14px; border: 1.5px dashed var(--aqua-500); background: var(--aqua-100); color: var(--aqua-600); font-weight: 820; cursor: pointer; margin-bottom: 18px; font-size: 15px; }
    .add-pay-row-btn:active { transform: scale(.98); }
    .pay-summary-box { border: 1.5px solid var(--line); border-radius: 18px; padding: 14px 18px; display: grid; gap: 10px; background: var(--surface-soft); }
    .pay-summary-line { display: flex; justify-content: space-between; font-weight: 790; color: var(--ink-700); font-size: 15px; }
    .pay-summary-line.pending strong { color: var(--coral-600); }
    .pay-summary-line.change  strong { color: #065f46; }
    .modal-confirm:disabled { opacity: .45; cursor: not-allowed; transform: none; }

    /* ── Responsive ── */
    @media (max-width: 1450px) { .product-grid { grid-template-columns: repeat(3,minmax(190px,1fr)); } .kpis { grid-template-columns: repeat(2,minmax(220px,1fr)); } }
    @media (max-width: 1180px) { .workspace { grid-template-columns: 1fr; } .order-panel { position: static; } .cart-list { max-height: none; } .product-grid { grid-template-columns: repeat(3,minmax(180px,1fr)); } }
    @media (max-width: 820px) {
      body { font-size: 16px; } .app-shell { padding: 10px 10px 18px; }
      .topbar { flex-direction: column; align-items: flex-start; padding-top: 76px; padding-left: 4px; }
      .top-actions { width: 100%; }
      .kpis { grid-template-columns: 1fr; gap: 10px; }
      .tools-row { grid-template-columns: 1fr; }
      .product-grid { grid-template-columns: repeat(2,minmax(0,1fr)); gap: 10px; }
      .add-btn { width: 100%; height: 54px; border-radius: 16px; }
      .product-body { grid-template-columns: 1fr; padding: 12px; }
      .order-actions { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) { .product-grid { grid-template-columns: 1fr; } .tip-row { grid-template-columns: repeat(2,1fr); } .grand-total strong { font-size: 24px; } }
  </style>
</head>
<body>
  <!-- Sidebar toggle -->
  <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Menú" aria-expanded="false">
    <span class="hamburger" aria-hidden="true"><span></span><span></span><span></span></span>
  </button>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-octo">🐙</div>
      <div><h1>DON PULPO</h1><p>Mariscos &amp; Más</p></div>
    </div>
    <nav class="nav-list">
      <a href="{{ route('pos') }}"       class="nav-link active"><span class="nav-icon">🛒</span>Punto de Venta</a>
      <a href="{{ route('dashboard') }}" class="nav-link"><span class="nav-icon">📈</span>Dashboard</a>
      <a href="{{ route('home') }}"      class="nav-link"><span class="nav-icon">🏠</span>Inicio</a>
    </nav>
    <div class="sidebar-art" aria-hidden="true"></div>
    <div class="user-card">
      <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
      <div>
        <strong>{{ Auth::user()->name }}</strong>
        <span>{{ ucfirst(Auth::user()->role) }}</span>
        <span>
          <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;color:rgba(255,255,255,.6);padding:0;cursor:pointer;font-size:13px">Cerrar sesión</button>
          </form>
        </span>
      </div>
    </div>
  </aside>

  <div class="backdrop" id="backdrop"></div>
  <div class="toast-bar" id="toastBar"></div>

  <!-- ══════════════════════════════════════════
       MODAL: Guardar orden (pedir mesa)
  ═══════════════════════════════════════════ -->
  <div class="modal-overlay" id="tableModal">
    <div class="modal-box">
      <h3>🍽️ Guardar orden</h3>
      <div class="field">
        <label for="tableNameInput">Mesa / Ubicación <span style="color:var(--coral-600)">*</span></label>
        <input id="tableNameInput" type="text" placeholder="Ej: Mesa 5, Barra 2, Para llevar..." />
      </div>
      <div class="field">
        <label for="customerNameInput">Nombre del cliente (opcional)</label>
        <input id="customerNameInput" type="text" placeholder="Ej: Juan García" />
      </div>
      <div class="field">
        <label for="saveNotesInput">Notas (opcional)</label>
        <textarea id="saveNotesInput" rows="2" placeholder="Instrucciones especiales..."></textarea>
      </div>
      <div class="modal-actions">
        <button class="modal-cancel" id="cancelTableModal">Cancelar</button>
        <button class="modal-confirm" id="confirmSaveOrder">💾 Guardar orden</button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════
       MODAL: Órdenes activas
  ═══════════════════════════════════════════ -->
  <div class="modal-overlay" id="ordersModal">
    <div class="modal-box orders-modal-box">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
        <h3 style="margin:0">🧾 Órdenes activas</h3>
        <button class="modal-cancel" style="min-height:40px;padding:0 16px" id="closeOrdersModal">Cerrar</button>
      </div>
      <div id="activeOrdersList">
        <div class="orders-empty">Cargando...</div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════
       MODAL: Cobrar orden (multi-pago)
  ═══════════════════════════════════════════ -->
  <div class="modal-overlay" id="payModal">
    <div class="modal-box pay-modal-box">
      <div class="pay-modal-header">
        <h3>💳 Cobrar orden</h3>
        <div class="pay-total-chip">
          <span>Total a cobrar</span>
          <strong id="payModalTotal">$0.00</strong>
        </div>
      </div>

      <div class="pay-section-label">Métodos de pago</div>
      <div id="paymentRows"></div>

      <button class="add-pay-row-btn" id="addPaymentRow" type="button">
        + Agregar método de pago
      </button>

      <div class="pay-summary-box">
        <div class="pay-summary-line">
          <span>Total ingresado</span>
          <strong id="payEntered">$0.00</strong>
        </div>
        <div class="pay-summary-line pending" id="payPendingRow">
          <span>⏳ Pendiente</span>
          <strong id="payPending">$0.00</strong>
        </div>
        <div class="pay-summary-line change" id="payChangeRow" style="display:none">
          <span>💵 Cambio</span>
          <strong id="payChange">$0.00</strong>
        </div>
      </div>

      <div class="modal-actions">
        <button class="modal-cancel" id="cancelPayModal">Cancelar</button>
        <button class="modal-confirm" id="confirmPay" disabled>✅ Confirmar pago</button>
      </div>
    </div>
  </div>

  <main class="app-shell">
    <section class="pos-page">
      <header class="topbar">
        <div class="branch">
          <div class="branch-badge">🐙</div>
          <div class="branch-text">
            <small>Sucursal</small>
            <strong>Don Pulpo Centro</strong>
          </div>
        </div>
        <div class="top-actions">
          <div class="status-pill"><span class="dot"></span>POS Activo</div>
          <div class="time-chip" id="clockChip"></div>
        </div>
      </header>

      <!-- KPIs -->
      <section class="kpis" aria-label="Resumen del día">
        <article class="kpi-card">
          <div class="kpi-icon">↗</div>
          <div><p>Ventas del día</p><strong id="kpiSales">$0.00</strong></div>
        </article>
        <article class="kpi-card">
          <div class="kpi-icon amber">🟡</div>
          <div><p>Órdenes abiertas</p><strong id="kpiOpen">0</strong></div>
        </article>
        <article class="kpi-card">
          <div class="kpi-icon coral">✅</div>
          <div><p>Órdenes pagadas hoy</p><strong id="kpiPaid">0</strong></div>
        </article>
        <article class="kpi-card">
          <div class="kpi-icon blue">💳</div>
          <div><p>Ticket promedio</p><strong id="kpiAvg">$0.00</strong></div>
        </article>
      </section>

      <section class="workspace">
        <!-- Panel catálogo -->
        <div>
          <section class="panel products-panel">
            <div class="tools-row">
              <label class="search-box">
                🔎
                <input id="searchInput" type="search" placeholder="Buscar platillos..." autocomplete="off" />
              </label>
              <div class="view-toggle">
                <button class="active" id="viewGrid" type="button">▦</button>
                <button id="viewList" type="button">☰</button>
              </div>
            </div>
            <div class="category-row" id="categoryRow"></div>
            <div class="product-grid" id="productGrid">
              <div class="cart-empty" style="grid-column:1/-1">Cargando menú...</div>
            </div>
          </section>
        </div>

        <!-- Panel orden -->
        <aside class="panel order-panel">
          <div class="order-head">
            <div class="order-head-row">
              <div>
                <h2 id="orderTitle">Nueva orden</h2>
                <p id="orderMeta">Sin ítems · <span id="orderMesaBadge"></span></p>
              </div>
              <button class="orders-btn" id="openOrdersBtn" type="button">🧾 Órdenes activas</button>
            </div>
          </div>

          <div class="cart-list" id="cartList">
            <div class="cart-empty">La orden está vacía. Toca + para agregar platillos.</div>
          </div>

          <div class="totals">
            <div class="total-line"><span>Subtotal</span><strong id="subtotalText">$0.00</strong></div>
            <div class="total-line">
              <span>
                <label class="iva-label" title="Activar / desactivar IVA 16%">
                  <input type="checkbox" class="iva-toggle" id="ivaToggle" />
                  IVA (16%)
                </label>
              </span>
              <strong id="taxText">$0.00</strong>
            </div>
            <div class="total-line"><span>Propina</span><strong id="tipText">$0.00</strong></div>

            <div class="tip-box">
              <p>Propina sugerida</p>
              <div class="tip-row" id="tipRow">
                <button class="tip-btn" data-tip="0.10" type="button">10%</button>
                <button class="tip-btn" data-tip="0.15" type="button">15%</button>
                <button class="tip-btn" data-tip="0.20" type="button">20%</button>
                <button class="tip-btn active" data-tip="0" type="button">Sin propina</button>
              </div>
            </div>

            <div class="grand-total"><span>Total</span><strong id="totalText">$0.00</strong></div>
          </div>

          <div class="order-actions">
            <button class="outline-btn" id="saveOrderBtn" type="button">💾 Guardar</button>
            <button class="pay-btn" id="payBtn" type="button">💳 Cobrar $0.00</button>
          </div>
        </aside>
      </section>
    </section>
  </main>

  <script>
    // ─────────────────────────────────────────────────────────────
    //  Utilidades
    // ─────────────────────────────────────────────────────────────
    const money = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
    const csrf  = document.querySelector('meta[name="csrf-token"]').content;

    async function api(method, url, body = null) {
      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: body ? JSON.stringify(body) : null,
      });
      return res.json();
    }

    function toast(msg, type = '', duration = 3500) {
      const el = document.getElementById('toastBar');
      el.textContent = msg;
      el.className   = 'toast-bar show ' + type;
      clearTimeout(el._t);
      el._t = setTimeout(() => { el.className = 'toast-bar'; }, duration);
    }

    function updateClock() {
      document.getElementById('clockChip').textContent =
        new Date().toLocaleString('es-MX', { weekday:'short', day:'numeric', month:'short', hour:'2-digit', minute:'2-digit' });
    }
    updateClock(); setInterval(updateClock, 30000);

    // Modales
    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    // ─────────────────────────────────────────────────────────────
    //  Estado
    // ─────────────────────────────────────────────────────────────
    const state = {
      categories:    [],
      allDishes:     [],
      visibleDishes: [],
      activeCategory: null,
      search:        '',
      tipPercent:    0,
      includeIva:    true,
      cart:          new Map(),   // key → { key, name, price, qty, dishId }
      savedOrderId:  null,
      orderNumber:   null,
      tableName:     '',
    };

    // ─────────────────────────────────────────────────────────────
    //  Sidebar
    // ─────────────────────────────────────────────────────────────
    const sidebarToggle = document.getElementById('sidebarToggle');
    const backdrop      = document.getElementById('backdrop');
    function setSidebar(open) {
      document.body.classList.toggle('sidebar-open', open);
      sidebarToggle.setAttribute('aria-expanded', String(open));
    }
    sidebarToggle.addEventListener('click', () => setSidebar(!document.body.classList.contains('sidebar-open')));
    backdrop.addEventListener('click', () => setSidebar(false));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { setSidebar(false); closeModal('tableModal'); closeModal('ordersModal'); closeModal('payModal'); } });

    // ─────────────────────────────────────────────────────────────
    //  Cargar menú
    // ─────────────────────────────────────────────────────────────
    async function loadMenu() {
      try {
        const json = await api('GET', '/api/v1/dish-categories?with_dishes=1');
        if (!json.success) { toast('Error cargando el menú', 'error'); return; }
        state.categories = json.data;
        state.allDishes  = json.data.flatMap(c => c.dishes.map(d => ({ ...d, category_name: c.name })));
        if (state.categories.length) state.activeCategory = state.categories[0].id;
        filterDishes(); renderCategories(); renderProducts();
      } catch(e) { toast('Sin conexión con el servidor', 'error'); }
    }

    async function loadKpis() {
      try {
        const json = await api('GET', '/api/v1/dashboard/summary');
        if (!json.success) return;
        const d = json.data;
        document.getElementById('kpiSales').textContent = money.format(d.sales_today);
        document.getElementById('kpiOpen').textContent  = d.open_orders;
        document.getElementById('kpiPaid').textContent  = d.paid_orders;
        document.getElementById('kpiAvg').textContent   = money.format(d.average_ticket);
      } catch {}
    }

    // ─────────────────────────────────────────────────────────────
    //  Categorías
    // ─────────────────────────────────────────────────────────────
    function renderCategories() {
      const cats = [{ id: 'all', name: '▦ Todos' }, ...state.categories];
      document.getElementById('categoryRow').innerHTML = cats.map(c => `
        <button class="cat-btn ${state.activeCategory === c.id ? 'active' : ''}" data-category="${c.id}" type="button">${c.name}</button>
      `).join('');
    }

    document.getElementById('categoryRow').addEventListener('click', e => {
      const btn = e.target.closest('[data-category]');
      if (!btn) return;
      const raw = btn.dataset.category;
      state.activeCategory = raw === 'all' ? 'all' : Number(raw);
      filterDishes(); renderCategories(); renderProducts();
    });

    function filterDishes() {
      const term = state.search.trim().toLowerCase();
      state.visibleDishes = state.allDishes.filter(d => {
        const byCat    = state.activeCategory === 'all' || d.dish_category_id === state.activeCategory;
        const bySearch = !term || d.name.toLowerCase().includes(term);
        return byCat && bySearch;
      });
    }

    document.getElementById('searchInput').addEventListener('input', e => {
      state.search = e.target.value; filterDishes(); renderProducts();
    });

    // ─────────────────────────────────────────────────────────────
    //  Productos
    // ─────────────────────────────────────────────────────────────
    function renderProducts() {
      const grid = document.getElementById('productGrid');
      if (!state.visibleDishes.length) {
        grid.innerHTML = `<div class="cart-empty" style="grid-column:1/-1">No se encontraron platillos.</div>`; return;
      }
      grid.innerHTML = state.visibleDishes.map(d => `
        <article class="product-card">
          <div class="food-image">
            ${d.image_path ? `<img src="${d.image_path}" alt="${d.name}" />` : '<span>🍽️</span>'}
          </div>
          <div class="product-body">
            <div>
              <h3 class="product-name">${d.name}</h3>
              <p class="product-cat">${d.category_name ?? ''}</p>
              <div class="price">${money.format(d.price)}</div>
            </div>
            <button class="add-btn" data-add="${d.id}" type="button" aria-label="Agregar ${d.name}">+</button>
          </div>
        </article>`).join('');
    }

    document.getElementById('productGrid').addEventListener('click', e => {
      const btn = e.target.closest('[data-add]');
      if (!btn) return;
      const dish = state.allDishes.find(d => d.id === Number(btn.dataset.add));
      if (dish) addToCart(dish);
    });

    // ─────────────────────────────────────────────────────────────
    //  Carrito
    // ─────────────────────────────────────────────────────────────
    function addToCart(dish) {
      const key = dish.id;
      const existing = state.cart.get(key);
      state.cart.set(key, existing
        ? { ...existing, qty: existing.qty + 1 }
        : { key, name: dish.name, price: parseFloat(dish.price), qty: 1, dishId: dish.id });
      invalidateSavedOrder();
      renderCart();
    }

    function changeQty(key, delta) {
      const entry = state.cart.get(key);
      if (!entry) return;
      const newQty = entry.qty + delta;
      if (newQty <= 0) state.cart.delete(key);
      else state.cart.set(key, { ...entry, qty: newQty });
      invalidateSavedOrder();
      renderCart();
    }

    function removeFromCart(key) {
      state.cart.delete(key);
      invalidateSavedOrder();
      renderCart();
    }

    function invalidateSavedOrder() {
      // Si el carrito cambia después de guardar, la orden guardada ya no es válida
      // Solo lo invalidamos si el carrito fue modificado manualmente, no al cargar una orden
    }

    function renderCart() {
      const items    = [...state.cart.values()];
      const cartList = document.getElementById('cartList');

      if (!items.length) {
        cartList.innerHTML = `<div class="cart-empty">La orden está vacía. Toca + para agregar platillos.</div>`;
        updateTotals(0); return;
      }

      cartList.innerHTML = items.map(({ key, name, price, qty }) => `
        <article class="cart-item">
          <div class="cart-thumb">🍽️</div>
          <div class="cart-main">
            <div class="cart-title-row">
              <div class="cart-title">
                <strong>${name}</strong>
                <span>${money.format(price)} c/u</span>
              </div>
              <button class="trash-btn" data-remove="${key}" type="button">🗑</button>
            </div>
            <div class="cart-controls">
              <div class="qty-group">
                <button class="qty-btn" data-dec="${key}" type="button">−</button>
                <span class="qty-number">${qty}</span>
                <button class="qty-btn" data-inc="${key}" type="button">+</button>
              </div>
              <div class="line-total">${money.format(price * qty)}</div>
            </div>
          </div>
        </article>`).join('');

      const subtotal = items.reduce((s, { price, qty }) => s + price * qty, 0);
      updateTotals(subtotal);
    }

    document.getElementById('cartList').addEventListener('click', e => {
      const inc    = e.target.closest('[data-inc]');
      const dec    = e.target.closest('[data-dec]');
      const remove = e.target.closest('[data-remove]');
      if (inc)    changeQty(Number(inc.dataset.inc),  +1);
      if (dec)    changeQty(Number(dec.dataset.dec),  -1);
      if (remove) removeFromCart(Number(remove.dataset.remove));
    });

    // ─────────────────────────────────────────────────────────────
    //  Totales (con IVA opcional)
    // ─────────────────────────────────────────────────────────────
    function updateTotals(subtotal) {
      const ivaActive = document.getElementById('ivaToggle').checked;
      const tax       = ivaActive ? subtotal * 0.16 : 0;
      const tip       = subtotal * state.tipPercent;
      const total     = subtotal + tax + tip;

      document.getElementById('subtotalText').textContent = money.format(subtotal);
      document.getElementById('taxText').textContent      = money.format(tax);
      document.getElementById('tipText').textContent      = money.format(tip);
      document.getElementById('totalText').textContent    = money.format(total);
      document.getElementById('payBtn').textContent       = `💳 Cobrar ${money.format(total)}`;
    }

    document.getElementById('ivaToggle').addEventListener('change', () => {
      const items    = [...state.cart.values()];
      const subtotal = items.reduce((s, { price, qty }) => s + price * qty, 0);
      updateTotals(subtotal);
    });

    document.getElementById('tipRow').addEventListener('click', e => {
      const btn = e.target.closest('[data-tip]');
      if (!btn) return;
      state.tipPercent = Number(btn.dataset.tip);
      document.querySelectorAll('.tip-btn').forEach(b => b.classList.toggle('active', b === btn));
      const items    = [...state.cart.values()];
      const subtotal = items.reduce((s, { price, qty }) => s + price * qty, 0);
      updateTotals(subtotal);
    });

    // ─────────────────────────────────────────────────────────────
    //  Helpers de payload
    // ─────────────────────────────────────────────────────────────
    function buildItems() {
      return [...state.cart.values()].map(({ dishId, name, price, qty }) => ({
        dish_id:       dishId ?? null,
        name_snapshot: name,
        unit_price:    price,
        quantity:      qty,
      }));
    }

    function currentTotals() {
      const items    = [...state.cart.values()];
      const subtotal = items.reduce((s, { price, qty }) => s + price * qty, 0);
      const ivaActive = document.getElementById('ivaToggle').checked;
      const tax      = ivaActive ? Math.round(subtotal * 0.16 * 100) / 100 : 0;
      const tip      = Math.round(subtotal * state.tipPercent * 100) / 100;
      return { subtotal, tax, tip, total: subtotal + tax + tip };
    }

    function resetCart() {
      state.cart.clear();
      state.savedOrderId = null;
      state.orderNumber  = null;
      state.tableName    = '';
      document.getElementById('orderTitle').textContent = 'Nueva orden';
      document.getElementById('orderMeta').innerHTML    = 'Sin ítems · <span id="orderMesaBadge"></span>';
      renderCart();
    }

    // ─────────────────────────────────────────────────────────────
    //  MODAL: Guardar orden (pedir mesa)
    // ─────────────────────────────────────────────────────────────
    document.getElementById('saveOrderBtn').addEventListener('click', () => {
      if (state.cart.size === 0) { toast('Agrega al menos un platillo', 'error'); return; }
      // Prellenar si ya hay mesa guardada
      document.getElementById('tableNameInput').value    = state.tableName || '';
      document.getElementById('customerNameInput').value = '';
      document.getElementById('saveNotesInput').value    = '';
      openModal('tableModal');
      setTimeout(() => document.getElementById('tableNameInput').focus(), 120);
    });

    document.getElementById('cancelTableModal').addEventListener('click', () => closeModal('tableModal'));

    document.getElementById('confirmSaveOrder').addEventListener('click', async () => {
      const tableName    = document.getElementById('tableNameInput').value.trim();
      const customerName = document.getElementById('customerNameInput').value.trim();
      const notes        = document.getElementById('saveNotesInput').value.trim();

      if (!tableName) {
        document.getElementById('tableNameInput').focus();
        toast('Indica el número o nombre de la mesa', 'error'); return;
      }

      const btn = document.getElementById('confirmSaveOrder');
      btn.textContent = 'Guardando...'; btn.disabled = true;

      try {
        const totals  = currentTotals();
        const payload = {
          items:         buildItems(),
          table_name:    tableName,
          customer_name: customerName || null,
          notes:         notes || null,
          ...totals,
        };

        const json = await api('POST', '/api/v1/orders', payload);

        if (json.success) {
          state.savedOrderId = json.data.id;
          state.orderNumber  = json.data.order_number;
          state.tableName    = tableName;
          closeModal('tableModal');
          toast(`✅ Orden guardada — ${tableName}`, 'success');
          resetCart();
          loadKpis();
        } else {
          toast(json.message || 'Error guardando la orden', 'error');
        }
      } catch(e) {
        toast('Error de conexión', 'error');
      } finally {
        btn.textContent = '💾 Guardar orden'; btn.disabled = false;
      }
    });

    // ─────────────────────────────────────────────────────────────
    //  MODAL: Órdenes activas
    // ─────────────────────────────────────────────────────────────
    document.getElementById('openOrdersBtn').addEventListener('click', async () => {
      openModal('ordersModal');
      await loadActiveOrders();
    });
    document.getElementById('closeOrdersModal').addEventListener('click', () => closeModal('ordersModal'));

    async function loadActiveOrders() {
      const list = document.getElementById('activeOrdersList');
      list.innerHTML = '<div class="orders-empty">Cargando...</div>';
      try {
        const json = await api('GET', '/api/v1/orders?status=open');
        if (!json.success) { list.innerHTML = '<div class="orders-empty">Error al cargar órdenes.</div>'; return; }

        const orders = json.data.data ?? json.data; // soporta paginado y array simple

        if (!orders.length) {
          list.innerHTML = '<div class="orders-empty">🎉 No hay órdenes abiertas en este momento.</div>'; return;
        }

        list.innerHTML = orders.map(o => `
          <div class="order-card" data-order-id="${o.id}">
            <div class="order-card-info">
              <strong>${o.order_number ?? `#${o.id}`}</strong>
              <span>
                ${o.table_name ? `🍽️ ${o.table_name}` : 'Sin mesa'}
                ${o.customer_name ? ` · ${o.customer_name}` : ''}
                · ${(o.items ?? []).length} ítems
                · <span class="order-card-badge">Abierta</span>
              </span>
            </div>
            <div class="order-card-total">${money.format(o.total)}</div>
          </div>
        `).join('');
      } catch(e) {
        list.innerHTML = '<div class="orders-empty">Error de conexión.</div>';
      }
    }

    document.getElementById('activeOrdersList').addEventListener('click', async e => {
      const card = e.target.closest('[data-order-id]');
      if (!card) return;
      const orderId = Number(card.dataset.orderId);
      await loadOrderIntoCart(orderId);
      closeModal('ordersModal');
    });

    async function loadOrderIntoCart(orderId) {
      try {
        const json = await api('GET', `/api/v1/orders/${orderId}`);
        if (!json.success) { toast('No se pudo cargar la orden', 'error'); return; }

        const order = json.data;
        state.cart.clear();
        state.savedOrderId = order.id;
        state.orderNumber  = order.order_number;
        state.tableName    = order.table_name ?? '';

        for (const item of (order.items ?? [])) {
          const key   = item.dish_id ?? -(item.id);  // clave por dish_id o negativa por ítem
          const price = parseFloat(item.unit_price);
          const qty   = parseInt(item.quantity);
          const existing = state.cart.get(key);
          if (existing) {
            state.cart.set(key, { ...existing, qty: existing.qty + qty });
          } else {
            state.cart.set(key, {
              key,
              name:   item.name_snapshot,
              price,
              qty,
              dishId: item.dish_id ?? null,
            });
          }
        }

        // Actualizar header de la orden
        document.getElementById('orderTitle').textContent = `Orden ${order.order_number ?? '#' + order.id}`;
        const mesa = order.table_name ? `🍽️ ${order.table_name}` : 'Sin mesa';
        document.getElementById('orderMeta').innerHTML = `${mesa} · <span id="orderMesaBadge"></span>`;

        renderCart();
        toast(`✅ Orden cargada — ${mesa}`, 'success');
      } catch(e) {
        toast('Error cargando la orden', 'error');
      }
    }

    // ─────────────────────────────────────────────────────────────
    //  MODAL: Cobrar (multi-pago)
    // ─────────────────────────────────────────────────────────────
    let payRowCounter = 0;
    let payOrderTotal = 0;

    function addPayRow(method = 'cash', amount = '') {
      const id  = payRowCounter++;
      const row = document.createElement('div');
      row.className   = 'pay-row';
      row.dataset.row = id;
      row.innerHTML   = `
        <select class="pay-method-sel" data-row="${id}">
          <option value="cash"     ${method === 'cash'     ? 'selected' : ''}>💵 Efectivo</option>
          <option value="card"     ${method === 'card'     ? 'selected' : ''}>💳 Tarjeta</option>
          <option value="transfer" ${method === 'transfer' ? 'selected' : ''}>📲 Transferencia</option>
        </select>
        <input class="pay-amount-inp" type="number" min="0.01" step="0.01"
               placeholder="0.00" value="${amount !== '' ? Number(amount).toFixed(2) : ''}" data-row="${id}" />
        <button class="pay-row-del" data-del="${id}" type="button">✕</button>
      `;
      document.getElementById('paymentRows').appendChild(row);
      updatePaySummary();
    }

    function getPayRows() {
      return [...document.querySelectorAll('.pay-row')].map(row => ({
        method: row.querySelector('.pay-method-sel').value,
        amount: parseFloat(row.querySelector('.pay-amount-inp').value) || 0,
      }));
    }

    function updatePaySummary() {
      const rows    = getPayRows();
      const entered = rows.reduce((s, r) => s + r.amount, 0);
      const pending = Math.max(0, payOrderTotal - entered);
      const change  = Math.max(0, entered - payOrderTotal);
      const ready   = entered >= payOrderTotal - 0.001 && rows.some(r => r.amount > 0);

      document.getElementById('payEntered').textContent        = money.format(entered);
      document.getElementById('payPending').textContent        = money.format(pending);
      document.getElementById('payChange').textContent         = money.format(change);
      document.getElementById('payPendingRow').style.display   = pending  > 0.001 ? '' : 'none';
      document.getElementById('payChangeRow').style.display    = change   > 0.001 ? '' : 'none';
      document.getElementById('confirmPay').disabled           = !ready;
    }

    function openPayModal() {
      if (state.cart.size === 0) { toast('Agrega al menos un platillo', 'error'); return; }
      const totals  = currentTotals();
      payOrderTotal = totals.total;
      document.getElementById('payModalTotal').textContent = money.format(payOrderTotal);

      // Limpiar filas anteriores y agregar una por defecto (efectivo, total completo)
      payRowCounter = 0;
      document.getElementById('paymentRows').innerHTML = '';
      addPayRow('cash', payOrderTotal.toFixed(2));

      updatePaySummary();
      openModal('payModal');
      setTimeout(() => { const inp = document.querySelector('.pay-amount-inp'); if (inp) inp.select(); }, 140);
    }

    document.getElementById('payBtn').addEventListener('click', openPayModal);

    document.getElementById('addPaymentRow').addEventListener('click', () => {
      const entered   = getPayRows().reduce((s, r) => s + r.amount, 0);
      const remaining = Math.max(0, payOrderTotal - entered);
      addPayRow('card', remaining > 0.001 ? remaining.toFixed(2) : '');
      setTimeout(() => {
        const inputs = document.querySelectorAll('.pay-amount-inp');
        if (inputs.length) inputs[inputs.length - 1].select();
      }, 60);
    });

    document.getElementById('paymentRows').addEventListener('input', e => {
      if (e.target.matches('.pay-amount-inp, .pay-method-sel')) updatePaySummary();
    });

    document.getElementById('paymentRows').addEventListener('click', e => {
      const del = e.target.closest('[data-del]');
      if (!del) return;
      const rows = document.querySelectorAll('.pay-row');
      if (rows.length <= 1) { toast('Debe haber al menos un método de pago', 'error'); return; }
      del.closest('.pay-row').remove();
      updatePaySummary();
    });

    document.getElementById('cancelPayModal').addEventListener('click', () => closeModal('payModal'));

    document.getElementById('confirmPay').addEventListener('click', async () => {
      const payRows = getPayRows().filter(r => r.amount > 0);
      if (!payRows.length) { toast('Agrega al menos un método de pago', 'error'); return; }

      const entered = payRows.reduce((s, r) => s + r.amount, 0);
      if (entered < payOrderTotal - 0.001) {
        toast(`Faltan ${money.format(payOrderTotal - entered)} para completar el pago`, 'error'); return;
      }

      const btn = document.getElementById('confirmPay');
      btn.textContent = 'Procesando...'; btn.disabled = true;

      try {
        // Crear orden si no existe
        let orderId = state.savedOrderId;
        if (!orderId) {
          const totals  = currentTotals();
          const payload = { items: buildItems(), ...totals, table_name: state.tableName || null, notes: null };
          const cj      = await api('POST', '/api/v1/orders', payload);
          if (!cj.success) { toast(cj.message || 'Error creando la orden', 'error'); return; }
          orderId            = cj.data.id;
          state.savedOrderId = orderId;
        }

        const payJson = await api('POST', `/api/v1/orders/${orderId}/pay`, {
          payments: payRows.map(r => ({ method: r.method, amount: r.amount })),
        });

        if (payJson.success) {
          const cambio = parseFloat(payJson.data.change_amount ?? 0);
          closeModal('payModal');
          toast(cambio > 0.001
            ? `✅ Pago registrado — Cambio: ${money.format(cambio)}`
            : '✅ Pago registrado correctamente.', 'success', 5000);
          resetCart();
          loadKpis();
        } else {
          toast(payJson.message || 'Error al procesar el pago', 'error');
        }
      } catch {
        toast('Error de conexión', 'error');
      } finally {
        btn.textContent = '✅ Confirmar pago'; btn.disabled = false;
      }
    });

    // ─────────────────────────────────────────────────────────────
    //  Vista toggle
    // ─────────────────────────────────────────────────────────────
    document.getElementById('viewGrid').addEventListener('click', () => {
      document.getElementById('productGrid').style.gridTemplateColumns = '';
      document.getElementById('viewGrid').classList.add('active');
      document.getElementById('viewList').classList.remove('active');
    });
    document.getElementById('viewList').addEventListener('click', () => {
      document.getElementById('productGrid').style.gridTemplateColumns = '1fr';
      document.getElementById('viewList').classList.add('active');
      document.getElementById('viewGrid').classList.remove('active');
    });

    // ─────────────────────────────────────────────────────────────
    //  Init
    // ─────────────────────────────────────────────────────────────
    loadMenu();
    loadKpis();
    setInterval(loadKpis, 60000);
  </script>
</body>
</html>
