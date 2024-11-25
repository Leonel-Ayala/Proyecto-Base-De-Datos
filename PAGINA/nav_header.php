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
                            <li><a class="dropdown-item" href="Gestionar_usuarios.php">Gestionar Usuarios</a></li>
                            <li><a class="dropdown-item" href="Gestionar_productos.php">Gestionar productos</a></li>
                            <li><a class="dropdown-item" href="Gestionar_regiones.php">Gestionar Regiones</a></li>
                            <li><a class="dropdown-item" href="Gestionar_comunas.php">Gestionar Comunas</a></li>
                            <li><a class="dropdown-item" href="Gestionar_calles.php">Gestionar Calles</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Consultar
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="Reporte_log.php">Reporte De Ingreso</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="https://www.instagram.com/laroatlb_developers/">Contactanos</a>
                    </li>
                </ul>
            </div>
            <!-- Botón de cerrar sesión en la esquina derecha -->
            <button class="btn btn-outline-light" style="position: absolute; right: 20px;" onclick="cerrarSesion()">Cerrar Sesión</button>
        </div>
    </nav>