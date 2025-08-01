<?php
// =============================================
// CONEXIÓN A BASE DE DATOS - VERSIÓN MEJORADA
// =============================================

// Configuración de la base de datos
$host = 'localhost';        // o '127.0.0.1'
$usuario = 'root';         // usuario por defecto de XAMPP
$contrasena = '';          // contraseña vacía por defecto en XAMPP
$base_datos = 'covercars'; // nombre de tu base de datos

// Configurar reporte de errores de MySQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Crear la conexión
    $conn = new mysqli($host, $usuario, $contrasena, $base_datos);
    
    // Configurar el conjunto de caracteres a UTF-8
    $conn->set_charset("utf8");
    
    // Mensaje de éxito (comentar en producción)
    // echo "Conexión exitosa a la base de datos";
    
} catch (mysqli_sql_exception $e) {
    // Manejo detallado de errores
    $error_message = "Error de conexión a la base de datos: " . $e->getMessage();
    
    // Log del error (opcional)
    error_log($error_message);
    
    // Mostrar mensaje de error amigable
    die("
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #dc3545; border-radius: 5px; background-color: #f8d7da; color: #721c24;'>
        <h2>🚨 Error de Conexión a la Base de Datos</h2>
        <p><strong>Problema:</strong> No se puede conectar con la base de datos MySQL.</p>
        
        <h3>💡 Posibles soluciones:</h3>
        <ol>
            <li><strong>Verificar XAMPP:</strong>
                <ul>
                    <li>Abre el panel de control de XAMPP</li>
                    <li>Asegúrate de que MySQL esté ejecutándose (botón Start)</li>
                    <li>Verifica que Apache también esté ejecutándose</li>
                </ul>
            </li>
            <li><strong>Verificar la base de datos:</strong>
                <ul>
                    <li>Abre phpMyAdmin (http://localhost/phpmyadmin)</li>
                    <li>Verifica que existe la base de datos 'covercars'</li>
                    <li>Si no existe, créala</li>
                </ul>
            </li>
            <li><strong>Verificar configuración:</strong>
                <ul>
                    <li>Host: localhost (o 127.0.0.1)</li>
                    <li>Usuario: root</li>
                    <li>Contraseña: (vacía en XAMPP por defecto)</li>
                    <li>Base de datos: covercars</li>
                </ul>
            </li>
        </ol>
        
        <p><strong>Error técnico:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        
        <div style='margin-top: 20px; padding: 15px; background-color: #d1ecf1; border-left: 4px solid #bee5eb;'>
            <strong>📞 ¿Necesitas ayuda?</strong><br>
            Si el problema persiste, verifica que:
            <ul>
                <li>XAMPP esté correctamente instalado</li>
                <li>No haya conflictos de puerto (puerto 3306 para MySQL)</li>
                <li>No tengas otros servicios MySQL ejecutándose</li>
            </ul>
        </div>
    </div>
    ");
}

// Función para probar la conexión (opcional)
function probarConexion() {
    global $conn;
    
    try {
        $resultado = $conn->query("SELECT 1");
        if ($resultado) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

// Función para crear la base de datos si no existe (opcional)
function crearBaseDatosComoFallback() {
    try {
        $host = 'localhost';
        $usuario = 'root';
        $contrasena = '';
        
        $conexion_temporal = new mysqli($host, $usuario, $contrasena);
        
        if ($conexion_temporal->connect_error) {
            return false;
        }
        
        // Crear base de datos si no existe
        $sql = "CREATE DATABASE IF NOT EXISTS covercars CHARACTER SET utf8 COLLATE utf8_general_ci";
        $conexion_temporal->query($sql);
        $conexion_temporal->close();
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>