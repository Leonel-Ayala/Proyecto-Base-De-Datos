<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrar Datos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .form-container {
            width: 400px;
            padding: 20px;
            margin: auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2 class="text-center mb-4">Eliminar Registro</h2>
    <form action="borrar.php" method="POST">
        <!-- Input para el ID del registro a eliminar -->
        <div class="mb-3">
            <label for="id" class="form-label">ID del Registro</label>
            <input type="text" id="id" name="id" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-danger w-100">Eliminar</button>
    </form>
</div>

</body>
</html>