<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Hub</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar con fondo verde -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success ">
        <div class="container-fluid">
            <!-- Imagen en el navbar -->
            <a class="navbar-brand" href="#">
                <img src="vetsol2.jpg" alt="Logo" style="width: px; height: 50px; margin-right: 10px;">
                Administrador
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="Super_Usuario_selector.php">Inicio</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Gestion
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="Gestionar_veterinarios.php">Gestionar Veterinarios</a></li>
                            <li><a class="dropdown-item" href="Gestionar_secretarias.php">Gestionar Secretarias</a></li>
                            <li><a class="dropdown-item" href="Gestionar_productos.php">Gestionar productos</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Consultar
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Consultar Clientes</a></li>
                            <li><a class="dropdown-item" href="#">Reporte De Ingreso</a></li>
                            <li><a class="dropdown-item" href="#">Reporte de producto mas usado</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="https://www.youtube.com/watch?v=HzTmU9OvyZ0&ab_channel=Lekuvege-Topic">Contactanos</a>
                    </li>
                </ul>
            </div>
            <!-- Botón de cerrar sesión en la esquina derecha -->
            <button class="btn btn-outline-light" style="position: absolute; right: 20px;" onclick="cerrarSesion()">Cerrar Sesión</button>
        </div>
    </nav>

    <!-- Contenedor para mostrar el mensaje de bienvenida -->
    <div class="container d-flex justify-content-center align-items-center flex-grow-1">
        <h2>Bienvenido Administrador 😊 <span id="nombre"></span></h2>
    </div>

    <!-- Imagen en el cuerpo de la página -->
    <div class="container text-center">
        <img src="vet.png" alt="Imagen Principal" class="img-fluid">
    </div>

    <!-- Optional JavaScript; choose one of the two! -->
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript para insertar el nombre dinámicamente -->
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href='hola.php';  // Redirigir a la página de login o inicio
        }
    </script>
</body>
</html>





