document.addEventListener("DOMContentLoaded", function() {
    const selectInterestButton = document.getElementById('selectInterestButton');
    const modal = document.getElementById('modal');
    const closeModalButton = document.getElementById('closeModalButton');
    const selectAllButton = document.getElementById('selectAllButton');
    const deselectAllButton = document.getElementById('deselectAllButton');
    const form = document.querySelector('form'); 

    // Array para almacenar el estado de cada círculo
    let stateString = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
    const pocillos_hab = document.querySelector('input[name="test[pocillos_hab]"]');
    pocillos_hab.value = stateString;

    // Mostrar el modal
    selectInterestButton.addEventListener('click', function() {
        modal.style.display = 'flex';
    });

    // Cerrar el modal y generar el string
    closeModalButton.addEventListener('click', function() {
        modal.style.display = 'none';

        // Reiniciar el estado a una cadena vacía para no concatenar
        stateString = '';

        // Seleccionar todos los círculos del modal
        document.querySelectorAll('.circle').forEach(circle => {
            // Obtener el color de fondo calculado
            let currentColor = window.getComputedStyle(circle).backgroundColor;

            // Si el color es verde (rgba(51, 185, 11, 0.56)) es un '1', si es rojo (rgba(255, 0, 0, 0.46)) es un '0'
            if (currentColor === 'rgba(51, 185, 11, 0.56)') { // verde con opacidad
                stateString += '1';
            } else {
                stateString += '0';
            }
        });

        pocillos_hab.value = stateString;
    });

    // Evento click para cambiar color de los círculos
    document.querySelectorAll('.circle').forEach(circle => {
        circle.addEventListener('click', function() {
            // Obtener el color de fondo calculado
            let currentColor = window.getComputedStyle(this).backgroundColor;

            // Comparar el color y cambiar el fondo
            if (currentColor === 'rgba(255, 0, 0, 0.46)') {  // rojo con opacidad
                this.style.backgroundColor = 'rgba(51, 185, 11, 0.56)'; // verde con opacidad
            } else {
                this.style.backgroundColor = 'rgba(255, 0, 0, 0.46)'; // rojo con opacidad
            }
        });
    });

    // Seleccionar todos los pocillos (cambiar todos los círculos a verde)
    selectAllButton.addEventListener('click', function() {
        document.querySelectorAll('.circle').forEach(circle => {
            circle.style.backgroundColor = 'rgba(51, 185, 11, 0.56)'; // verde con opacidad
        });
    });

    // Deseleccionar todos los pocillos (cambiar todos los círculos a rojo)
    deselectAllButton.addEventListener('click', function() {
        document.querySelectorAll('.circle').forEach(circle => {
            circle.style.backgroundColor = 'rgba(255, 0, 0, 0.46)'; // rojo con opacidad
        });
    });

    // Evento submit para prevenir el envío si no se selecciona al menos un pocillo
    form.addEventListener('submit', function(event) {
        // Llamar a la función para verificar la selección
        if (window.checkPocillosSelection(stateString)) {
            event.preventDefault(); // Prevenir el envío del formulario
        }
    });
});
