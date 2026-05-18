<?php
require_once __DIR__ . '/../models/MenuModel.php';

class MenuController {
    private $modelo;

    public function __construct() {
        try {
            $this->modelo = new MenuModel();
        } catch (Exception $e) {
            // Si la conexión a la BD falla, guardamos el mensaje de error amigable
            $this->modelo = $e->getMessage();
        }
    }

    public function mostrarMenu() {
        if (is_string($this->modelo)) {
            return $this->modelo; // Retorna el error de conexión si existe
        }
        return $this->modelo->obtenerPlatillos();
    }

    public function procesarFormularioAdmin() {
        if (is_string($this->modelo)) return; // Si no hay BD, no procesa acciones

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion'])) {
            
            // Acción: AGREGAR
            if ($_POST['accion'] == 'agregar') {
                if (empty($_POST['nombre']) || empty($_POST['precio']) || empty($_POST['categoria'])) {
                    echo "<script>alert('Error: Los campos Nombre, Precio y Categoría son obligatorios.');</script>";
                } elseif ($_POST['precio'] <= 0) {
                    echo "<script>alert('Error: El precio de venta debe ser mayor a $0.'); window.history.back();</script>";
                } else {
                    try {
                        $this->modelo->agregarPlatillo($_POST);
                        echo "<script>alert('Platillo agregado correctamente a la Base de Datos.'); window.location.href='menu_admin.php';</script>";
                    } catch (Exception $e) {
                        echo "<script>alert('" . $e->getMessage() . "');</script>";
                    }
                }
            }
            
            // Acción: ACTUALIZAR
            if ($_POST['accion'] == 'actualizar') {
                if (empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['precio']) || empty($_POST['categoria'])) {
                    echo "<script>alert('Error: Todos los campos son requeridos para actualizar.');</script>";
                } elseif ($_POST['precio'] <= 0) {
                    echo "<script>alert('Error: El precio de venta debe ser mayor a $0.'); window.history.back();</script>";
                } else {
                    try {
                        $this->modelo->actualizarPlatillo($_POST);
                        echo "<script>alert('Platillo actualizado con éxito en la Base de Datos.'); window.location.href='menu_admin.php';</script>";
                    } catch (Exception $e) {
                        echo "<script>alert('" . $e->getMessage() . "');</script>";
                    }
                }
            }
            
            // Acción: ELIMINAR
            if ($_POST['accion'] == 'eliminar' && !empty($_POST['id'])) {
                try {
                    $this->modelo->eliminarPlatillo($_POST['id']);
                    echo "<script>alert('Platillo eliminado del sistema.'); window.location.href='menu_admin.php';</script>";
                } catch (Exception $e) {
                    echo "<script>alert('" . $e->getMessage() . "');</script>";
                }
            }
        }
    }
}
?>