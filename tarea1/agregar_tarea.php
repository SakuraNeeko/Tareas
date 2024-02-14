<?php
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

// Procesar la solicitud POST para agregar una nueva tarea
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener la descripción de la tarea desde el formulario
    $descripcion = $_POST['descripcion'];

    // Preparar la consulta SQL para insertar la nueva tarea en la base de datos
    $sql = "INSERT INTO tareas (descripcion) VALUES ('$descripcion')";

    // Ejecutar la consulta SQL
    if ($conn->query($sql) === TRUE) {
        // Devolver el ID de la nueva tarea para que pueda ser utilizado por JavaScript
        echo $conn->insert_id;
    } else {
        // Si hay un error al ejecutar la consulta SQL, devolver un mensaje de error
        echo "Error al agregar la tarea: " . $conn->error;
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
