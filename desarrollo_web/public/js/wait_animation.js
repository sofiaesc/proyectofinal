document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector('form');  // Seleccionar el formulario
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Al enviar el formulario
    form.addEventListener('submit', function(event) {
        const pocillos_hab = document.querySelector('input[name="test[pocillos_hab]"]');
        // Llamar a la función para verificar la selección de pocillos
        const shouldPreventSubmit = window.checkPocillosSelection(pocillos_hab.value); // Obtén el valor del estado

        if (shouldPreventSubmit) {
            event.preventDefault(); // Prevenir el envío del formulario si no se seleccionó ningún pocillo
        } else {
            // Mostrar la animación de carga
            loadingOverlay.style.display = 'flex';

            // Deshabilitar la interacción con la pantalla
            form.querySelectorAll('button').forEach(function(button) {
                button.disabled = true;
            });
        }
    });
});