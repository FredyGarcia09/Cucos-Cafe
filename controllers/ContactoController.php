<?php
// controllers/ContactoController.php
// Controlador principal para manejar el flujo de Vistas <-> Modelos

session_start();
require_once __DIR__ . '/../models/ContactoModel.php';

$controlador = new ContactoController();
$controlador->procesarPeticion();

class ContactoController {
    private $modelo;

    public function __construct() {
        $this->modelo = new ContactoModel();
    }

    public function mostrarMensajes() {
        return $this->modelo->obtenerMensajes();
    }

    public function procesarPeticion() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion'])) {
            
            // 1. LOGIN
            if ($_POST['accion'] == 'login') {
                $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
                $password = isset($_POST['password']) ? trim($_POST['password']) : '';

                if (empty($usuario) || empty($password)) {
                    echo "<script>alert('Error: Todos los campos son obligatorios.'); window.history.back();</script>";
                } elseif ($this->modelo->verificarLogin($usuario, $password)) {
                    $_SESSION['usuarioLogueado'] = $usuario;
                    header("Location: ../views/admin/admin.php");
                    exit();
                } else {
                    echo "<script>alert('Usuario o contraseña incorrectos'); window.history.back();</script>";
                }
            }

            // 2. CREATE: NUEVO MENSAJE (Con validación de campos)
            if ($_POST['accion'] == 'nuevo_mensaje') {
                $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $asunto = isset($_POST['asunto']) ? trim($_POST['asunto']) : '';
                $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
                $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';

                if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
                    echo "<script>alert('Error: Los campos marcados como obligatorios no pueden estar vacíos.'); window.history.back();</script>";
                } elseif (strlen($nombre) < 3) {
                    echo "<script>alert('Error: El nombre debe contener al menos 3 caracteres.'); window.history.back();</script>";
                } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
                    echo "<script>alert('Error: El nombre solo debe contener letras y espacios.'); window.history.back();</script>";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<script>alert('Error: El formato del correo electrónico no es válido.'); window.history.back();</script>";
                } elseif (!empty($telefono) && !preg_match("/^[0-9]{10}$/", $telefono)) {
                    echo "<script>alert('Error: El teléfono debe tener exactamente 10 dígitos numéricos.'); window.history.back();</script>";
                } elseif (strlen($mensaje) < 10) {
                    echo "<script>alert('Error: El mensaje debe ser más descriptivo (mínimo 10 caracteres).'); window.history.back();</script>";
                } else {
                    $_POST['nombre'] = $nombre;
                    $_POST['email'] = $email;
                    $_POST['asunto'] = $asunto;
                    $_POST['mensaje'] = $mensaje;
                    $_POST['telefono'] = $telefono;
                    $this->modelo->guardarMensaje($_POST);
                    echo "<script>alert('¡Mensaje enviado con éxito! Cuco te responderá pronto.'); window.location.href='../views/public/contacto.php';</script>";
                }
            }

            // 3. UPDATE: ACTUALIZAR ESTADO
            if ($_POST['accion'] == 'actualizar_estado') {
                $this->modelo->actualizarEstado($_POST['id']);
                header("Location: ../views/admin/admin.php");
                exit();
            }

            // 4. DELETE: ELIMINAR MENSAJE
            if ($_POST['accion'] == 'eliminar') {
                $this->modelo->eliminarMensaje($_POST['id']);
                header("Location: ../views/admin/admin.php");
                exit();
            }

            // CERRAR SESIÓN
            if ($_POST['accion'] == 'logout') {
                session_destroy();
                header("Location: ../views/admin/login.php");
                exit();
            }
        }
    }
}
?>