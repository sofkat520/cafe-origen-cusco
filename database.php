<?php
/**
 * Café Origen Cusco - Conexión a Base de Datos (mysqli)
 * Cambia estas 4 variables según tu panel de cPanel / phpMyAdmin.
 * En hosting compartido el host casi siempre es "localhost"
 * y el usuario/base suelen llevar el prefijo de tu cuenta,
 * ej: "usuario_cafeorigen".
 */

$host = "localhost";
$user = "root";
$pass = "";
$db   = "cafe_origen_cusco";

// Activamos el modo de reporte de errores de mysqli para capturarlos con try/catch
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Mensaje legible para el usuario final, sin exponer datos sensibles del servidor
    http_response_code(500);
    die("
        <div style='font-family:sans-serif;max-width:600px;margin:60px auto;padding:24px;
                     background:#fdfbf7;border:1px solid #c86d51;border-radius:12px;color:#3d2314'>
            <h2 style='margin-top:0'>No se pudo conectar a la base de datos</h2>
            <p>Verifica que en <strong>config/database.php</strong> los datos de \$host, \$user, \$pass y \$db
               coincidan con los que creaste en phpMyAdmin de cPanel.</p>
            <p style='font-size:13px;color:#8a5a45'>Detalle técnico: " . htmlspecialchars($e->getMessage()) . "</p>
        </div>
    ");
}
