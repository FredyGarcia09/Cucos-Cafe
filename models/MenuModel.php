<?php
// Modelo encargado de gestionar las operaciones CRUD en el archivo menu.xml
class MenuModel {
    // Definición de la ruta física del archivo XML usando la constante de directorio actual
    private $rutaXML = __DIR__ . '/../data/menu.xml';

    // (R) READ: Leer todos los platillos del menú
    public function obtenerPlatillos() {
        try {
            // Verificamos si el archivo existe antes de abrirlo
            if (!file_exists($this->rutaXML)) {
                throw new Exception("Error temporal en el sistema de menú: Archivo no encontrado.");
            }
            
            // simplexml_load_file carga el XML como un objeto manejable
            $xml = simplexml_load_file($this->rutaXML);
            
            if ($xml === false) {
                throw new Exception("Error temporal en el sistema de menú: Archivo corrupto o sin permisos.");
            }
            
            return $xml;
            
        } catch (Exception $e) {
            // Retorna el mensaje amigable de error si algo falla
            return $e->getMessage(); 
        }
    }

    // (C) CREATE: Agregar un nuevo platillo al XML
    public function agregarPlatillo($datos) {
        $xml = simplexml_load_file($this->rutaXML);
        
        // Creamos un nuevo "nodo" hijo llamado platillo
        $nuevoPlatillo = $xml->addChild('platillo');
        $nuevoPlatillo->addChild('id', uniqid()); // Genera un ID único para el producto
        $nuevoPlatillo->addChild('nombre', htmlspecialchars($datos['nombre']));
        $nuevoPlatillo->addChild('tamano', htmlspecialchars($datos['tamano']));
        $nuevoPlatillo->addChild('precio', htmlspecialchars($datos['precio']));
        $nuevoPlatillo->addChild('descripcion', htmlspecialchars($datos['descripcion']));
        $nuevoPlatillo->addChild('categoria', htmlspecialchars($datos['categoria']));

        // Guardamos los cambios sobreescribiendo el archivo XML
        $xml->asXML($this->rutaXML);
    }

    // (U) UPDATE: Modificar los valores de un platillo existente
    public function actualizarPlatillo($datos) {
        $xml = simplexml_load_file($this->rutaXML);
        
        // Buscamos el nodo correspondiente por medio de su ID único
        foreach ($xml->platillo as $platillo) {
            if ((string)$platillo->id === $datos['id']) {
                // Modificamos las propiedades con los nuevos valores sanitizados
                $platillo->nombre = htmlspecialchars($datos['nombre']);
                $platillo->tamano = htmlspecialchars($datos['tamano']);
                $platillo->precio = htmlspecialchars($datos['precio']);
                $platillo->descripcion = htmlspecialchars($datos['descripcion']);
                $platillo->categoria = htmlspecialchars($datos['categoria']);
                break;
            }
        }
        // Consolidamos los cambios en el archivo físico
        $xml->asXML($this->rutaXML);
    }

    // (D) DELETE: Eliminar un platillo por su ID
    public function eliminarPlatillo($id) {
        $xml = simplexml_load_file($this->rutaXML);
        $index = 0;
        
        // Buscamos el platillo que coincida con el ID para removerlo del arreglo
        foreach ($xml->platillo as $platillo) {
            if ((string)$platillo->id === $id) {
                unset($xml->platillo[$index]); // Elimina el nodo del árbol XML
                break;
            }
            $index++;
        }
        $xml->asXML($this->rutaXML);
    }
}
?>