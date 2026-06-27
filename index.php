<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Don Pulpo | Menú</title>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  min-height: 100vh;
  font-family: Arial, sans-serif;
  color: #06235c;
  background:
    url("bg-menu.jpg") center / cover fixed no-repeat;
}

.menu-wrapper {
  max-width: 980px;
  margin: auto;
  padding: 35px 14px 90px;
}

.brand {
  text-align: center;
  color: #00285a;
  margin-bottom: 28px;
}

.brand h1 {
  font-family: Georgia, serif;
  font-size: clamp(42px, 8vw, 76px);
  letter-spacing: 4px;
  text-shadow: 0 6px 18px rgba(0,0,0,.45);
}

.brand p {
  color: #f0b33a;
  letter-spacing: 8px;
  font-weight: bold;
}

.category-card {
  background: rgba(255, 250, 240, .94);
  border: 3px solid #d99a1e;
  border-radius: 24px;
  padding: 28px;
  margin-bottom: 28px;
  box-shadow: 0 18px 45px rgba(0,0,0,.35);
  backdrop-filter: blur(4px);
}

.category-title {
  width: fit-content;
  margin: 0 auto 24px;
  padding: 10px 34px;
  background: #00285a;
  color: white;
  border: 3px solid #d99a1e;
  border-radius: 12px;
  font-family: Georgia, serif;
  font-size: clamp(26px, 5vw, 42px);
  letter-spacing: 5px;
  text-transform: uppercase;
  text-align: center;
  box-shadow:
    inset 0 0 0 2px #001936,
    0 0 0 3px #fff8eb;
}

.menu-item {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 10px;
  align-items: end;
  margin-bottom: 13px;
  font-size: clamp(16px, 3vw, 23px);
  font-weight: 700;
}

.item-name {
  line-height: 1.25;
}

.dots {
  border-bottom: 3px dotted #06235c;
  transform: translateY(-6px);
  min-width: 25px;
}

.price {
  color: #06235c;
  font-size: clamp(17px, 3vw, 24px);
  font-weight: 900;
  white-space: nowrap;
}

.whatsapp {
  position: fixed;
  left: 16px;
  right: 16px;
  bottom: 16px;
  z-index: 20;
  background: #25d366;
  color: white;
  text-align: center;
  text-decoration: none;
  padding: 15px;
  border-radius: 50px;
  font-weight: 800;
  box-shadow: 0 12px 30px rgba(0,0,0,.35);
}
@media (max-width: 600px) {
  .menu-wrapper {
    padding: 24px 10px 85px;
  }

  .category-card {
    padding: 22px 14px;
    border-radius: 18px;
  }

  .menu-item {
    grid-template-columns: 1fr auto;
    gap: 8px;
  }

  .dots {
    display: none;
  }

  .category-title {
    letter-spacing: 3px;
  }
  .brand{
    margin-top: 50px;
  }
}
</style>
</head>

<body>

<main class="menu-wrapper">
  <div class="brand">
    <h1>DON PULPO</h1>
    <p>MARISCOS</p>
  </div>

  <div id="menu"></div>
</main>

<a
  class="whatsapp"
  id="whatsappBtn"
  href="#"
  style="display:none"
  target="_blank">
  Ordenar por WhatsApp
</a>

<script>
const whatsappNumber = "5218112345678";

