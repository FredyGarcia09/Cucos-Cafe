// DATOS INICIALES
const XML_INICIAL = `<?xml version="1.0" encoding="UTF-8"?>
<contactos>
    <mensaje>
        <id>1</id>
        <nombre>Sistema</nombre>
        <email>admin@cucos.com</email>
        <asunto>Bienvenida</asunto>
        <texto>Sistema inicializado correctamente.</texto>
    </mensaje>
</contactos>`;

const ADMIN_XML = `<?xml version="1.0" encoding="UTF-8"?>
<admins>
    <admin><usuario>cuco</usuario><password>1234</password></admin>
</admins>`;

// LÓGICA COMÚN: MANEJO DE "BASE DE DATOS" 
function obtenerBD() {
    let db = localStorage.getItem("cucos_db_xml");
    if (!db) {
        // Si es la primera vez, guardamos el XML inicial
        localStorage.setItem("cucos_db_xml", XML_INICIAL);
        return XML_INICIAL;
    }
    return db;
}

function guardarBD(xmlDoc) {
    let serializer = new XMLSerializer();
    let xmlString = serializer.serializeToString(xmlDoc);
    localStorage.setItem("cucos_db_xml", xmlString);
}

// LÓGICA DE LA PÁGINA DE CONTACTO
// Esta función se activará cuando envíes el formulario
function procesarFormularioContacto(event) {
    event.preventDefault(); // DETIENE EL ERROR 405

    // Obtener datos del HTML
    const nombre = document.getElementById("nombre").value;
    const email = document.getElementById("email").value;
    const asunto = document.getElementById("asunto").value;
    const mensaje = document.getElementById("mensaje").value;

    // Leer la "Base de Datos" actual
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(obtenerBD(), "text/xml");

    // Crear el nuevo nodo XML
    const nuevoNodo = xmlDoc.createElement("mensaje");
    const id = Date.now(); // Usamos la fecha como ID

    crearElemento(xmlDoc, nuevoNodo, "id", id);
    crearElemento(xmlDoc, nuevoNodo, "nombre", nombre);
    crearElemento(xmlDoc, nuevoNodo, "email", email);
    crearElemento(xmlDoc, nuevoNodo, "asunto", asunto);
    crearElemento(xmlDoc, nuevoNodo, "texto", mensaje);

    // Insertarlo y Guardar
    xmlDoc.getElementsByTagName("contactos")[0].appendChild(nuevoNodo);
    guardarBD(xmlDoc);

    // Feedback al usuario
    alert("¡Mensaje enviado! Cuco lo ha recibido 🐶");
    document.querySelector("form").reset(); // Limpia formulario
}

// LÓGICA DEL ADMIN (Login y Dashboard)
function validarLogin() {
    const u = document.getElementById("usuario").value;
    const p = document.getElementById("password").value;
    
    // Validación simple contra el XML string
    if (u === "cuco" && p === "1234") {
        sessionStorage.setItem("usuarioLogueado", u);
        window.location.href = "admin.html";
    } else {
        alert("Error. Intenta: cuco / 1234");
    }
}

function cargarAdminPanel() {
    if (!sessionStorage.getItem("usuarioLogueado")) {
        window.location.href = "login.html";
        return;
    }
    document.getElementById("admin-name").textContent = sessionStorage.getItem("usuarioLogueado");
    
    renderizarTabla();
}

function renderizarTabla() {
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(obtenerBD(), "text/xml");
    const mensajes = xmlDoc.getElementsByTagName("mensaje");
    let html = "";

    if (mensajes.length === 0) {
        html = "<tr><td colspan='5' style='text-align:center; padding:20px;'>No hay mensajes</td></tr>";
    } else {
        // Mostrar del más reciente al más antiguo
        for (let i = mensajes.length - 1; i >= 0; i--) {
            const m = mensajes[i];
            html += `
                <tr class="hover:bg-gray-50 border-b">
                    <td class="p-3">#${obtenerValor(m, "id").toString().slice(-4)}</td>
                    <td class="p-3 font-bold">${obtenerValor(m, "nombre")}</td>
                    <td class="p-3 text-sm text-gray-600">${obtenerValor(m, "email")}</td>
                    <td class="p-3"><span class="badge">${obtenerValor(m, "asunto")}</span></td>
                    <td class="p-3 text-sm">${obtenerValor(m, "texto")}</td>
                    <td class="p-3">
                        <button onclick="eliminarMensaje(${i})" class="btn-delete" title="Borrar">🗑️</button>
                    </td>
                </tr>
            `;
        }
    }
    document.getElementById("cuerpo-tabla").innerHTML = html;
}

function eliminarMensaje(index) {
    if(!confirm("¿Borrar mensaje?")) return;

    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(obtenerBD(), "text/xml");
    const mensajes = xmlDoc.getElementsByTagName("mensaje");

    if (mensajes[index]) {
        mensajes[index].parentNode.removeChild(mensajes[index]);
        guardarBD(xmlDoc); // Guardar cambios en LocalStorage
        renderizarTabla(); // Refrescar pantalla
    }
}

// Función auxiliar para crear nodos XML
function crearElemento(doc, padre, tag, valor) {
    let el = doc.createElement(tag);
    el.textContent = valor;
    padre.appendChild(el);
}

function obtenerValor(nodo, tag) {
    return nodo.getElementsByTagName(tag)[0]?.textContent || "";
}

// Función de Respaldo Físico (Descargar)
function descargarXML() {
    const blob = new Blob([obtenerBD()], {type: "text/xml"});
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "Backup_Contactos.xml";
    link.click();
}

function cerrarSesion() {
    sessionStorage.removeItem("usuarioLogueado");
    window.location.href = "login.html";
}