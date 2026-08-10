
function buscarUsuario() {

    let textoDeBusqueda = document.getElementById("buscarUsuario").value;
    
    console.log(textoDeBusqueda);
const datos = {
    textoBusqueda: textoDeBusqueda
};

fetch("buscador.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify(datos)
})
.then(response => response.json())
.then(data => {
    console.log("Respuesta del servidor:", data);
})
.catch(error => {
    console.error("Error:", error);
});
}