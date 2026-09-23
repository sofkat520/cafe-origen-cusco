<?php
require_once __DIR__ . '/config/database.php';

// ---------------------------------------------------------
// Formulario de contacto / reserva (POST simulado a sí misma)
// ---------------------------------------------------------
$reserva_ok = false;
$reserva_nombre = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'reserva') {
    $reserva_nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $reserva_email  = htmlspecialchars(trim($_POST['email'] ?? ''));
    $reserva_fecha  = htmlspecialchars(trim($_POST['fecha'] ?? ''));
    $reserva_mensaje = htmlspecialchars(trim($_POST['mensaje'] ?? ''));

    // En un entorno real aquí se insertaría en una tabla "reservas"
    // o se enviaría un correo con mail()/PHPMailer. Para este entregable
    // académico se confirma la recepción y se refleja en la interfaz.
    if ($reserva_nombre !== '' && $reserva_email !== '') {
        $reserva_ok = true;
    }
}

// ---------------------------------------------------------
// Categorías (para el filtro)
// ---------------------------------------------------------
$categorias = [];
$resCat = $conn->query("SELECT id, nombre, slug FROM categorias ORDER BY id ASC");
while ($row = $resCat->fetch_assoc()) {
    $categorias[] = $row;
}

// ---------------------------------------------------------
// Productos (lectura dinámica, con filtro opcional por categoría)
// ---------------------------------------------------------
$categoria_filtro = isset($_GET['categoria']) ? (int) $_GET['categoria'] : 0;

if ($categoria_filtro > 0) {
    $stmt = $conn->prepare(
        "SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen_emoji, c.nombre AS categoria_nombre, c.id AS categoria_id
         FROM productos p JOIN categorias c ON p.categoria_id = c.id
         WHERE p.disponible = 1 AND p.categoria_id = ?
         ORDER BY p.id ASC"
    );
    $stmt->bind_param("i", $categoria_filtro);
    $stmt->execute();
    $resProd = $stmt->get_result();
} else {
    $resProd = $conn->query(
        "SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen_emoji, c.nombre AS categoria_nombre, c.id AS categoria_id
         FROM productos p JOIN categorias c ON p.categoria_id = c.id
         WHERE p.disponible = 1
         ORDER BY p.id ASC"
    );
}

$productos = [];
while ($row = $resProd->fetch_assoc()) {
    $productos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Café Origen Cusco | Cafetería de especialidad</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          cafe: '#3d2314',
          terracota: '#c86d51',
          crema: '#fdfbf7'
        },
        fontFamily: {
          display: ['"Playfair Display"', 'serif'],
          body: ['Inter', 'sans-serif']
        }
      }
    }
  }
