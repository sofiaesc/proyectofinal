document.addEventListener("DOMContentLoaded", function() {
    const warningMessage = document.createElement('div');
    warningMessage.textContent = "Debe seleccionar al menos un pocillo";
    warningMessage.style.color = "red";
    warningMessage.style.display = "none"; // Ocultar por defecto
    document.querySelector('.carga-container').appendChild(warningMessage);

    // Función para verificar la selección de pocillos
    function checkPocillosSelection(stateString) {
        // Mostrar el mensaje de advertencia si no se seleccionó ningún pocillo
        console.log(stateString);
        if (stateString === "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000") {
            warningMessage.style.display = "block"; // Mostrar el mensaje
            return true; // Indicar que se debe prevenir el envío
        } else {
            warningMessage.style.display = "none"; // Ocultar el mensaje
            return false; // Indicar que se puede continuar
        }
    }
    // Exportar la funcion
    window.checkPocillosSelection = checkPocillosSelection;
});
