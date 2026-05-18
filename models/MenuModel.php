<?php
// Modelo encargado de gestionar las operaciones CRUD en la base de datos MySQL
class MenuModel {
    private $conexion;

    public function __construct() {
        try {
            // Rúbrica: Conexión segura usando PDO a MySQL (XAMPP usa usuario 'root' y contraseña vacía)
            $this->conexion = new PDO("mysql:host=localhost;dbname=cucos_cafe;charset=utf8", "root", "root");
            // Configurar para que lance excepciones en caso de fallos técnicos
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Esto nos dirá exactamente por qué MySQL está rechazando la conexión
            throw new Exception("ERROR REAL DE MYSQL: " . $e->getMessage());
        }
    }

    // (R) READ: Consultar todos los registros de la tabla menu
    public function obtenerPlatillos() {
        try {
            $sql = "SELECT * FROM menu";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            // Retornamos como objetos FETCH_OBJ para no romper la sintaxis de tus vistas ($item->nombre)
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return "Error temporal en el sistema: No se pudieron cargar los productos del menú.";
        }
    }

    // (C) CREATE: Insertar un nuevo platillo/bebida
    public function agregarPlatillo($datos) {
        try {
            $sql = "INSERT INTO menu (nombre, categoria, tamano, precio, descripcion) VALUES (:nombre, :categoria, :tamano, :precio, :descripcion)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':nombre'      => htmlspecialchars($datos['nombre']),
                ':categoria'   => htmlspecialchars($datos['categoria']),
                ':tamano'      => htmlspecialchars($datos['tamano']),
                ':precio'      => htmlspecialchars($datos['precio']),
                ':descripcion' => htmlspecialchars($datos['descripcion'])
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error del sistema: No se pudo registrar el nuevo platillo.");
        }
    }

    // (U) UPDATE: Actualizar precio, descripción u otros campos de un ID existente
    public function actualizarPlatillo($datos) {
        try {
            $sql = "UPDATE menu SET nombre = :nombre, categoria = :categoria, tamano = :tamano, precio = :precio, descripcion = :descripcion WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':id'          => htmlspecialchars($datos['id']),
                ':nombre'      => htmlspecialchars($datos['nombre']),
                ':categoria'   => htmlspecialchars($datos['categoria']),
                ':tamano'      => htmlspecialchars($datos['tamano']),
                ':precio'      => htmlspecialchars($datos['precio']),
                ':descripcion' => htmlspecialchars($datos['descripcion'])
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error del sistema: No se pudieron guardar los cambios del platillo.");
        }
    }

    // (D) DELETE: Eliminar permanentemente un platillo por su ID
    public function eliminarPlatillo($id) {
        try {
            $sql = "DELETE FROM menu WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new Exception("Error del sistema: No se pudo eliminar el platillo seleccionado.");
        }
    }
}
?>