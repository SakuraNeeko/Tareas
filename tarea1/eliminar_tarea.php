<?php
// Verificar si se recibió el parámetro 'id'
if (isset($_GET['id'])) {
    // Obtener el ID de la tarea a eliminar
    $id = $_GET['id'];

    // Establecer conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "tareasDB";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Ejecutar consulta SQL para eliminar la tarea
    $sql = "DELETE FROM tareas WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // La tarea se eliminó correctamente
        echo "La tarea se eliminó correctamente.";
    } else {
        // Ocurrió un error al eliminar la tarea
        echo "Error al eliminar la tarea: " . $conn->error;
    }

    // Cerrar conexión
    $conn->close();
} else {
    // Si no se proporcionó el parámetro 'id'
    echo "No se proporcionó el parámetro 'id'.";
}
?>
