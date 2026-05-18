<?php
// 1. Llamamos al controlador
require_once '../../controllers/MenuController.php';
$controlador = new MenuController();
$menuData = $controlador->mostrarMenu();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../../assets/estilos.css">
    
    <title>Menú - Cuco's Cafe</title>
</head>
<body>

    <header class="main-header">
        <div class="nav-container">
            <a href="index.php" class="brand">
                
                <img src="../../assets/Pictures/Logotipo.png" alt="Logo" class="logo">
                
                <h1>Cuco's Cafe</h1>
            </a>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="menu.php" class="active">Menú</a></li>
                    <li><a href="ubicacion.php">Ubicación</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

<main>
        <section>
            <h2>☕ Nuestro Menú Completo</h2>
            <div class="table-container">
                
                <?php 
                // Rúbrica: Manejo básico de errores al intentar abrir la vista
                if (is_string($menuData)) {
                    // Si el modelo devolvió un texto (el error del catch), lo mostramos
                    echo "<p style='color:red; text-align:center;'><strong>" . $menuData . "</strong></p>";
                } else {
                ?>

                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Tamaño</th>
                            <th>Precio</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Ciclo para imprimir cada platillo del XML en tu diseño HTML
                        foreach ($menuData->platillo as $item) { 
                        ?>
                        <tr>
                            <td><?php echo $item->nombre; ?></td>
                            <td><?php echo $item->categoria; ?></td>
                            <td><?php echo $item->tamano; ?></td>
                            <td>$<?php echo $item->precio; ?></td>
                            <td><?php echo $item->descripcion; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                
                <?php } // Fin del if/else ?>
            </div>
        </section>