<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Don Pulpo · Mesero</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <style>
    :root {
      --navy-dark:  #020E1F;
      --navy-deep:  #062645;
      --gold:       #CB8317;
      --gold-dark:  #8E601C;
      --gold-100:   #fdf3e3;
      --surface:    #ffffff;
      --surface-soft: #f2f6f9;
      --ink-900:    #1a2535;
      --ink-500:    #4F6C81;
      --line:       #c8d9e5;
      --radius-lg:  20px;
      --radius-md:  16px;
      --radius-sm:  12px;
    }
    * { box-sizing: border-box; }
    html { height: 100%; -webkit-text-size-adjust: 100%; }
    body {
      margin: 0; min-height: 100%;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;
      color: var(--ink-900);
      background: #f2f6f9;
      font-size: 16px;
      overflow-x: hidden;
    }
    button { border: 0; touch-action: manipulation; user-select: none; -webkit-tap-highlight-color: transparent; cursor: pointer; }
    input, select, textarea { font: inherit; }

    /* ── Header ─────────────────────────────────────── */
    .app-header {
      position: sticky; top: 0; z-index: 50;
      background: linear-gradient(135deg, var(--navy-dark), var(--navy-deep));
      padding: max(14px, env(safe-area-inset-top)) 20px 14px;
      display: flex; align-items: center; justify-content: space-between;
      gap: 12px;
    }
    .header-brand-row { display: flex; align-items: center; gap: 10px; }
    .header-logo  { width: 34px; height: 34px; object-fit: contain; flex: 0 0 auto; }
    .header-brand { color: var(--gold); font-weight: 800; font-size: 20px; letter-spacing: -.5px; line-height: 1; }
    .header-sub   { color: rgba(255,255,255,.55); font-size: 12px; font-weight: 500; margin-top: 2px; }
    .active-badge {
      background: var(--gold);
      color: var(--navy-dark);
      font-weight: 800; font-size: 13px;
      padding: 6px 14px; border-radius: 999px;
      white-space: nowrap; flex-shrink: 0;
    }
    .btn-logout {
      background: rgba(255,255,255,.10); color: rgba(255,255,255,.70);
      border-radius: var(--radius-sm); padding: 8px 14px;
      font-size: 13px; font-weight: 600;
    }

    /* ── Search ─────────────────────────────────────── */
    .search-wrap {
      background: var(--surface); border-bottom: 1px solid var(--line);
      padding: 12px 16px;
    }
    .search-input {
      width: 100%; padding: 13px 18px;
      border: 1.5px solid var(--line); border-radius: 14px;
      font-size: 16px; background: var(--surface-soft);
      outline: none; transition: border-color .15s;
    }
    .search-input:focus { border-color: var(--gold); }

    /* ── Category strip ─────────────────────────────── */
    .cat-bar {
      display: flex; gap: 10px; overflow-x: auto;
      padding: 12px 16px;
      scrollbar-width: none;
      background: var(--surface); border-bottom: 1px solid var(--line);
      position: sticky; top: 65px; z-index: 40;
    }
    .cat-bar::-webkit-scrollbar { display: none; }
    .cat-btn {
      flex-shrink: 0; padding: 11px 20px;
      border-radius: 999px; font-size: 14px; font-weight: 700;
      background: var(--surface-soft); color: var(--ink-500);
      transition: background .15s, color .15s;
      min-height: 44px;
    }
    .cat-btn.active { background: var(--navy-dark); color: var(--gold); }

    /* ── Product list (full-width cards) ────────────── */
    .content-area { padding-bottom: 110px; }
    .product-grid {
      display: flex; flex-direction: column;
      gap: 10px; padding: 14px 16px;
    }

    .product-card {
      background: var(--surface); border-radius: var(--radius-md);
      padding: 14px 16px;
      box-shadow: 0 2px 10px rgba(1,19,46,.07);
      display: flex; align-items: center; gap: 14px;
      width: 100%;
    }
    .product-thumb {
      width: 76px; height: 76px; border-radius: 14px;
      object-fit: cover; flex-shrink: 0;
    }
    .product-thumb-placeholder {
      width: 76px; height: 76px; border-radius: 14px;
      background: var(--surface-soft); flex-shrink: 0;
      display: grid; place-items: center; font-size: 30px;
    }
    .product-info    { flex: 1; min-width: 0; }
    .product-name    { font-weight: 700; font-size: 16px; line-height: 1.3; }
    .product-price   { color: var(--gold-dark); font-weight: 800; font-size: 17px; margin-top: 4px; }
    .product-in-cart-label { font-size: 12px; color: var(--ink-500); font-weight: 600; margin-top: 3px; }
    .product-actions { display: flex; flex-direction: column; gap: 8px; align-items: stretch; flex-shrink: 0; }
    .btn-view-dish {
      background: var(--surface-soft); color: var(--ink-500);
      border: 1.5px solid var(--line); border-radius: 10px;
      padding: 8px 14px; font-size: 13px; font-weight: 700; min-height: 38px;
      text-align: center;
    }
    .btn-view-dish:active { opacity: .75; }
    .product-add-btn {
      background: var(--navy-dark); color: var(--gold);
      border-radius: var(--radius-sm); font-weight: 700; font-size: 14px;
      padding: 10px 16px; min-height: 46px; white-space: nowrap; text-align: center;
    }
    .product-add-btn.in-cart { background: var(--gold); color: var(--navy-dark); }
    .product-add-btn:active { opacity: .85; }

    /* ── Dish photo modal ───────────────────────────── */
    .dish-modal {
      position: fixed; inset: 0; z-index: 150;
      display: flex; align-items: flex-end; justify-content: center;
      background: rgba(1,14,31,.65); backdrop-filter: blur(4px);
      opacity: 0; pointer-events: none;
      transition: opacity .22s ease;
    }
    .dish-modal.open { opacity: 1; pointer-events: auto; }
    .dish-modal-card {
      background: var(--surface); border-radius: 28px 28px 0 0;
      width: 100%; max-width: 540px; overflow: hidden;
      transform: translateY(40px);
      transition: transform .26s cubic-bezier(.22,.61,.36,1);
      max-height: 90vh; display: flex; flex-direction: column;
    }
    .dish-modal.open .dish-modal-card { transform: translateY(0); }
    .dish-modal-img {
      width: 100%; max-height: 280px; object-fit: cover; flex-shrink: 0;
    }
    .dish-modal-img-placeholder {
      width: 100%; height: 180px; background: var(--surface-soft);
      display: grid; place-items: center; font-size: 72px; flex-shrink: 0;
    }
    .dish-modal-body { padding: 20px 20px 12px; overflow-y: auto; flex: 1; }
    .dish-modal-name  { font-weight: 800; font-size: 22px; line-height: 1.2; margin-bottom: 6px; }
    .dish-modal-price { color: var(--gold-dark); font-weight: 800; font-size: 20px; margin-bottom: 10px; }
    .dish-modal-desc  { color: var(--ink-500); font-size: 15px; line-height: 1.55; }
    .dish-modal-footer {
      padding: 16px 20px max(20px, env(safe-area-inset-bottom));
      border-top: 1px solid var(--line); display: flex; gap: 12px; flex-shrink: 0;
    }
    .btn-modal-close {
      flex: 1; padding: 16px; border-radius: 16px;
      background: var(--surface-soft); color: var(--ink-900);
      font-weight: 700; font-size: 16px; min-height: 56px;
    }
    .btn-modal-add {
      flex: 2; padding: 16px; border-radius: 16px;
      background: linear-gradient(135deg, var(--navy-dark), var(--navy-deep));
      color: var(--gold); font-weight: 800; font-size: 16px; min-height: 56px;
    }
    .btn-modal-close:active, .btn-modal-add:active { opacity: .85; }

    /* ── Cart bottom bar ────────────────────────────── */
    .cart-bar {
      position: fixed; bottom: 0; left: 0; right: 0; z-index: 60;
      padding: 12px 16px max(16px, env(safe-area-inset-bottom));
      background: var(--surface); border-top: 1px solid var(--line);
      box-shadow: 0 -4px 24px rgba(1,19,46,.12);
      display: flex; gap: 14px; align-items: center;
    }
    .cart-bar.hidden { display: none; }
    .cart-info { flex: 1; }
    .cart-count { font-size: 13px; color: var(--ink-500); font-weight: 600; }
    .cart-total { font-size: 20px; font-weight: 800; }
    .btn-ver-pedido {
      background: linear-gradient(135deg, var(--gold-dark), var(--gold));
      color: #fff; font-weight: 800; font-size: 17px;
      padding: 15px 26px; border-radius: 18px;
      white-space: nowrap; min-height: 56px;
    }
    .btn-ver-pedido:active { opacity: .88; }

    /* ── Cart modal (slide-up sheet) ─────────────────── */
    .sheet {
      position: fixed; inset: 0; z-index: 100;
      display: flex; flex-direction: column;
      background: var(--surface);
      transform: translateY(100%);
      transition: transform .28s cubic-bezier(.22,.61,.36,1);
    }
    .sheet.open { transform: translateY(0); }
    .sheet-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: max(18px, env(safe-area-inset-top)) 20px 18px;
      background: linear-gradient(135deg, var(--navy-dark), var(--navy-deep));
      border-bottom: 1px solid rgba(255,255,255,.08);
      flex-shrink: 0;
    }
    .sheet-title { color: var(--gold); font-weight: 800; font-size: 22px; }
    .btn-back {
      color: rgba(255,255,255,.75); background: rgba(255,255,255,.10);
      border-radius: var(--radius-sm); padding: 10px 18px;
      font-weight: 700; font-size: 15px;
    }
    .sheet-body { flex: 1; overflow-y: auto; overscroll-behavior: contain; }

    /* Cart items */
    .cart-items { padding: 0 16px; }
    .cart-item {
      display: flex; align-items: center; gap: 12px;
      padding: 16px 0; border-bottom: 1px solid var(--line);
    }
    .cart-item-name { flex: 1; font-weight: 700; font-size: 16px; line-height: 1.3; }
    .cart-item-price { font-weight: 800; color: var(--gold-dark); font-size: 15px; white-space: nowrap; }
    .item-notes-wrap { padding: 0 0 14px; border-bottom: 1px solid var(--line); }
    .item-notes-input {
      width: 100%; padding: 10px 14px;
      border: 1.5px solid var(--line); border-radius: 12px;
      font-size: 14px; background: var(--surface-soft); outline: none;
      transition: border-color .15s;
    }
    .item-notes-input::placeholder { color: var(--ink-500); opacity: .75; }
    .item-notes-input:focus { border-color: var(--gold); background: var(--gold-100); }
    .qty-controls { display: flex; align-items: center; gap: 10px; }
    .qty-btn {
      width: 40px; height: 40px; border-radius: 12px;
      background: var(--surface-soft); font-size: 20px; font-weight: 700;
      display: grid; place-items: center; color: var(--ink-900);
      min-width: 40px;
    }
    .qty-btn:active { opacity: .75; }
    .qty-num { font-weight: 800; font-size: 18px; min-width: 28px; text-align: center; }
    .cart-empty { text-align: center; padding: 56px 24px; color: var(--ink-500); }
    .cart-empty svg { opacity: .35; margin-bottom: 16px; }
    .cart-empty p { font-size: 17px; font-weight: 600; }

    /* Order total row */
    .cart-total-row {
      display: flex; justify-content: space-between; align-items: center;
      padding: 18px 16px; font-weight: 800; font-size: 20px;
      border-top: 2px solid var(--line); background: var(--surface-soft);
    }
    .cart-total-row span:last-child { color: var(--gold-dark); }

    /* Order form */
    .order-form { padding: 20px 16px max(24px, env(safe-area-inset-bottom)); }
    .form-label { font-weight: 700; font-size: 14px; margin-bottom: 7px; display: block; color: var(--ink-900); }
    .form-control-lg-custom {
      width: 100%; padding: 15px 18px;
      border: 1.5px solid var(--line); border-radius: 14px;
      font-size: 16px; background: var(--surface);
      margin-bottom: 16px; outline: none;
      transition: border-color .15s;
    }
    .form-control-lg-custom:focus { border-color: var(--gold); }

    /* Order type tabs */
    .type-tabs { display: flex; gap: 8px; margin-bottom: 20px; }
    .type-tab {
      flex: 1; padding: 13px 8px; border-radius: 14px; text-align: center;
      font-weight: 700; font-size: 14px;
      background: var(--surface-soft); color: var(--ink-500);
      border: 2px solid transparent;
      min-height: 50px;
    }
    .type-tab.active {
      border-color: var(--gold); background: var(--gold-100); color: var(--gold-dark);
    }

    /* Submit button */
    .btn-submit {
      width: 100%; padding: 20px;
      background: linear-gradient(135deg, var(--navy-dark), var(--navy-deep));
      color: var(--gold); font-weight: 800; font-size: 19px;
      border-radius: 20px; min-height: 64px;
    }
    .btn-submit:active  { opacity: .88; }
    .btn-submit:disabled { opacity: .45; cursor: not-allowed; }

    /* ── Toast ──────────────────────────────────────── */
    .toast-wrap {
      position: fixed; top: 80px; left: 50%; transform: translateX(-50%);
      z-index: 200; pointer-events: none;
    }
    .toast {
      background: var(--navy-dark); color: #fff;
      padding: 13px 26px; border-radius: 999px;
      font-size: 14px; font-weight: 700; white-space: nowrap;
      border-left: 4px solid var(--gold);
      opacity: 0; transition: opacity .2s ease;
    }
    .toast.show { opacity: 1; }
    .toast.success { border-left-color: #4ade80; }
    .toast.error   { border-left-color: #f87171; }
  </style>
</head>
<body>

<!-- ── Header ── -->
<header class="app-header">
  <div class="header-brand-row">
    <img class="header-logo" src="{{ asset('assets/images/logo.png') }}" alt="Don Pulpo" />
    <div>
      <div class="header-brand">Don Pulpo</div>
      <div class="header-sub">{{ Auth::user()->name }}</div>
    </div>
  </div>
  <div id="activeBadge" class="active-badge">Activas: —</div>
  <form method="POST" action="{{ route('logout') }}" style="margin:0;">
    @csrf
    <button type="submit" class="btn-logout">Salir</button>
  </form>
</header>

<!-- ── Search ── -->
<div class="search-wrap">
  <input type="text" id="searchInput" class="search-input" placeholder="Buscar producto…" autocomplete="off" />
</div>

<!-- ── Category strip ── -->
<div class="cat-bar" id="catBar">
  <button class="cat-btn active" data-cat-id="all">Todo</button>
</div>

<!-- ── Product grid ── -->
<div class="content-area">
  <div class="product-grid" id="productGrid">
    <p style="grid-column:1/-1;text-align:center;padding:40px;color:var(--ink-500);">Cargando menú…</p>
  </div>
</div>

<!-- ── Cart bottom bar ── -->
<div class="cart-bar hidden" id="cartBar">
  <div class="cart-info">
    <div class="cart-count" id="cartCount">0 productos</div>
    <div class="cart-total" id="cartTotalBar">$0.00</div>
  </div>
  <button class="btn-ver-pedido" id="btnOpenCart">Ver pedido →</button>
</div>

<!-- ── Cart / Order sheet ── -->
<div class="sheet" id="cartSheet" role="dialog" aria-modal="true" aria-label="Tu pedido">
  <div class="sheet-header">
    <span class="sheet-title">Tu pedido</span>
    <button class="btn-back" id="btnCloseCart">← Menú</button>
  </div>
  <div class="sheet-body" id="sheetBody">
    <!-- Rendered by JS -->
  </div>
</div>

<!-- ── Dish photo modal ── -->
<div class="dish-modal" id="dishModal" role="dialog" aria-modal="true">
  <div class="dish-modal-card" id="dishModalCard">
    <div id="dishModalImgWrap"></div>
    <div class="dish-modal-body">
      <div class="dish-modal-name"  id="dishModalName"></div>
      <div class="dish-modal-price" id="dishModalPrice"></div>
      <div class="dish-modal-desc"  id="dishModalDesc"></div>
    </div>
    <div class="dish-modal-footer">
      <button class="btn-modal-close" id="btnModalClose">Cerrar</button>
      <button class="btn-modal-add"   id="btnModalAdd">+ Agregar al pedido</button>
    </div>
  </div>
</div>

<!-- ── Toast ── -->
<div class="toast-wrap"><div class="toast" id="toast"></div></div>

<script>
// ── Config ─────────────────────────────────────────────────────────
const ACTIVE_ORDERS_REFRESH_MS = 10000;  // Cambiar este valor para ajustar la frecuencia de actualización
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── State ──────────────────────────────────────────────────────────
const cart = new Map(); // dish_id → { name, price, qty }
let allDishes       = [];
let selectedCat     = 'all';
let selectedType    = 'dine_in';

// ── DOM refs ───────────────────────────────────────────────────────
const productGrid  = document.getElementById('productGrid');
const catBar       = document.getElementById('catBar');
const cartBar      = document.getElementById('cartBar');
const cartSheet    = document.getElementById('cartSheet');
const sheetBody    = document.getElementById('sheetBody');
const btnOpenCart  = document.getElementById('btnOpenCart');
const btnCloseCart = document.getElementById('btnCloseCart');
const searchInput  = document.getElementById('searchInput');
const activeBadge  = document.getElementById('activeBadge');

// ── Boot ───────────────────────────────────────────────────────────
(async function boot() {
  await loadMenu();
  startActiveOrdersPoller();
})();

// ── Menu loading ───────────────────────────────────────────────────
async function loadMenu() {
  try {
    const res  = await fetch('/api/v1/dish-categories?with_dishes=1');
    const json = await res.json();
    const categories = json.data ?? [];

    categories.forEach(cat => {
      (cat.dishes ?? []).forEach(d => {
        if (d.status === 'active') {
          allDishes.push({
            id:    d.id,
            name:  d.name,
            price: parseFloat(d.price),
            catId: cat.id,
            catName: cat.name,
            img:  d.image_path ? `/storage/${d.image_path}` : null,
            desc: d.description ?? '',
          });
        }
      });
    });

    categories.forEach(cat => {
      const hasActive = (cat.dishes ?? []).some(d => d.status === 'active');
      if (!hasActive) return;
      const btn = document.createElement('button');
      btn.className = 'cat-btn';
      btn.dataset.catId = cat.id;
      btn.textContent = cat.name;
      catBar.appendChild(btn);
    });

    renderProducts();
  } catch (_) {
    productGrid.innerHTML = '<p style="grid-column:1/-1;text-align:center;padding:40px;color:#f87171;">Error cargando menú. Recarga la página.</p>';
  }
}

// ── Render products ────────────────────────────────────────────────
function renderProducts() {
  const search = searchInput.value.trim().toLowerCase();
  let list = allDishes;
  if (selectedCat !== 'all') list = list.filter(d => String(d.catId) === String(selectedCat));
  if (search)                 list = list.filter(d => d.name.toLowerCase().includes(search));

  if (list.length === 0) {
    productGrid.innerHTML = '<p style="grid-column:1/-1;text-align:center;padding:40px;color:var(--ink-500);">Sin resultados.</p>';
    return;
  }

  productGrid.innerHTML = list.map(dish => {
    const qty   = cart.get(dish.id)?.qty ?? 0;
    const label = qty > 0 ? `+1 (${qty} en pedido)` : '+ Agregar';
    const thumb = dish.img
      ? `<img src="${esc(dish.img)}" class="product-thumb" alt="${esc(dish.name)}" loading="lazy" />`
      : `<div class="product-thumb-placeholder">🍽️</div>`;
    return `
      <div class="product-card">
        ${thumb}
        <div class="product-info">
          <div class="product-name">${esc(dish.name)}</div>
          <div class="product-price">$${dish.price.toFixed(2)}</div>
          ${qty > 0 ? `<div class="product-in-cart-label">${qty} en pedido</div>` : ''}
        </div>
        <div class="product-actions">
          <button class="btn-view-dish"
            data-action="view-dish"
            data-id="${dish.id}"
            data-name="${esc(dish.name)}"
            data-price="${dish.price}"
            data-img="${esc(dish.img ?? '')}"
            data-desc="${esc(dish.desc)}"
          >Ver foto</button>
          <button class="product-add-btn ${qty > 0 ? 'in-cart' : ''}"
            data-action="add-product"
            data-id="${dish.id}"
            data-name="${esc(dish.name)}"
            data-price="${dish.price}"
          >${label}</button>
        </div>
      </div>
    `;
  }).join('');
}

// ── Category filter ────────────────────────────────────────────────
catBar.addEventListener('click', e => {
  const btn = e.target.closest('.cat-btn');
  if (!btn) return;
  catBar.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  selectedCat = btn.dataset.catId;
  renderProducts();
});

searchInput.addEventListener('input', renderProducts);

// ── Global click handler (data-action pattern) ─────────────────────
document.addEventListener('click', handleClickEvent);

function handleClickEvent(e) {
  const el = e.target.closest('[data-action]');
  if (!el) return;

  switch (el.dataset.action) {
    case 'add-product': {
      const id    = Number(el.dataset.id);
      const name  = el.dataset.name;
      const price = parseFloat(el.dataset.price);
      const entry = cart.get(id);
      cart.set(id, { name, price, qty: (entry?.qty ?? 0) + 1, notes: entry?.notes ?? '' });
      updateCartBar();
      renderProducts();
      showToast(`${name} agregado`);
      break;
    }
    case 'qty-inc': {
      const id    = Number(el.dataset.id);
      const entry = cart.get(id);
      if (entry) cart.set(id, { ...entry, qty: entry.qty + 1 });
      renderSheet();
      renderProducts();
      updateCartBar();
      break;
    }
    case 'qty-dec': {
      const id    = Number(el.dataset.id);
      const entry = cart.get(id);
      if (!entry) break;
      if (entry.qty <= 1) cart.delete(id);
      else                cart.set(id, { ...entry, qty: entry.qty - 1 });
      renderSheet();
      renderProducts();
      updateCartBar();
      if (cart.size === 0) closeCart();
      break;
    }
    case 'set-type': {
      selectedType = el.dataset.type;
      document.querySelectorAll('[data-action="set-type"]').forEach(t => t.classList.remove('active'));
      el.classList.add('active');
      break;
    }
    case 'view-dish':
      openDishModal({
        id:    Number(el.dataset.id),
        name:  el.dataset.name,
        price: parseFloat(el.dataset.price),
        img:   el.dataset.img   || null,
        desc:  el.dataset.desc  || '',
      });
      break;
    case 'submit-order':
      submitOrder();
      break;
  }
}

// ── Cart bar ───────────────────────────────────────────────────────
function updateCartBar() {
  const total = [...cart.values()].reduce((s, i) => s + i.price * i.qty, 0);
  const qty   = [...cart.values()].reduce((s, i) => s + i.qty, 0);
  cartBar.classList.toggle('hidden', qty === 0);
  document.getElementById('cartCount').textContent  = `${qty} producto${qty !== 1 ? 's' : ''}`;
  document.getElementById('cartTotalBar').textContent = `$${total.toFixed(2)}`;
}

// ── Cart sheet ─────────────────────────────────────────────────────
btnOpenCart.addEventListener('click',  () => openCart());
btnCloseCart.addEventListener('click', () => closeCart());

sheetBody.addEventListener('input', e => {
  const inp = e.target.closest('.item-notes-input');
  if (!inp) return;
  const id    = Number(inp.dataset.notesId);
  const entry = cart.get(id);
  if (entry) cart.set(id, { ...entry, notes: inp.value });
});

function openCart() {
  renderSheet();
  cartSheet.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeCart() {
  cartSheet.classList.remove('open');
  document.body.style.overflow = '';
}

function renderSheet() {
  const items = [...cart.entries()];

  if (items.length === 0) {
    sheetBody.innerHTML = `
      <div class="cart-empty">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <p>Tu pedido está vacío.</p>
      </div>`;
    return;
  }

  const total = items.reduce((s, [, i]) => s + i.price * i.qty, 0);

  sheetBody.innerHTML = `
    <div class="cart-items">
      ${items.map(([id, item]) => `
        <div class="cart-item">
          <div class="cart-item-name">${esc(item.name)}</div>
          <div class="qty-controls">
            <button class="qty-btn" data-action="qty-dec" data-id="${id}">−</button>
            <span class="qty-num">${item.qty}</span>
            <button class="qty-btn" data-action="qty-inc" data-id="${id}">+</button>
          </div>
          <div class="cart-item-price">$${(item.price * item.qty).toFixed(2)}</div>
        </div>
        <div class="item-notes-wrap">
          <input class="item-notes-input" type="text" data-notes-id="${id}"
            placeholder="Comentario para cocina (ej: sin cebolla, término medio…)"
            value="${(item.notes ?? '').replace(/"/g, '&quot;')}" />
        </div>
      `).join('')}
    </div>
    <div class="cart-total-row">
      <span>Total</span>
      <span>$${total.toFixed(2)}</span>
    </div>
    <div class="order-form">
      <div class="type-tabs">
        <button class="type-tab ${selectedType === 'dine_in'  ? 'active' : ''}" data-action="set-type" data-type="dine_in">Mesa</button>
        <button class="type-tab ${selectedType === 'takeout'  ? 'active' : ''}" data-action="set-type" data-type="takeout">Para llevar</button>
        <button class="type-tab ${selectedType === 'delivery' ? 'active' : ''}" data-action="set-type" data-type="delivery">Delivery</button>
      </div>
      <label class="form-label" for="tableName">Mesa / Referencia</label>
      <input id="tableName" class="form-control-lg-custom" type="text" placeholder="Ej: Mesa 4, Terraza…" autocomplete="off" />
      <label class="form-label" for="customerName">Cliente (opcional)</label>
      <input id="customerName" class="form-control-lg-custom" type="text" placeholder="Nombre del cliente" autocomplete="off" />
      <label class="form-label" for="orderNotes">Notas (opcional)</label>
      <textarea id="orderNotes" class="form-control-lg-custom" rows="2" placeholder="Alergias, preferencias…"></textarea>
      <button class="btn-submit" data-action="submit-order">Enviar orden</button>
    </div>
  `;
}

// ── Submit order ───────────────────────────────────────────────────
async function submitOrder() {
  if (cart.size === 0) { showToast('Agrega productos primero', 'error'); return; }

  const tableName    = (document.getElementById('tableName')?.value    ?? '').trim();
  const customerName = (document.getElementById('customerName')?.value ?? '').trim();
  const notes        = (document.getElementById('orderNotes')?.value   ?? '').trim();

  const items = [...cart.entries()].map(([dishId, item]) => ({
    dish_id:       dishId,
    name_snapshot: item.name,
    unit_price:    item.price,
    quantity:      item.qty,
    notes:         item.notes || null,
  }));

  const submitBtn = document.querySelector('[data-action="submit-order"]');
  if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Enviando…'; }

  try {
    const res  = await fetch('/api/v1/orders', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({
        items,
        order_type:    selectedType,
        table_name:    tableName    || null,
        customer_name: customerName || null,
        notes:         notes        || null,
      }),
    });

    const json = await res.json();

    if (json.success) {
      cart.clear();
      updateCartBar();
      renderProducts();
      closeCart();
      showToast(`Orden ${json.data.order_number} enviada`, 'success');
      refreshActiveCount();
    } else {
      showToast(json.message ?? 'Error al enviar la orden', 'error');
      if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Enviar orden'; }
    }
  } catch (_) {
    showToast('Error de conexión', 'error');
    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Enviar orden'; }
  }
}

