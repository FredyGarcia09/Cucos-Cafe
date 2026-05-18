<?php
// models/ContactoModel.php
// Modelo encargado de gestionar el CRUD en el archivo XML de contactos

class ContactoModel {
    private $archivoContactos;
    private $archivoAdmin;

    public function __construct() {
        // Rutas físicas a los archivos XML
        $this->archivoContactos = __DIR__ . '/../data/contactos.xml';
        $this->archivoAdmin = __DIR__ . '/../data/admin.xml';
    }

    // VERIFICAR LOGIN
    public function verificarLogin($usuario, $password) {
        if (!file_exists($this->archivoAdmin)) return false;
        
        $xml = simplexml_load_file($this->archivoAdmin);
        foreach ($xml->admin as $admin) {
            if ((string)$admin->usuario === $usuario && (string)$admin->password === $password) {
                return true;
            }
        }
        return false;
    }

    // (R) LEER MENSAJES
    public function obtenerMensajes() {
        if (!file_exists($this->archivoContactos)) return [];
        
        $xml = simplexml_load_file($this->archivoContactos);
        $mensajes = [];
        
        foreach ($xml->mensaje as $m) {
            $msg = new stdClass();
            $msg->id = (string)$m->id;
            $msg->nombre = (string)$m->nombre;
            $msg->email = (string)$m->email;
            $msg->asunto = (string)$m->asunto;
            $msg->texto = (string)$m->texto;
            // Si el XML viejo no tiene la etiqueta 'estado', asumimos que está Pendiente
            $msg->estado = isset($m->estado) ? (string)$m->estado : 'Pendiente';
            $mensajes[] = $msg;
        }
        
        return array_reverse($mensajes);
    }

    // (C) CREAR NUEVO MENSAJE
    public function guardarMensaje($datos) {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        
        if (file_exists($this->archivoContactos)) {
            $doc->load($this->archivoContactos);
            $root = $doc->documentElement;
        } else {
            $root = $doc->createElement('contactos');
            $doc->appendChild($root);
        }

        $nuevoMensaje = $doc->createElement('mensaje');
        
        $nuevoMensaje->appendChild($doc->createElement('id', time())); 
        $nuevoMensaje->appendChild($doc->createElement('nombre', htmlspecialchars($datos['nombre'])));
        $nuevoMensaje->appendChild($doc->createElement('email', htmlspecialchars($datos['email'])));
        $nuevoMensaje->appendChild($doc->createElement('asunto', htmlspecialchars($datos['asunto'])));
        $nuevoMensaje->appendChild($doc->createElement('texto', htmlspecialchars($datos['mensaje'])));
        
        // Agregamos el estado inicial por defecto
        $nuevoMensaje->appendChild($doc->createElement('estado', 'Pendiente'));

        $root->appendChild($nuevoMensaje);
        $doc->save($this->archivoContactos);
    }

    // (U) ACTUALIZAR ESTADO
    public function actualizarEstado($id) {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->load($this->archivoContactos);

        $mensajes = $doc->getElementsByTagName('mensaje');
        foreach ($mensajes as $m) {
            $idNodo = $m->getElementsByTagName('id')->item(0)->nodeValue;
            if ($idNodo == $id) {
                // Buscamos si ya tiene la etiqueta estado
                $estadoNodo = $m->getElementsByTagName('estado')->item(0);
                if ($estadoNodo) {
                    // Alternamos entre Atendido y Pendiente
                    $nuevoEstado = ($estadoNodo->nodeValue == 'Pendiente') ? 'Atendido' : 'Pendiente';
                    $estadoNodo->nodeValue = $nuevoEstado;
                } else {
                    // Si era un mensaje viejo sin etiqueta, se la creamos
                    $m->appendChild($doc->createElement('estado', 'Atendido'));
                }
                break; // Terminamos la búsqueda al actualizar
            }
        }
        $doc->save($this->archivoContactos);
    }

    // (D) ELIMINAR MENSAJE
    public function eliminarMensaje($id) {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->load($this->archivoContactos);

        $mensajes = $doc->getElementsByTagName('mensaje');
        foreach ($mensajes as $m) {
            $idNodo = $m->getElementsByTagName('id')->item(0)->nodeValue;
            if ($idNodo == $id) {
                $m->parentNode->removeChild($m);
                break;
            }
        }
        $doc->save($this->archivoContactos);
    }
}
?>