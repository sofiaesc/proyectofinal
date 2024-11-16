document.addEventListener('DOMContentLoaded', () => {

    const menuBtn = document.querySelector(".menu-btn");
    const navLinks = document.querySelector(".nav-links");
    const disclaimerButton = document.getElementById('disclaimer');
    const modalDisclaimer = document.getElementById('modal-disclaimer');
    const closeModalDisclaimer = document.getElementById('closeModalDisclaimer');

    // -------------------- MENU -------------------- //
    // Toggle del menu
    menuBtn.addEventListener("click", () => {
        navLinks.classList.toggle("show-menu");
    });

    // Cerrar menu cuando se aprieta un link
    navLinks.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", () => {
            if (window.innerWidth < 900) {
                navLinks.classList.remove("show-menu");
            }
        });
    });

    // -------------------- CARRUSEL -------------------- //
    const items = document.querySelectorAll('.carousel-item'); 
    const caption = document.querySelector('.carousel-caption'); 
    let currentIndex = 0; 
    const totalItems = items.length;

    // Mostrar imagen por indice
    function showItem(index) {
        items[currentIndex].classList.remove('active'); 
        currentIndex = (index + totalItems) % totalItems; 
        items[currentIndex].classList.add('active'); 
        updateCarousel(); 
        updateCaption(); 
    }

    // Sliding
    function updateCarousel() {
        const offset = -currentIndex * 100; 
        document.querySelector('.carousel-inner').style.transform = `translateX(${offset}%)`; 
    }

    // Actualizar texto que acompaña la imagen
    function updateCaption() {
        const text = items[currentIndex].getAttribute('data-text'); 
        caption.textContent = text; 
    }

    // Siguiente imagen
    function showNextItem() {
        showItem(currentIndex + 1); 
    }

    // Imagen previa
    function showPrevItem() {
        showItem(currentIndex - 1); 
    }

    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', showNextItem); 
    }
    if (prevBtn) {
        prevBtn.addEventListener('click', showPrevItem); 
    }

    showItem(currentIndex); 


    // -------------------- INSTRUCCIONES -------------------- //
    if (disclaimerButton) {
        // Mostrar el modal
        disclaimerButton.addEventListener('click', function() {
            modalDisclaimer.style.display = 'flex';
        });

        // Cerrar el modal
        closeModalDisclaimer.addEventListener('click', function() {
            modalDisclaimer.style.display = 'none';
        });

        // Cerrar el modal si se hace clic fuera de la caja del modal
        window.addEventListener('click', function(event) {
            if (event.target === modalDisclaimer) {
                modalDisclaimer.style.display = 'none';
            }
        });
    } else {
        console.error('No se encontraron los elementos del modal o el botón.');
    }
    

    /* Editar nombre */
    // Obtener el modal y el botón de editar
    const modal = document.getElementById("editNameModal");
    const editButton = document.getElementById("editButton");
    const closeModalBtn = document.getElementById("closeModalBtn");

    // Cuando el usuario hace clic en el botón de editar, abrir el modal
    editButton.onclick = function() {
        modal.style.display = "block";
    }

    // Cuando el usuario hace clic en el botón de cerrar (×), cerrar el modal
    closeModalBtn.onclick = function() {
        modal.style.display = "none";
    }

    // Cuando el usuario hace clic fuera del modal, también lo cierra
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    } 
});
