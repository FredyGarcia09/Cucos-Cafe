<?php
// Importamos el controlador para cargar los mensajes reales del XML
require_once __DIR__ . '/../../controllers/ContactoController.php';

// Verificamos si el usuario inició sesión correctamente en PHP
if (!isset($_SESSION['usuarioLogueado'])) {
    header("Location: login.php");
    exit();
}

// Obtenemos los mensajes usando el método del controlador
$mensajes = $controlador->mostrarMensajes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Cuco's Cafe</title>
    <link rel="stylesheet" href="../../assets/estilos.css">
    </head>
<body>

    <header class="main-header">
        <div class="nav-container">
            <div class="brand">
                <img src="../../assets/Pictures/Logotipo.png" alt="Logo" class="logo">
                <h1>Cuco's Admin</h1>
            </div>
            <nav>
                <ul style="display: flex; align-items: center; gap: 1rem;">
                    <li><span style="font-weight: bold; color: var(--coffee);">Hola, <?php echo htmlspecialchars($_SESSION['usuarioLogueado']); ?> 👋</span></li>
                    <li>
                        <form action="../../controllers/ContactoController.php" method="POST" style="padding: 0; box-shadow: none; margin: 0; background: transparent; width: auto;">
                            <input type="hidden" name="accion" value="logout">
                            <button type="submit" class="btn-delete" style="margin: 0; background-color: var(--coffee); color: white; border: none; cursor: pointer;">Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="page-header">
        <h1>⚙️ Panel de Control</h1>
        <p>"Gestiona los mensajes de tus clientes y el menú del café"</p>
    </div>

    <main>
        <section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="text-align: left; margin: 0;">Bandeja de Entrada</h2>
                    <p style="color: #666;">Mensajes recibidos desde el formulario web</p>
                </div>
                
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <a href="menu_admin.php" class="btn-primary" style="text-decoration:none; padding: 10px 20px; background-color: var(--coffee); color: white; border-radius: 50px; font-weight: bold;">
                        🍔 Gestionar Menú
                    </a>
    
                    <a href="../../data/contactos.xml" download="Respaldo_Contactos.xml" class="btn-admin-action" style="text-decoration: none;">
                        💾 Descargar Respaldo XML
                    </a>
                </div>
            </div>

            <div class="table-container">
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Mensaje</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mensajes)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 3rem; color: #666;">
                                    📭 No hay mensajes en la base de datos XML.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($mensajes as $msg): ?>
                                <tr>
                                    <td><strong>#<?php echo substr($msg->id, -4); ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($msg->nombre); ?></strong><br>
                                        <span style="font-size: 0.85rem; color: #666;"><?php echo htmlspecialchars($msg->email); ?></span>
                                    </td>
                                    <td><span class="badge-status"><?php echo htmlspecialchars($msg->asunto); ?></span></td>
                                    
                                    <td>
                                        <?php if($msg->estado === 'Atendido'): ?>
                                            <span style="background-color: #d4edda; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">✔ Atendido</span>
                                        <?php else: ?>
                                            <span style="background-color: #fff3cd; color: #f57f17; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">⏳ Pendiente</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?php echo nl2br(htmlspecialchars($msg->texto)); ?></td>
                                    
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <form action="../../controllers/ContactoController.php" method="POST" style="padding: 0; box-shadow: none; margin: 0; background: transparent; width: auto;">
                                                <input type="hidden" name="accion" value="actualizar_estado">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($msg->id); ?>">
                                                <button type="submit" style="background-color: #4CAF50; color: white; padding: 5px 10px; border-radius: 4px; border: none; font-size: 0.85rem; cursor: pointer;" title="Marcar como Atendido/Pendiente">
                                                    🔄 Estado
                                                </button>
                                            </form>

                                            <form action="../../controllers/ContactoController.php" method="POST" onsubmit="return confirm('¿De verdad deseas borrar este mensaje permanentemente?');" style="padding: 0; box-shadow: none; margin: 0; background: transparent; width: auto;">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($msg->id); ?>">
                                                <button type="submit" class="btn-delete" style="padding: 5px 10px; font-size: 0.85rem;">🗑️ Borrar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <p style="text-align: center; margin-top: 2rem; font-size: 0.9rem; color: #888; font-style: italic;">
                Nota: Los datos mostrados provienen directamente del archivo XML físico del servidor (Backend).
            </p>
        </section>
    </main>

    <footer>
        <div class="footer-content" style="display: flex; justify-content: center;">
            <p>Sistema de Gestión Interna - Cuco's Cafe 2026 🐕</p>
        </div>
    </footer>

</body>
</html>