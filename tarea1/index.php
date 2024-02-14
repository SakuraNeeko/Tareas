<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tareas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #fafafa;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        li button {
            margin-left: 10px;
            cursor: pointer;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
        }
        .completado {
            background-color: #4caf50;
            color: #fff;
        }
        .eliminar {
            background-color: #f44336;
            color: #fff;
        }
        .formulario {
            margin-top: 20px;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 70%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            padding: 8px 16px;
            border: none;
            background-color: #4caf50;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Tareas</h1>
        
        <ul id="lista-tareas">
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

            // Obtener tareas de la base de datos
            $sql = "SELECT * FROM tareas";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<li data-id='" . $row["id"] . "'>" . $row["descripcion"] . " ";
                    if ($row["completada"]) {
                        echo "<button class='completado' disabled>Completado</button>";
                    } else {
                        echo "<button class='completar' onclick='marcarCompletada(this, " . $row["id"] . ")'>Completar</button>";
                    }
                    echo "<button class='eliminar' onclick='eliminarTarea(" . $row["id"] . ")'>Eliminar</button></li>";
                }
            } else {
                echo "<li>No se encontraron tareas</li>";
            }

            // Cerrar conexión
            $conn->close();
            ?>
        </ul>

        <div class="formulario">
            <h2>Agregar Nueva Tarea</h2>
            <form id="form-tarea" onsubmit="return agregarTarea()">
                <label for="descripcion">Descripción:</label>
                <input type="text" id="descripcion" name="descripcion">
                <input type="submit" value="Agregar Tarea">
            </form>
        </div>
    </div>

    <script>
        // Al cargar la página, verificar si hay tareas completadas almacenadas en localStorage
        document.addEventListener("DOMContentLoaded", function() {
            var completadas = JSON.parse(localStorage.getItem('completadas'));
            if (completadas) {
                completadas.forEach(function(id) {
                    var botonCompletar = document.querySelector("button[data-id='" + id + "']");
                    if (botonCompletar) {
                        marcarCompletada(botonCompletar, id);
                    }
                });
            }
        });

        function marcarCompletada(boton, id) {
            // Cambiar texto del botón a "Completado"
            boton.innerHTML = "Completado";
            boton.className = "completado"; // Cambiar clase CSS
            boton.disabled = true; // Deshabilitar el botón después de completarlo

            // Guardar el estado de completado en localStorage
            var completadas = JSON.parse(localStorage.getItem('completadas')) || [];
            if (!completadas.includes(id)) {
                completadas.push(id);
                localStorage.setItem('completadas', JSON.stringify(completadas));
            //Esta parte si marca el estado, pero no se queda actualizado (revisar)
            }
        }

        function eliminarTarea(id) {
            // Enviar solicitud AJAX al servidor para eliminar la tarea
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "eliminar_tarea.php?id=" + id, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Quitar la tarea eliminada de la lista
                    var tareaEliminada = document.querySelector("li[data-id='" + id + "']");
                    if (tareaEliminada) {
                        tareaEliminada.parentNode.removeChild(tareaEliminada);
                    }
                }
            };
            xhr.send();
        }

        function agregarTarea() {
            // Obtener la descripción de la nueva tarea
            var descripcion = document.getElementById('descripcion').value.trim();
            if (descripcion === '') {
                alert('Por favor ingresa una descripción para la tarea.');
                return false;
            }

            // Enviar solicitud AJAX al servidor para agregar la nueva tarea
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "agregar_tarea.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Agregar la nueva tarea a la lista
                    var listaTareas = document.getElementById('lista-tareas');
                    var nuevaTarea = document.createElement('li');
                    nuevaTarea.textContent = descripcion + " ";
                    var botonCompletar = document.createElement('button');
                    botonCompletar.textContent = "Completar";
                    botonCompletar.className = "completar";
                    botonCompletar.onclick = function() {
                        marcarCompletada(botonCompletar, xhr.responseText);
                    };
                    nuevaTarea.appendChild(botonCompletar);
                    var botonEliminar = document.createElement('button');
                    botonEliminar.textContent = "Eliminar";
                    botonEliminar.className = "eliminar";
                    botonEliminar.onclick = function() {
                        eliminarTarea(xhr.responseText);
                    };
                    nuevaTarea.appendChild(botonEliminar);
                    nuevaTarea.setAttribute('data-id', xhr.responseText);
                    listaTareas.appendChild(nuevaTarea);
                    document.getElementById('descripcion').value = ''; // Limpiar el campo de descripción
                }
            };
            xhr.send("descripcion=" + descripcion);
            return false; // Evitar que el formulario se envíe de forma tradicional
        }
    </script>
</body>
</html>
