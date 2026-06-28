<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Don Pulpo | Restaurante de Mariscos</title>

  <style>
    /* =====================
       RESET & GLOBALS
    ===================== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: sans-serif;
    }

    a { text-decoration: none; color: inherit; }

    :root {
      --azul-marino: #073b5a;
      --azul-profundo: #0b4f71;
      --azul-marino-claro: #126782;
      --celeste: #5ecdf2;
      --celeste-fuerte: #1ca7ec;
      --celeste-suave: #ddf6ff;
      --turquesa: #00b4c8;
      --turquesa-claro: #8eeeff;
      --arena: #eefaff;
      --crema: #f7fdff;
      --naranja: #ff8a3d;
      --coral: #ff6b4a;
      --texto: #102a3a;
      --gris: #64748b;
      --blanco: #ffffff;
      --sombra: 0 24px 60px rgba(7,59,90,.16);
    }

    /* =====================
       SECTIONS TOGGLE
    ===================== */
    #section-landing { display: block; }
    #section-menu    { display: none;  }

    /* =====================
       LANDING — FONDO
    ===================== */
    #section-landing {
      background:
        radial-gradient(circle at 10% 85%, rgba(94,205,242,.28), transparent 30%),
        radial-gradient(circle at 90% 18%, rgba(0,180,200,.20),  transparent 32%),
        radial-gradient(circle at 55% 100%, rgba(7,59,90,.10),   transparent 28%),
        linear-gradient(180deg, #f7fdff 0%, #eefaff 52%, #ffffff 100%);
      min-height: 100vh;
    }

    /* =====================
       SHARED NAVBAR
    ===================== */
    .page { min-height: 100vh; position: relative; }

    .bubble { position: absolute; border: 2px solid rgba(94,205,242,.42); border-radius: 50%; z-index: 0; }
    .bubble.one   { width:90px;  height:90px;  top:170px; left:60px; }
    .bubble.two   { width:46px;  height:46px;  top:460px; right:110px; }
    .bubble.three { width:28px;  height:28px;  bottom:120px; left:48%; }

    .tentacle-bg {
      position: absolute; right:-120px; bottom:-80px; width:380px; height:380px;
      background:
        radial-gradient(circle at 35% 35%, rgba(94,205,242,.28), transparent 35%),
        radial-gradient(circle at 62% 58%, rgba(0,180,200,.22),  transparent 30%);
      border-radius: 50%; filter: blur(2px); z-index: 0;
    }

    .navbar-wrap {
      width: min(1180px, calc(100% - 32px));
      margin: 20px auto 0;
      position: relative;
      z-index: 10;
    }

    .navbar {
      height: 86px;
      background: rgba(221,246,255,.88);
      backdrop-filter: blur(18px);
      border: 1px solid rgba(94,205,242,.35);
      border-radius: 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 28px;
      box-shadow: 0 18px 45px rgba(7,59,90,.10);
    }

    .brand {
      display: flex; align-items: center; gap: 12px;
      font-weight: 900; letter-spacing: -.5px;
      color: var(--azul-marino); font-size: 1.45rem;
    }

    .brand-icon {
      width:52px; height:52px; border-radius:18px;
      display:grid; place-items:center; color:#fff; font-size:1.7rem;
      background: linear-gradient(135deg, var(--celeste-fuerte), var(--azul-marino-claro));
      box-shadow: 0 12px 28px rgba(28,167,236,.35);
    }

    .nav-links {
      display: flex; align-items: center; gap: 38px;
      color: #6b8798; font-weight: 700;
    }

    .nav-links a { position: relative; transition: .2s ease; }
    .nav-links a:hover, .nav-links a.active { color: var(--azul-marino); }
    .nav-links a.active::after {
      content: ""; position: absolute;
      left: 50%; bottom: -31px; transform: translateX(-50%);
      width: 70px; height: 6px; border-radius: 10px;
      background: linear-gradient(90deg, var(--celeste), var(--turquesa));
    }

    .nav-actions { display: flex; align-items: center; gap: 18px; }

    .cart { position: relative; font-size: 1.8rem; }
    .cart span {
      position: absolute; right:-9px; top:-8px;
      width:21px; height:21px; border-radius:50%;
      background: linear-gradient(135deg, var(--celeste-fuerte), var(--turquesa));
      color:#fff; font-size:.72rem; display:grid; place-items:center; font-weight:900;
    }

    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      gap: 10px; border: none; cursor: pointer; font-weight: 900;
      border-radius: 999px; padding: 15px 26px; transition: .2s ease;
      white-space: nowrap;
    }

    .btn-primary {
      color: #fff;
      background: linear-gradient(135deg, var(--celeste-fuerte), var(--azul-marino-claro));
      box-shadow: 0 18px 36px rgba(28,167,236,.28);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 24px 48px rgba(18,103,130,.35); }

    .btn-secondary {
      color: var(--azul-marino); background: #fff;
      border: 1px solid rgba(94,205,242,.35);
      box-shadow: 0 14px 32px rgba(7,59,90,.08);
    }

    /* =====================
       HERO
    ===================== */
    .hero {
      width: min(1180px, calc(100% - 32px));
      margin: 54px auto 0;
      min-height: calc(100vh - 160px);
      display: grid; grid-template-columns: 1fr 1.05fr;
      align-items: center; gap: 44px;
      position: relative; z-index: 2;
    }

    .hero-content { position: relative; z-index: 3; }

    .tag {
      display: inline-flex; align-items: center; gap: 9px;
      background: rgba(255,255,255,.88);
      color: var(--azul-marino-claro);
      border: 1px solid rgba(94,205,242,.35);
      box-shadow: 0 12px 30px rgba(28,167,236,.10);
      padding: 9px 14px; border-radius: 999px; font-size: .9rem;
      font-weight: 900; text-transform: uppercase; letter-spacing: .3px; margin-bottom: 22px;
    }

    h1 {
      font-size: clamp(3rem, 6vw, 5.8rem);
      line-height: .98; letter-spacing: -3.5px;
      color: var(--azul-marino); margin-bottom: 24px;
    }

    h1 span {
      background: linear-gradient(135deg, var(--celeste-fuerte), var(--turquesa));
      -webkit-background-clip: text; background-clip: text; color: transparent;
    }

    .hero-content p {
      font-size: 1.18rem; color: #365466; max-width: 540px;
      line-height: 1.75; margin-bottom: 34px;
    }

    .hero-buttons { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 38px; }

    .stats { display: flex; gap: 22px; flex-wrap: wrap; }

    .stat {
      background: rgba(255,255,255,.82);
      border: 1px solid rgba(94,205,242,.25);
      border-radius: 20px; padding: 16px 20px; min-width: 130px;
      box-shadow: 0 18px 36px rgba(7,59,90,.08);
    }
    .stat strong { display: block; color: var(--azul-marino); font-size: 1.45rem; font-weight: 950; }
    .stat small  { color: var(--gris); font-weight: 700; }

    /* Plate */
    .hero-visual { position:relative; height:640px; display:grid; place-items:center; z-index:2; }

    .plate-glow {
      position:absolute; width:560px; height:560px; border-radius:50%;
      background:
        radial-gradient(circle, rgba(94,205,242,.44) 0%, transparent 58%),
        radial-gradient(circle at 70% 20%, rgba(0,180,200,.28), transparent 32%);
      filter: blur(8px);
    }

    .plate {
      position:relative; width:min(560px,90vw); aspect-ratio:1; border-radius:50%;
      background: radial-gradient(circle at 45% 42%, #ffffff 0 38%, #eaf9ff 39% 53%, #ffffff 54% 100%);
      box-shadow: inset 0 0 0 22px rgba(255,255,255,.78), 0 36px 80px rgba(7,59,90,.18);
      display:grid; place-items:center; overflow:hidden;
    }
    .plate::before {
      content:""; position:absolute; inset:38px; border-radius:50%;
      background:
        radial-gradient(circle at 45% 52%, rgba(94,205,242,.18), transparent 30%),
        linear-gradient(135deg, rgba(255,255,255,.95), rgba(221,246,255,.65));
    }

    .seafood { position:relative; z-index:3; width:78%; height:78%; border-radius:50%; display:grid; place-items:center; }

    .shrimp-main {
      position:absolute; width:310px; height:170px;
      background: linear-gradient(135deg, #ff9966, #ff6b4a);
      border-radius:55% 45% 55% 45%; transform:rotate(-22deg);
      box-shadow: inset -16px -18px 0 rgba(107,38,20,.12), 0 18px 28px rgba(18,103,130,.16);
    }
    .shrimp-main::before, .shrimp-main::after {
      content:""; position:absolute; border-radius:50%;
      background:rgba(255,255,255,.34); width:38px; height:110px; top:28px; transform:rotate(8deg);
    }
    .shrimp-main::before { left:70px; }
    .shrimp-main::after  { left:150px; }

    .greens {
      position:absolute; width:360px; height:360px; border-radius:50%;
      background:
        radial-gradient(ellipse at 30% 40%, #1c9b54 0 3px, transparent 4px),
        radial-gradient(ellipse at 60% 25%, #54c76e 0 4px, transparent 5px),
        radial-gradient(ellipse at 70% 70%, #2ca85b 0 4px, transparent 5px);
    }
    .greens::before {
      content:""; position:absolute; inset:55px;
      background:
        linear-gradient(50deg,  transparent 45%, #1c9b54 46% 52%, transparent 53%),
        linear-gradient(110deg, transparent 45%, #32b263 46% 52%, transparent 53%),
        linear-gradient(160deg, transparent 45%, #54c76e 46% 52%, transparent 53%);
      border-radius:50%; opacity:.8;
    }

    .tomatoes { position:absolute; right:58px; bottom:95px; display:flex; gap:9px; z-index:4; }
    .tomatoes i {
      width:42px; height:42px; border-radius:50%;
      background: radial-gradient(circle at 30% 25%, #fff5, transparent 25%), #e93324;
      box-shadow: inset -8px -8px 0 rgba(98,20,16,.15);
    }

    .lemon {
      position:absolute; left:42px; top:94px; width:84px; height:84px; border-radius:50%;
      background:
        radial-gradient(circle, transparent 0 32%, rgba(255,255,255,.65) 33% 35%, transparent 36%),
        conic-gradient(#ffce3a 0 20deg, #fff3a6 20deg 26deg, #ffce3a 26deg 52deg, #fff3a6 52deg 60deg, #ffce3a 60deg 100%);
      border:7px solid #ffd76b; z-index:4; box-shadow:0 14px 26px rgba(255,122,26,.18);
    }

    .sauce-splash {
      position:absolute; width:620px; height:420px; right:-110px; top:10px;
      background:
        radial-gradient(circle at 30% 40%, rgba(94,205,242,.36), transparent 8%),
        radial-gradient(circle at 58% 35%, rgba(0,180,200,.30),  transparent 5%),
        radial-gradient(circle at 72% 60%, rgba(18,103,130,.22), transparent 7%);
      filter:blur(.2px); z-index:-1;
    }

    .dish-card {
      position:absolute; background:rgba(255,255,255,.92);
      backdrop-filter:blur(14px); border:1px solid rgba(94,205,242,.25);
      border-radius:28px; box-shadow:var(--sombra);
      padding:14px; width:230px; z-index:6;
    }
    .dish-card.one { left:10px;  bottom:90px; transform:rotate(-2deg); }
    .dish-card.two { right:-5px; bottom:42px; transform:rotate(3deg); }

    .dish-img {
      height:120px; border-radius:20px;
      background:
        radial-gradient(circle at 45% 40%, rgba(28,167,236,.95), transparent 18%),
        radial-gradient(circle at 60% 55%, rgba(0,180,200,.90),  transparent 20%),
        linear-gradient(135deg, #eaf9ff, #ffffff);
      margin-bottom:12px;
    }

    .dish-card h3 { color:var(--azul-marino); font-size:1.02rem; margin-bottom:8px; }
    .rating       { color:var(--celeste-fuerte); font-weight:900; }
    .rating span  { color:var(--texto); margin-left:6px; }

    /* Featured cards */
    .featured {
      width:min(1180px,calc(100% - 32px));
      margin:30px auto 80px;
      display:grid; grid-template-columns:repeat(3,1fr); gap:22px;
      position:relative; z-index:3;
    }

    .feature-card {
      background:rgba(255,255,255,.90);
      border:1px solid rgba(94,205,242,.25);
      border-radius:26px; padding:26px;
      box-shadow:0 18px 45px rgba(7,59,90,.08);
      transition:.2s ease;
    }
    .feature-card:hover { transform:translateY(-6px); box-shadow:0 26px 60px rgba(7,59,90,.15); }

    .feature-icon {
      width:54px; height:54px; border-radius:18px;
      display:grid; place-items:center; font-size:1.6rem; margin-bottom:16px;
      background:linear-gradient(135deg,rgba(94,205,242,.20),rgba(0,180,200,.18));
    }
    .feature-card h3 { color:var(--azul-marino); margin-bottom:9px; font-size:1.25rem; }
    .feature-card p  { color:var(--gris); line-height:1.6; }

    /* =====================
       MENU SECTION
    ===================== */
    #section-menu {
      min-height: 100vh;
      font-family: Arial, sans-serif;
      color: #06235c;
      background: url('/bg-menu.jpg') center / cover fixed no-repeat;
    }

    .menu-wrapper { max-width:980px; margin:auto; padding:35px 14px 90px; }

    .menu-brand { text-align:center; color:#00285a; margin-bottom:28px; }
    .menu-brand h2 {
      font-family:Georgia,serif;
      font-size:clamp(42px,8vw,76px);
      letter-spacing:4px;
      text-shadow:0 6px 18px rgba(0,0,0,.45);
    }
    .menu-brand p { color:#f0b33a; letter-spacing:8px; font-weight:bold; }

    .category-card {
      background:rgba(255,250,240,.94);
      border:3px solid #d99a1e;
      border-radius:24px; padding:28px; margin-bottom:28px;
      box-shadow:0 18px 45px rgba(0,0,0,.35);
      backdrop-filter:blur(4px);
    }

    .category-title {
      width:fit-content; margin:0 auto 24px;
      padding:10px 34px;
      background:#00285a; color:white;
      border:3px solid #d99a1e; border-radius:12px;
      font-family:Georgia,serif;
      font-size:clamp(26px,5vw,42px);
      letter-spacing:5px; text-transform:uppercase;
      text-align:center;
      box-shadow:inset 0 0 0 2px #001936, 0 0 0 3px #fff8eb;
    }

    .menu-item { margin-bottom:16px; font-size:clamp(16px,3vw,23px); font-weight:700; }
    .item-name-row {
      display:grid; grid-template-columns:auto 1fr auto;
      gap:10px; align-items:end;
    }
    .item-name { line-height:1.25; }
    .dots { border-bottom:3px dotted #06235c; transform:translateY(-6px); min-width:25px; }
    .price { color:#06235c; font-size:clamp(17px,3vw,24px); font-weight:900; white-space:nowrap; }
    .item-description { font-size:.82em; font-weight:400; color:#4a6070; margin-top:4px; line-height:1.4; padding-left:2px; }

    .whatsapp-btn {
      position:fixed; left:16px; right:16px; bottom:16px; z-index:20;
      background:#25d366; color:white; text-align:center; text-decoration:none;
      padding:15px; border-radius:50px; font-weight:800;
      box-shadow:0 12px 30px rgba(0,0,0,.35);
    }

    /* =====================
       RESPONSIVE
    ===================== */
    @media (max-width: 980px) {
      .navbar { height:auto; padding:18px; gap:18px; flex-wrap:wrap; }
      .nav-links { order:3; width:100%; justify-content:center; gap:22px; font-size:.95rem; }
      .nav-links a.active::after { display:none; }
      .hero { grid-template-columns:1fr; margin-top:42px; text-align:center; }
      .hero-content p { margin-left:auto; margin-right:auto; }
      .hero-buttons, .stats { justify-content:center; }
      .hero-visual { height:560px; }
      .featured { grid-template-columns:1fr; }
    }

    @media (max-width: 640px) {
      .navbar-wrap { width:calc(100% - 20px); margin-top:10px; }
      .navbar { border-radius:22px; }
      .brand { font-size:1.15rem; }
      .brand-icon { width:46px; height:46px; }
      .nav-actions .btn { display:none; }
      .nav-links { overflow-x:auto; justify-content:flex-start; padding-bottom:4px; }
      .hero { width:calc(100% - 28px); min-height:auto; gap:20px; }
      h1 { letter-spacing:-2px; }
      .hero-content p { font-size:1rem; }
      .btn { width:100%; }
      .hero-visual { height:480px; }
      .plate { width:360px; }
      .shrimp-main { width:220px; height:120px; }
      .greens { width:260px; height:260px; }
      .lemon { width:62px; height:62px; }
      .tomatoes { right:45px; bottom:78px; }
      .tomatoes i { width:30px; height:30px; }
      .dish-card { width:180px; }
      .dish-card.one { left:-8px; bottom:54px; }
      .dish-card.two { right:-10px; bottom:8px; }
      .dish-img { height:90px; }
      .featured { width:calc(100% - 28px); margin-top:10px; }

      .menu-wrapper { padding:24px 10px 85px; }
      .category-card { padding:22px 14px; border-radius:18px; }
      .dots { display:none; }
      .item-name-row { grid-template-columns:1fr auto; }
      .category-title { letter-spacing:3px; }
      .menu-brand { margin-top:50px; }
    }
  </style>
</head>

<body>

<!-- ===================== NAVBAR (compartida) ===================== -->
<header class="navbar-wrap">
  <nav class="navbar">
    <a href="#" class="brand" id="nav-logo">
      <span class="brand-icon">🐙</span>
      <span>DON PULPO</span>
    </a>

    <div class="nav-links">
      <a href="#"     id="nav-inicio" class="active">Inicio</a>
      <a href="#menu" id="nav-menu">Menú</a>
      <a href="#">Especialidades</a>
      <a href="#">Ubicación</a>
      <a href="#">Contacto</a>
    </div>

    <div class="nav-actions">
      <div class="cart">🛒 <span>1</span></div>
      <a href="#menu" class="btn btn-primary">Ordenar ahora</a>
    </div>
  </nav>
</header>

<!-- ===================== LANDING ===================== -->
<div id="section-landing">
  <main class="page">
    <div class="bubble one"></div>
    <div class="bubble two"></div>
    <div class="bubble three"></div>
    <div class="tentacle-bg"></div>

    <section class="hero">
      <div class="hero-content">
        <h1>Don Pulpo, Desde el mar hasta tu paladar</h1>
        <p>
          Disfruta ceviches, tostadas, camarones, pulpo, aguachiles y especialidades
          preparadas al momento con el auténtico sabor de Don Pulpo.
        </p>
        <div class="hero-buttons">
          <a href="#menu" class="btn btn-primary">Ver menú</a>
          <a href="#" class="btn btn-secondary">Reservar mesa</a>
        </div>
        <div class="stats">
          <div class="stat"><strong>4.9</strong><small>Calificación</small></div>
          <div class="stat"><strong>+25</strong><small>Platillos</small></div>
          <div class="stat"><strong>100%</strong><small>Sabor fresco</small></div>
        </div>
      </div>

      <div class="hero-visual">
        <div class="plate-glow"></div>
        <div class="sauce-splash"></div>
        <div class="plate">
          <div class="seafood">
            <div class="greens"></div>
            <div class="lemon"></div>
            <div class="shrimp-main"></div>
            <div class="tomatoes"><i></i><i></i><i></i></div>
          </div>
        </div>
        <article class="dish-card one">
          <div class="dish-img"></div>
          <h3>Aguachile Especial</h3>
          <div class="rating">★ <span>5.0</span></div>
        </article>
        <article class="dish-card two">
          <div class="dish-img"></div>
          <h3>Pulpo a las Brasas</h3>
          <div class="rating">★ <span>5.0</span></div>
        </article>
      </div>
    </section>

    <section class="featured">
      <article class="feature-card">
        <div class="feature-icon">🦐</div>
        <h3>Mariscos frescos</h3>
        <p>Platillos preparados al momento con ingredientes seleccionados y sazón de casa.</p>
      </article>
      <article class="feature-card">
        <div class="feature-icon">🍋</div>
        <h3>Especialidades Don Pulpo</h3>
        <p>Aguachiles, tostadas, ceviches, camarones y recetas ideales para compartir.</p>
      </article>
      <article class="feature-card">
        <div class="feature-icon">📍</div>
        <h3>Ordena o visítanos</h3>
        <p>Consulta el menú digital, pide para llevar o reserva una mesa en minutos.</p>
      </article>
    </section>
  </main>
</div>

<!-- ===================== MENÚ ===================== -->
<div id="section-menu">
  <main class="menu-wrapper">
    <div class="menu-brand">
      <h2>DON PULPO</h2>
      <p>MARISCOS</p>
    </div>
    <div id="menu-items">
      @foreach($categories as $category)
        @php $platillos = $dishes->get($category->id, collect()); @endphp
        @if($platillos->isNotEmpty())
        <section class="category-card">
          <h2 class="category-title">{{ $category->name }}</h2>
          @foreach($platillos as $dish)
          <div class="menu-item">
            <div class="item-name-row">
              <span class="item-name">{{ $dish->name }}</span>
              <span class="dots"></span>
              <span class="price">{{ $dish->price > 0 ? '$'.number_format($dish->price, 0) : 'S/P' }}</span>
            </div>
            @if(!empty($dish->description))
            <div class="item-description">{{ $dish->description }}</div>
            @endif
          </div>
          @endforeach
        </section>
        @endif
      @endforeach
      @if($categories->isEmpty())
      <p style="text-align:center;color:#fff;font-size:1.2rem;padding:40px 0">
        El menú estará disponible pronto.
      </p>
      @endif
    </div>
  </main>
</div>

<script>
/* ===================== HASH ROUTER ===================== */
const sectionLanding = document.getElementById('section-landing');
const sectionMenu    = document.getElementById('section-menu');
const navInicio      = document.getElementById('nav-inicio');
const navMenu        = document.getElementById('nav-menu');

const navbar = document.querySelector('.navbar-wrap');

function showSection(hash) {
  if (hash === '#menu') {
    sectionLanding.style.display = 'none';
    sectionMenu.style.display    = 'block';
    navbar.style.display         = 'none';
    navInicio.classList.remove('active');
    navMenu.classList.add('active');
  } else {
    sectionLanding.style.display = 'block';
    sectionMenu.style.display    = 'none';
    navbar.style.display         = '';
    navMenu.classList.remove('active');
    navInicio.classList.add('active');
  }
}

showSection(window.location.hash);
window.addEventListener('hashchange', () => showSection(window.location.hash));

/* ===================== WHATSAPP ===================== */
const whatsappNumber = "5218112345678";

function setupWhatsapp() {
  const text = encodeURIComponent("Hola, quiero hacer un pedido en Don Pulpo");
  const btn = document.getElementById('whatsappBtn');
  if (btn) {
    btn.href = `https://wa.me/${whatsappNumber}?text=${text}`;
    btn.style.display = 'block';
  }
}

setupWhatsapp();
</script>

</body>
</html>