const menu = [
  {
    categoria: "Entradas",
    platillos: [
      ["Ostiones en su concha (6 piezas)", 250],
      ["Ostiones en su concha (12 piezas)", 400],
      ["Orden 3 quesadillas de camarón", 120],
      ["Orden 3 quesadillas de Marlín", 110],
      ["Papas a la francesa", 85],
      ["Aros de cebolla", 105],
      ["Dedos de queso", 70],
      ["Guacamole", 115],
      ["Bardal", 260], 
      ["Palomitas de pollo", 80]
    ]
  },
  {
    categoria: "Tostadas",
    platillos: [
      ["Tostada de ceviche de pescado", 80],
      ["Tostada de ceviche de camarón", 100],
      ["Tostada mixta", 120],
      ["Tostada de camarón", 90],
      ["Tostada de pulpo", 105],
      ["Tostada de Marlín", 80]
    ]
  },
  {
    categoria: "Ceviches",
    platillos: [
      ["Ceviche de pescado medio litro", 130],
      ["Ceviche de pescado litro", 250],
      ["Ceviche de camarón y pescado medio litro", 210],
      ["Ceviche de camarón y pescado litro", 300],
      ["Ceviche de camarón medio litro", 180],
      ["Ceviche de camarón litro", 310]
    ]
  },
  {
    categoria: "Aguachiles",
    platillos: [
      ["Aguachile de camarón (verde, rojo o negro)", 250]
    ]
  },
  {
    categoria: "Empanizados",
    platillos: [
      ["Mariscada individual", 250],
      ["Mariscada para dos personas", 400],
      ["Mariscada para 4 personas", 700],
      ["Filete", 170],
      ["Camarón", 230],
      ["Rollo de filete", 250],
      ["Mixto filete y camarones", 245]
    ]
  },
  {
    categoria: "Cocteles",
    platillos: [
      ["Coctel de camarón chico", 100],
      ["Coctel de camarón mediano", 180],
      ["Coctel de camarón grande", 230],
      ["Coctel de pulpo chico", 115],
      ["Coctel de pulpo mediano", 195],
      ["Coctel de pulpo grande", 245],
      ["Campechano mixto mediano", 200],
      ["Campechano mixto grande", 250],
      ["Vuelve a la vida mediano", 205],
      ["Vuelve a la vida grande", 255]
    ]
  },
  {
    categoria: "Caldos",
    platillos: [
      ["Sopa de mariscos 1/2", 160],
      ["Sopa de mariscos", 250],
      ["Caldo de filete 1/2", 120],
      ["Caldo de filete", 160],
      ["Caldo de camarón 1/2", 140],
      ["Caldo de camarón", 180],
      ["Caldo Mixto", 190],
      ["Veneno de camarón", 220]
    ]
  },
  {
    categoria: "Fritos",
    platillos: [
      ["Mojarra frita", 250],
      ["Huachinango", 450]
    ]
  },
  {
    categoria: "Plancha",
    platillos: [
      ["Filete a la plancha", 180],
      ["Filete a la plancha gratinado", 210],
      ["Camarones a la plancha", 220],
      ["Camarones a la plancha gratinado", 245],
      ["Mixto a la plancha", 250],
      ["Mixto a la plancha gratinado", 265],
      ["Filete al vapor", 260]
    ]
  },
  {
    categoria: "Filetes",
    platillos: [
      ["Filete a la mantequilla", 250],
      ["Filete gratinado", 250],
      ["Filete a la mexicana", 250],
      ["Filete a la diabla", 250],
      ["Filete al ajillo", 250],
      ["Filete a la veracruzana", 250],
      ["Filete al mojo de ajo", 250],
      ["Filete ranchero", 250]
    ]
  },
  {
    categoria: "Camarones",
    platillos: [
      ["Camarones a la mantequilla", 265],
      ["Camarones al mojo de ajo", 265],
      ["Camarones rancheros", 265],
      ["Camarones a la mexicana", 265],
      ["Camarones a la diabla", 265],
      ["Camarones al ajillo", 265],
      ["Camarones gratinados", 270],
      ["Camarones a la veracruzana", 265],
      ["Camarones encebollados a la diabla", 270],
      ["Camarones Envueltos", 285],
      ["Camarones Macuil para pelar (8 piezas)", 250]
    ]
  },
  {
    categoria: "Combos",
    platillos: [
      ["Combo 1 - Filete, Camarones Envueltos, Posta de Mojarra y aros de cebolla, arroz, papas y ensalada", 265],
      ["Combo 2 - Medio caldo de filete, filete empanizado", 270],
      ["Combo 3 - Coctel de camarón mediano y tostada de ceviche", 250],
      ["Combo 4 - Camarones gratinados y medio caldo de filete", 350],
      ["Combo 5 - 2 Mojarras", 480]
    ]
  },
  {
    categoria: "Menú infantil (todos los platillos incluyen papas a la francesa y jugo)",
    platillos: [
      ["Nuggets de pollo", 90],
      ["Dedos de queso", 70],
      ["Papas a la francesa", 50],
      ["Fajitas de pollo", 160],
      ["Palomitas de pollo", 120],
      ["Juguito", 25]
    ]
  },
  {
    categoria: "Bebidas",
    platillos: [
      ["Coca cola", 45],
      ["Agua mineral", 45],
      ["Joya de sabor", 45],
      ["Agua natural", 40],
      //["Limonada natural", 60],
      //["Limonada mineral", 75]
    ]
  }
];

const menuContainer = document.getElementById("menu");

function formatPrice(price) {
  if (price < 0 || price === null || price === "") {
    return "S/P";
  }

  return `$${price}`;
}

function renderMenu() {
  menuContainer.innerHTML = "";

  menu.forEach(category => {
    const section = document.createElement("section");
    section.className = "category-card";

    const title = document.createElement("h2");
    title.className = "category-title";
    title.textContent = category.categoria;

    section.appendChild(title);

    category.platillos.forEach(([name, price]) => {
      const item = document.createElement("div");
      item.className = "menu-item";

      item.innerHTML = `
        <span class="item-name">${name}</span>
        <span class="dots"></span>
        <span class="price">${formatPrice(price)}</span>
      `;

      section.appendChild(item);
    });

    menuContainer.appendChild(section);
  });
}

function setupWhatsapp() {
  const text = encodeURIComponent("Hola, quiero hacer un pedido en Don Pulpo");
  document.getElementById("whatsappBtn").href =
    `https://wa.me/${whatsappNumber}?text=${text}`;
}

renderMenu();
setupWhatsapp();
</script>

</body>
</html>