<?php
// Importamos el controlador y capturamos procesos activos de formularios
require_once '../../controllers/MenuController.php';
$controlador = new MenuController();

// Ejecutamos validaciones del controlador en caso de operaciones POST
$controlador->procesarFormularioAdmin();

// Solicitamos la colección actualizada de platillos contenidos en el XML
$menuData = $controlador->mostrarMenu();

// Mecanismo interactivo para precargar datos en caso de que se solicite una edición (GET)
$platilloAEditar = null;
if (isset($_GET['editar_id']) && !is_string($menuData)) {
    foreach ($menuData->platillo as $item) {
        if ((string)$item->id === $_GET['editar_id']) {
            $platilloAEditar = $item;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Menú - Cuco's Cafe</title>
    <link rel="stylesheet" href="../../assets/estilos.css">
</head>
<body>

    <header class="main-header">
        <div class="nav-container">
            <div class="brand">
                <img src="../../assets/Pictures/Logotipo.png" alt="Logo" class="logo">
                <h1>Cuco's Admin Menú</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="admin.php" style="color: var(--coffee); text-decoration: none; font-weight: bold;">← Volver al Dashboard</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="page-header">
        <h1>⚙️ Gestión del Menú Dinámico</h1>
        <p>"Agrega, edita o elimina los productos del catálogo"</p>
    </div>

    <main style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
        
        <section style="margin-bottom: 4rem; background: #fff; padding: 2rem; border-radius: 1rem; box-shadow: var(--shadow-card);">
            <h2><?php echo $platilloAEditar ? '📝 Editar Platillo Existente' : '➕ Agregar Nuevo Producto'; ?></h2>
            
            <form method="POST" action="menu_admin.php" style="margin-top: 1.5rem;">
                <input type="hidden" name="accion" value="<?php echo $platilloAEditar ? 'actualizar' : 'agregar'; ?>">
                
                <?php if ($platilloAEditar): ?>
                    <input type="hidden" name="id" value="<?php echo $platilloAEditar->id; ?>">
                <?php endif; ?>

                <fieldset style="border: 1px solid var(--coffee-light); padding: 1.5rem; border-radius: 0.5rem;">
                    <legend style="font-weight: bold; color: var(--coffee); padding: 0 0.5rem;">Información del Producto</legend>
                    
                    <label for="nombre">Nombre Completo: <span style="color: red;">*</span></label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej: Capuchino Irlandés" 
                           value="<?php echo $platilloAEditar ? $platilloAEditar->nombre : ''; ?>">

                    <label for="categoria">Categoría del Menú: <span style="color: red;">*</span></label>
                    <select id="categoria" name="categoria" required>
                        <option value="">-- Selecciona una categoría --</option>
                        <option value="Bebidas Calientes" <?php echo ($platilloAEditar && $platilloAEditar->categoria == 'Bebidas Calientes') ? 'selected' : ''; ?>>Bebidas Calientes</option>
                        <option value="Bebidas Frías" <?php echo ($platilloAEditar && $platilloAEditar->categoria == 'Bebidas Frías') ? 'selected' : ''; ?>>Bebidas Frías</option>
                        <option value="Alimentos y Postres" <?php echo ($platilloAEditar && $platilloAEditar->categoria == 'Alimentos y Postres') ? 'selected' : ''; ?>>Alimentos y Postres</option>
                    </select>

                    <label for="tamano">Tamaño / Porción:</label>
                    <input type="text" id="tamano" name="tamano" placeholder="Ej: Mediano (12oz) o 1 pieza" 
                           value="<?php echo $platilloAEditar ? $platilloAEditar->tamano : ''; ?>">

                    <label for="precio">Precio de Venta ($): <span style="color: red;">*</span></label>
                    <input type="number" id="precio" name="precio" required min="1" step="0.01" placeholder="Ej: 45" 
                        value="<?php echo $platilloAEditar ? $platilloAEditar->precio : ''; ?>">

                    <label for="descripcion">Descripción Breve:</label>
                    <textarea id="descripcion" name="descripcion" rows="3" placeholder="Ingredientes o notas del chef..."><?php echo $platilloAEditar ? $platilloAEditar->descripcion : ''; ?></textarea>
                </fieldset>

                <div style="text-align: center; margin-top: 1.5rem;">
                    <button type="submit"><?php echo $platilloAEditar ? '💾 Guardar Cambios' : '📤 Registrar Producto'; ?></button>
                    <?php if ($platilloAEditar): ?>
                        <a href="menu_admin.php" style="display:inline-block; margin-left:10px; padding: 10px 20px; background:#888; color:#fff; text-decoration:none; border-radius:4px;">Cancelar Edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section style="background: #fff; padding: 2rem; border-radius: 1rem; box-shadow: var(--shadow-card);">
            <h2>📋 Inventario de Productos en el Menú</h2>
            
            <div class="table-container" style="margin-top: 1.5rem;">
                <?php if (is_string($menuData)): ?>
                    <p style="color:red; text-align:center;"><strong><?php echo $menuData; ?></strong></p>
                <?php else: ?>
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Tamaño</th>
                                <th>Precio</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menuData->platillo as $item): ?>
                            <tr>
                                <td><strong><?php echo $item->nombre; ?></strong></td>
                                <td><?php echo $item->categoria; ?></td>
                                <td><?php echo $item->tamano; ?></td>
                                <td>$<?php echo $item->precio; ?></td>
                                <td><?php echo $item->descripcion; ?></td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="menu_admin.php?editar_id=<?php echo $item->id; ?>" class="btn-primary" style="text-decoration:none; font-size:0.85rem; padding: 5px 10px; background: #4a3728;">Editar</a>
                                        
                                        <form method="POST" action="menu_admin.php" onsubmit="return confirm('¿De verdad deseas eliminar este producto?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $item->id; ?>">
                                            <button type="submit" class="btn-delete" style="font-size:0.85rem; padding: 5px 10px; background:#c0392b; color:white; border:none; cursor:pointer; border-radius:4px;">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content" style="display: flex; justify-content: center;">
            <p>Sistema de Gestión de Menú Dinámico - Cuco's Cafe 2026 🐕</p>
        </div>
    </footer>

</body>
</html>