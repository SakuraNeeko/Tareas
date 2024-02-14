<?php
// Verificar si se ha proporcionado un ID de tarea válido
if (isset($_GET['id']) && !empty($_GET['id'])) {
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

    // Obtener el ID de la tarea desde la solicitud GET
    $id = $_GET['id'];

    // Actualizar el estado de la tarea en la base de datos
    $sql = "UPDATE tareas SET completada = 1 WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Si se actualizó correctamente, retornar un mensaje de éxito
        echo "La tarea ha sido marcada como completada";
    } else {
        // Si ocurrió un error, retornar el mensaje de error
        echo "Error al marcar la tarea como completada: " . $conn->error;
    }

    // Cerrar conexión
    $conn->close();
} else {
    echo "ID de tarea no válido";
}
?>