// ── Dish modal ─────────────────────────────────────────────────────
let modalDishId = null;

document.getElementById('dishModal').addEventListener('click', e => {
  if (e.target === document.getElementById('dishModal')) closeDishModal();
});
document.getElementById('btnModalClose').addEventListener('click', closeDishModal);
document.getElementById('btnModalAdd').addEventListener('click', () => {
  if (modalDishId === null) return;
  const dish  = allDishes.find(d => d.id === modalDishId);
  if (!dish) return;
  const entry = cart.get(dish.id);
  cart.set(dish.id, { name: dish.name, price: dish.price, qty: (entry?.qty ?? 0) + 1 });
  updateCartBar();
  renderProducts();
  closeDishModal();
  showToast(`${dish.name} agregado`);
});

function openDishModal(dish) {
  modalDishId = dish.id;

  const imgWrap = document.getElementById('dishModalImgWrap');
  imgWrap.innerHTML = dish.img
    ? `<img src="${esc(dish.img)}" class="dish-modal-img" alt="${esc(dish.name)}" />`
    : `<div class="dish-modal-img-placeholder">🍽️</div>`;

  document.getElementById('dishModalName').textContent  = dish.name;
  document.getElementById('dishModalPrice').textContent = `$${dish.price.toFixed(2)}`;
  document.getElementById('dishModalDesc').textContent  = dish.desc;

  const qty = cart.get(dish.id)?.qty ?? 0;
  document.getElementById('btnModalAdd').textContent =
    qty > 0 ? `+ Agregar otro (${qty} en pedido)` : '+ Agregar al pedido';

  document.getElementById('dishModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDishModal() {
  document.getElementById('dishModal').classList.remove('open');
  modalDishId = null;
  const sheetOpen = document.getElementById('cartSheet').classList.contains('open');
  document.body.style.overflow = sheetOpen ? 'hidden' : '';
}

// ── Active orders counter ──────────────────────────────────────────
async function refreshActiveCount() {
  try {
    const res  = await fetch('/api/v1/orders/active-count');
    const json = await res.json();
    activeBadge.textContent = `Activas: ${json.active_orders}`;
  } catch (_) { /* mantiene el último valor */ }
}

function startActiveOrdersPoller() {
  refreshActiveCount();
  setInterval(refreshActiveCount, ACTIVE_ORDERS_REFRESH_MS);
}

// ── Helpers ────────────────────────────────────────────────────────
function esc(str) {
  return String(str)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

let toastTimer;
function showToast(msg, type = '') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = `toast show${type ? ' ' + type : ''}`;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => t.classList.remove('show'), 2400);
}
</script>
</body>
</html>
