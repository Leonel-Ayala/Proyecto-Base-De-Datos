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
        <div class="collapse navbar-collapse " id="navbarNav">
            <ul class="navbar-nav justify-content-between">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="Super_Usuario_selector.php">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Gestion
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="Gestionar_veterinario.php">Gestionar Veterinarios</a></li>
                        <li><a class="dropdown-item" href="Gestionar_secre.php">Gestionar Secretarias</a></li>
                        <li><a class="dropdown-item" href="inventario.php">Gestionar productos</a></li>
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
             <!-- Botón para alternar el modo claro/oscuro debajo de la barra de navegación -->
            <!-- Botón de cerrar sesión en la esquina derecha -->
            <button class="btn btn-outline-light ms-auto" onclick="cerrarSesion()">Cerrar Sesión</button>
        </div>
       
    </div>
</nav>