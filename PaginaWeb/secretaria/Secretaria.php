<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretaría</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Barras verdes -->
     <?php include("nav_secre.php")?>
     <div class="container d-flex justify-content-center align-items-center flex-grow-1">
        <h2>Bienvenido a la secretaría 😊 <span id="nombre"></span></h2>
    </div>
    <div class="container text-center">
        <img src="vetsol.jpg" alt="Imagen Principal" class="img-fluid">
    </div>

            

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href='../hola.php';  // Redirigir a la página de login o inicio
        }
    </script>
</body>
</html>
