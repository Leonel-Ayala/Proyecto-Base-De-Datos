<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretaría</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://static.vecteezy.com/system/resources/previews/005/221/039/non_2x/animals-cartoon-living-in-the-wild-nature-vector.jpg');
            background-repeat: no-repeat;
            background-size: cover;
        }
        .side-bar {
            width: 30px;
            background-color: green;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Barras verdes -->
    <div class="side-bar position-fixed top-0 bottom-0 start-0"></div>
    <div class="side-bar position-fixed top-0 bottom-0 end-0"></div>

    <!-- Contenedor de "Cerrar sesión" -->
    <div class="position-fixed top-0 end-0 m-3">
        <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
    </div>

    <!-- Contenedor principal centrado -->
    <div class="container d-flex flex-column align-items-center justify-content-center flex-grow-1">
        <h2 class="mb-4 text-center">Bienvenido Administrador</h2>
        <p class="text-center">¿Que desea realizar?</p>

        <!-- Botones para acciones -->
        <a href="registrar_cita.php" class="btn btn-success btn-lg mt-3">Registrar Veterinario</a>
        
        <?php include("listado_veterinarios.php") ?>
        <select class="form-select" aria-label="Default select example" id="urlSelector">
    <option selected>Open this select menu</option>
    <option value="hola.php">One</option>
    <option value="registrar.php">Two</option>
    <option value="https://example.com/three">Three</option>
    </select>


        <a href="registrar_cliente.php" class="btn btn-success btn-lg mt-3">Registrar Cliente</a>
        <a href="borrar_cliente.php" class="btn btn-danger btn-lg mt-3">Borrar Cliente</a>
        <a href="borrar_cita.php" class="btn btn-danger btn-lg mt-3">Borrar Cita</a>
        <a href="actualizar_cliente.php" class="btn btn-primary btn-lg mt-3">Actualizar Cliente</a>
        <a href="actualizar_cita.php" class="btn btn-primary btn-lg mt-3">Actualizar Cita</a>
        <a href="consultar_ficha.php" class="btn btn-info btn-lg mt-3">Consultar Ficha Clínica</a>   
    </div>

    <div class=" container d-flex flex-column align-items-center justify-content-right flex-grow-1">
    <a href="registrar_cita.php" class="btn btn-success btn-lg mt-3">Registrar Veterinario</a>
        <a href="registrar_cliente.php" class="btn btn-success btn-lg mt-3">Registrar Cliente</a>
        <a href="borrar_cliente.php" class="btn btn-danger btn-lg mt-3">Borrar Cliente</a>
        <a href="borrar_cita.php" class="btn btn-danger btn-lg mt-3">Borrar Cita</a>
        <a href="actualizar_cliente.php" class="btn btn-primary btn-lg mt-3">Actualizar Cliente</a>
        <a href="actualizar_cita.php" class="btn btn-primary btn-lg mt-3">Actualizar Cita</a>
        <a href="consultar_ficha.php" class="btn btn-info btn-lg mt-3">Consultar Ficha Clínica</a>   
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<script>
    // Obtén el selector por su ID
    const selector = document.getElementById("urlSelector");

    // Agrega un evento que detecte el cambio de opción
    selector.addEventListener("change", function () {
        const selectedValue = this.value; // Obtén el valor seleccionado
        if (selectedValue) {
            window.location.href = selectedValue; // Redirige a la URL
        }
    });
</script>

