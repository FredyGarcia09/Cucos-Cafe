<?php
// Requerimos el modelo de forma segura usando la ruta absoluta del archivo
require_once __DIR__ . '/../models/MenuModel.php';

// Controlador para gestionar las rutas y validaciones del menú
class MenuController {
    private $modelo;

    public function __construct() {
        // Instanciamos el modelo al iniciar el controlador
        $this->modelo = new MenuModel();
    }

    // Obtiene la lista estructurada para enviarla a las vistas
    public function mostrarMenu() {
        return $this->modelo->obtenerPlatillos();
    }

    // Procesa de forma centralizada los envíos de formularios del Administrador (CUD)
    public function procesarFormularioAdmin() {
        // Validamos si se envió información mediante el método POST
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion'])) {
            
            // Caso de negocio 1: AGREGAR UN NUEVO PRODUCTO
            if ($_POST['accion'] == 'agregar') {
                // Rúbrica: Validación estricta con empty() para evitar campos o registros vacíos
                if (empty($_POST['nombre']) || empty($_POST['precio']) || empty($_POST['categoria'])) {
                    echo "<script>alert('Error: Los campos Nombre, Precio y Categoría son obligatorios.');</script>";
                } else {
                    $this->modelo->agregarPlatillo($_POST);
                    echo "<script>alert('Platillo agregado correctamente.'); window.location.href='menu_admin.php';</script>";
                }
            }
            
            // Caso de negocio 2: ACTUALIZAR UN PRODUCTO EXISTENTE
            if ($_POST['accion'] == 'actualizar') {
                // Validamos que contenga el ID de referencia y los datos obligatorios actualizados
                if (empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['precio']) || empty($_POST['categoria'])) {
                    echo "<script>alert('Error: Todos los campos son requeridos para actualizar.');</script>";
                } else {
                    $this->modelo->actualizarPlatillo($_POST);
                    echo "<script>alert('Platillo actualizado con éxito.'); window.location.href='menu_admin.php';</script>";
                }
            }
            
            // Caso de negocio 3: ELIMINAR UN PRODUCTO
            if ($_POST['accion'] == 'eliminar' && !empty($_POST['id'])) {
                $this->modelo->eliminarPlatillo($_POST['id']);
                echo "<script>alert('Platillo eliminado del sistema.'); window.location.href='menu_admin.php';</script>";
            }
        }
    }
}
?>