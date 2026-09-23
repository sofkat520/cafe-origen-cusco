# ☕ Café Origen Cusco

https://cafeorigencusco.freehosting.dev/#tienda

Sitio web funcional y tienda básica para **Café Origen Cusco**, una cafetería de especialidad en Cusco, Perú, que vende bebidas preparadas y granos de café de origen (La Convención, Quillabamba). Proyecto académico de desarrollo web con backend en **PHP 8 nativo** y base de datos **MySQL**.

## ✨ Características

- Catálogo de productos **100% dinámico**, leído desde MySQL con `mysqli`.
- Filtro por categoría (**Bebidas** / **Granos de Café**) mediante parámetros GET.
- Carrito de compras simulado en JavaScript (barra lateral con total calculado en tiempo real).
- Formulario de reserva/contacto con envío `POST` a la misma página y confirmación visual (modal de éxito).
- Frontend responsive con **Tailwind CSS** (vía CDN), tipografías de Google Fonts (`Playfair Display` + `Inter`) y paleta café/terracota/crema.
- Sin frameworks pesados: fácil de subir a cualquier hosting compartido con cPanel.

## 🧰 Requisitos

- PHP 8.0 o superior con extensión `mysqli` habilitada.
- MySQL 5.7+ o MariaDB.
- Acceso a **cPanel** con **phpMyAdmin** (o cualquier hosting compartido equivalente).

## 📁 Estructura de carpetas

```
cafe-origen-cusco/
├── config/
│   └── database.php     # Conexión mysqli a la base de datos
├── database.sql         # Script para crear la BD, tablas y datos de ejemplo
├── index.php             # Página principal + tienda dinámica + carrito + reservas
└── README.md
```

## 🚀 Instalación paso a paso (cPanel + phpMyAdmin)

1. **Sube los archivos**
   - Ingresa al **Administrador de archivos** de cPanel.
   - Sube toda la carpeta `cafe-origen-cusco` (o su contenido) dentro de `public_html` (o una subcarpeta si prefieres, por ejemplo `public_html/cafe`).

2. **Crea la base de datos**
   - En cPanel, entra a **phpMyAdmin**.
   - Haz clic en la pestaña **Importar**.
   - Selecciona el archivo `database.sql` de este proyecto y presiona **Continuar / Importar**.
   - Esto creará automáticamente la base de datos `cafe_origen_cusco`, las tablas `categorias` y `productos`, y los datos de ejemplo.

   > ⚠️ En muchos hostings compartidos, cPanel obliga a que el nombre de la base de datos y del usuario lleven el prefijo de tu cuenta (ej. `usuario_cafeorigen`). Si es tu caso, crea la base de datos desde **"Bases de datos MySQL"** en cPanel con ese nombre y luego importa el archivo `database.sql` sobre esa base ya creada (edita la línea `CREATE DATABASE` si es necesario).

3. **Crea un usuario de MySQL y asígnalo a la base de datos**
   - En cPanel, ve a **"Bases de datos MySQL"**.
   - Crea un usuario y una contraseña.
   - Añade ese usuario a la base de datos `cafe_origen_cusco` con todos los privilegios.

4. **Configura la conexión**
   - Abre `config/database.php` y reemplaza las variables con tus datos reales:
     ```php
     $host = "localhost";
     $user = "usuario_cafeorigen";
     $pass = "tu_contraseña";
     $db   = "usuario_cafeorigen_cafe_origen_cusco";
     ```

5. **Verifica el sitio**
   - Visita `https://tudominio.com/` (o `https://tudominio.com/cafe/` si lo subiste en subcarpeta).
   - Deberías ver el catálogo de productos cargado dinámicamente desde la base de datos.

## 🧪 Pruebas locales (opcional)

Si quieres probarlo en tu computadora antes de subirlo:

```bash
php -S localhost:8000
```

Y en otra terminal, con MySQL corriendo localmente, importa `database.sql` con:

```bash
mysql -u root -p < database.sql
```

## 🗄️ Modelo de datos

- **categorias**: `id`, `nombre`, `slug`, `creado_en`
- **productos**: `id`, `categoria_id` (FK), `nombre`, `descripcion`, `precio`, `imagen_emoji`, `disponible`, `creado_en`

## 👤 Autor

Proyecto desarrollado como entregable académico para el curso de Desarrollo Web — **Café Origen Cusco**, Cusco, Perú.

## 📄 Licencia

Uso académico / educativo.