</script>
<style>
  body { font-family: 'Inter', sans-serif; background-color: #fdfbf7; }
  h1, h2, h3, .font-display { font-family: 'Playfair Display', serif; }
  #carrito-panel { transition: transform 0.3s ease-in-out; }
</style>
</head>
<body class="text-cafe">

<!-- ===================== NAVBAR ===================== -->
<header class="bg-cafe text-crema sticky top-0 z-40 shadow-lg">
  <div class="max-w-6xl mx-auto flex items-center justify-between px-5 py-4">
    <span class="font-display text-2xl font-bold tracking-wide">Café Origen <span class="text-terracota">Cusco</span></span>
    <nav class="hidden md:flex gap-6 text-sm font-medium">
      <a href="#tienda" class="hover:text-terracota transition">Tienda</a>
      <a href="#nosotros" class="hover:text-terracota transition">Nosotros</a>
      <a href="#reserva" class="hover:text-terracota transition">Reservas</a>
    </nav>
    <button onclick="toggleCarrito()" class="relative bg-terracota hover:bg-opacity-90 transition px-4 py-2 rounded-full text-sm font-semibold flex items-center gap-2">
      🛒 Carrito
      <span id="carrito-contador" class="bg-cafe text-crema text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
    </button>
  </div>
</header>

<!-- ===================== HERO ===================== -->
<section class="bg-cafe text-crema py-20 px-5 text-center">
  <h1 class="font-display text-4xl md:text-5xl font-bold mb-4">Café de altura, historia de Cusco</h1>
  <p class="max-w-xl mx-auto text-crema/80 mb-8">Bebidas de especialidad y granos seleccionados a mano en las alturas de La Convención y Quillabamba, tostados en pequeños lotes.</p>
  <a href="#tienda" class="bg-terracota px-6 py-3 rounded-full font-semibold hover:bg-opacity-90 transition inline-block">Ver la tienda</a>
</section>

<!-- ===================== TIENDA ===================== -->
<section id="tienda" class="max-w-6xl mx-auto px-5 py-16">
  <h2 class="font-display text-3xl font-bold text-center mb-2">Nuestra Tienda</h2>
  <p class="text-center text-cafe/60 mb-10">Bebidas preparadas al momento y granos listos para llevar a casa</p>

  <!-- Filtro por categoría -->
  <div class="flex flex-wrap justify-center gap-3 mb-10">
    <a href="index.php" class="px-5 py-2 rounded-full border font-medium transition <?= $categoria_filtro === 0 ? 'bg-terracota text-crema border-terracota' : 'border-cafe/20 text-cafe hover:border-terracota' ?>">
      Todos
    </a>
    <?php foreach ($categorias as $cat): ?>
      <a href="index.php?categoria=<?= (int) $cat['id'] ?>#tienda"
         class="px-5 py-2 rounded-full border font-medium transition <?= $categoria_filtro === (int) $cat['id'] ? 'bg-terracota text-crema border-terracota' : 'border-cafe/20 text-cafe hover:border-terracota' ?>">
        <?= htmlspecialchars($cat['nombre']) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Grid de productos (renderizado dinámico desde MySQL) -->
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (count($productos) === 0): ?>
      <p class="col-span-full text-center text-cafe/50">No hay productos disponibles en esta categoría por el momento.</p>
    <?php endif; ?>

    <?php foreach ($productos as $p): ?>
      <div class="bg-white border border-cafe/10 rounded-2xl p-6 shadow-sm hover:shadow-xl transition flex flex-col">
        <div class="text-5xl mb-4"><?= htmlspecialchars($p['imagen_emoji']) ?></div>
        <span class="text-xs uppercase tracking-wide text-terracota font-semibold mb-1"><?= htmlspecialchars($p['categoria_nombre']) ?></span>
        <h3 class="font-display text-xl font-bold mb-2"><?= htmlspecialchars($p['nombre']) ?></h3>
        <p class="text-sm text-cafe/60 mb-4 flex-1"><?= htmlspecialchars($p['descripcion']) ?></p>
        <div class="flex items-center justify-between mt-auto">
          <span class="font-display text-lg font-bold">S/ <?= number_format((float) $p['precio'], 2) ?></span>
          <button
            onclick="agregarAlCarrito(<?= (int) $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', <?= (float) $p['precio'] ?>)"
            class="bg-cafe text-crema text-sm font-semibold px-4 py-2 rounded-full hover:bg-terracota transition">
            Añadir al carrito
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===================== NOSOTROS ===================== -->
<section id="nosotros" class="bg-terracota/10 py-16 px-5">
  <div class="max-w-4xl mx-auto text-center">
    <h2 class="font-display text-3xl font-bold mb-4">Nuestra historia</h2>
    <p class="text-cafe/70 leading-relaxed">Café Origen Cusco nace en el corazón de la ciudad imperial con un propósito simple: acercar el café de las alturas cusqueñas a cada taza. Trabajamos directamente con caficultores de La Convención y Quillabamba, tostando en pequeños lotes para resaltar el carácter único de cada origen.</p>
  </div>
</section>

<!-- ===================== RESERVAS / CONTACTO ===================== -->
<section id="reserva" class="max-w-2xl mx-auto px-5 py-16">
  <h2 class="font-display text-3xl font-bold text-center mb-2">Reserva tu mesa</h2>
  <p class="text-center text-cafe/60 mb-8">Cuéntanos para cuándo y te confirmamos tu espacio</p>

  <form method="POST" action="index.php#reserva" id="form-reserva" class="bg-white border border-cafe/10 rounded-2xl p-8 shadow-sm space-y-4">
    <input type="hidden" name="accion" value="reserva">
    <div>
      <label class="block text-sm font-medium mb-1">Nombre completo</label>
      <input type="text" name="nombre" required class="w-full border border-cafe/20 rounded-lg px-4 py-2 focus:outline-none focus:border-terracota" placeholder="Tu nombre">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Correo electrónico</label>
      <input type="email" name="email" required class="w-full border border-cafe/20 rounded-lg px-4 py-2 focus:outline-none focus:border-terracota" placeholder="tucorreo@ejemplo.com">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Fecha deseada</label>
      <input type="date" name="fecha" class="w-full border border-cafe/20 rounded-lg px-4 py-2 focus:outline-none focus:border-terracota">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Mensaje (opcional)</label>
      <textarea name="mensaje" rows="3" class="w-full border border-cafe/20 rounded-lg px-4 py-2 focus:outline-none focus:border-terracota" placeholder="Número de personas, ocasión especial, etc."></textarea>
    </div>
    <button type="submit" class="w-full bg-cafe text-crema font-semibold py-3 rounded-full hover:bg-terracota transition">Enviar reserva</button>
  </form>
</section>

<!-- ===================== FOOTER ===================== -->
<footer class="bg-cafe text-crema/70 text-center py-8 px-5 text-sm">
  <p>© <?= date('Y') ?> Café Origen Cusco · Hecho con cariño en la ciudad imperial</p>
</footer>

<!-- ===================== CARRITO LATERAL ===================== -->
<div id="carrito-panel" class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-2xl z-50 translate-x-full flex flex-col">
  <div class="flex items-center justify-between p-5 border-b border-cafe/10">
    <h3 class="font-display text-xl font-bold">Tu carrito</h3>
    <button onclick="toggleCarrito()" class="text-2xl leading-none">&times;</button>
  </div>
  <div id="carrito-items" class="flex-1 overflow-y-auto p-5 space-y-4">
    <p id="carrito-vacio" class="text-cafe/50 text-sm">Aún no añadiste productos.</p>
  </div>
  <div class="p-5 border-t border-cafe/10">
    <div class="flex items-center justify-between mb-4">
      <span class="font-semibold">Total</span>
      <span id="carrito-total" class="font-display text-xl font-bold">S/ 0.00</span>
    </div>
    <button onclick="finalizarCompra()" class="w-full bg-terracota text-crema font-semibold py-3 rounded-full hover:bg-cafe transition">Finalizar compra</button>
  </div>
</div>
<div id="carrito-overlay" onclick="toggleCarrito()" class="fixed inset-0 bg-black/40 z-40 hidden"></div>

<!-- ===================== MODAL RESERVA EXITOSA ===================== -->
<?php if ($reserva_ok): ?>
<div id="modal-exito" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-5">
  <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl">
    <div class="text-5xl mb-4">✅</div>
    <h3 class="font-display text-2xl font-bold mb-2">¡Gracias, <?= $reserva_nombre ?: 'visitante' ?>!</h3>
    <p class="text-cafe/60 mb-6">Tu reserva fue recibida correctamente. Te contactaremos pronto para confirmar los detalles.</p>
    <button onclick="document.getElementById('modal-exito').remove()" class="bg-cafe text-crema font-semibold px-6 py-2 rounded-full hover:bg-terracota transition">Cerrar</button>
  </div>
</div>
<?php endif; ?>

<script>
  // ---------------------------------------------------------
  // Carrito de compras (simulado en JS, sin persistencia en BD)
  // ---------------------------------------------------------
  let carrito = [];

  function toggleCarrito() {
    document.getElementById('carrito-panel').classList.toggle('translate-x-full');
    document.getElementById('carrito-overlay').classList.toggle('hidden');
  }

  function agregarAlCarrito(id, nombre, precio) {
    const existente = carrito.find(item => item.id === id);
    if (existente) {
      existente.cantidad += 1;
    } else {
      carrito.push({ id: id, nombre: nombre, precio: precio, cantidad: 1 });
    }
    renderCarrito();
    if (document.getElementById('carrito-panel').classList.contains('translate-x-full')) {
      toggleCarrito();
    }
  }

  function quitarDelCarrito(id) {
    carrito = carrito.filter(item => item.id !== id);
    renderCarrito();
  }

  function renderCarrito() {
    const contenedor = document.getElementById('carrito-items');
    const contador = document.getElementById('carrito-contador');
    const totalEl = document.getElementById('carrito-total');

    if (carrito.length === 0) {
      contenedor.innerHTML = '<p id="carrito-vacio" class="text-cafe/50 text-sm">Aún no añadiste productos.</p>';
      contador.textContent = '0';
      totalEl.textContent = 'S/ 0.00';
      return;
    }

    let html = '';
    let total = 0;
    let cantidadTotal = 0;

    carrito.forEach(item => {
      const subtotal = item.precio * item.cantidad;
      total += subtotal;
      cantidadTotal += item.cantidad;
      html += `
        <div class="flex items-center justify-between gap-3 border-b border-cafe/10 pb-3">
          <div>
            <p class="font-semibold text-sm">${item.nombre}</p>
            <p class="text-xs text-cafe/50">S/ ${item.precio.toFixed(2)} x ${item.cantidad}</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="font-semibold text-sm">S/ ${subtotal.toFixed(2)}</span>
            <button onclick="quitarDelCarrito(${item.id})" class="text-terracota hover:text-cafe text-lg leading-none">&times;</button>
          </div>
        </div>
      `;
    });

    contenedor.innerHTML = html;
    contador.textContent = cantidadTotal;
    totalEl.textContent = 'S/ ' + total.toFixed(2);
  }

  function finalizarCompra() {
    if (carrito.length === 0) {
      alert('Tu carrito está vacío. Añade algún producto antes de continuar.');
      return;
    }
    const total = carrito.reduce((acc, item) => acc + item.precio * item.cantidad, 0);
    alert('¡Compra simulada con éxito! Total a pagar: S/ ' + total.toFixed(2) + '\nGracias por tu preferencia.');
    carrito = [];
    renderCarrito();
    toggleCarrito();
  }
</script>

</body>
</html>
