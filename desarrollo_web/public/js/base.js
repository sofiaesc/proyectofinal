document.addEventListener('DOMContentLoaded', () => {
    // -------------------- MENU -------------------- //
    const menuBtn = document.querySelector(".menu-btn");
    const navLinks = document.querySelector(".nav-links");

    if (menuBtn && navLinks) {
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
    }

    // -------------------- MENU USUARIO ------------------- //
    const userMenuToggle = document.getElementById("user-menu-toggle");
    const userDropdownMenu = document.getElementById("user-dropdown-menu");

    if (userMenuToggle && userDropdownMenu) {
        userMenuToggle.addEventListener("click", (e) => {
            e.stopPropagation(); // Evita que el clic se propague al body
            userDropdownMenu.classList.toggle("show"); // Muestra u oculta el menú
        });

        // Cierra el menú si se hace clic fuera de él
        document.addEventListener("click", () => {
            userDropdownMenu.classList.remove("show");
        });
    }

    // -------------------- CARRUSEL -------------------- //
    const items = document.querySelectorAll('.carousel-item');
    const caption = document.querySelector('.carousel-caption');
    let currentIndex = 0;
    const totalItems = items.length;

    // Mostrar imagen por índice
    function showItem(index) {
        // Remover la clase 'active' del item actual
        items[currentIndex].classList.remove('active');
        // Actualizar el índice
        currentIndex = (index + totalItems) % totalItems;
        // Añadir la clase 'active' al nuevo item
        items[currentIndex].classList.add('active');

        // Cargar la imagen solo si es el item activo
        loadImage(currentIndex);

        updateCarousel();
        updateCaption();
    }

    // Cargar la imagen solo si está activa
    function loadImage(index) {
        const activeItem = items[index];
        const img = activeItem.querySelector('img');

        // Solo cargar la imagen si no ha sido cargada previamente
        if (img && !img.src) {
            img.src = img.getAttribute('data-src'); // Establecer el src de la imagen
        }
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

    // Inicializar el primer item
    showItem(currentIndex);

    // -------------------- INSTRUCCIONES -------------------- //
    const disclaimerButton = document.getElementById('disclaimer');
    const modalDisclaimer = document.getElementById('modal-disclaimer');
    const closeModalDisclaimer = document.getElementById('closeModalDisclaimer');

    if (disclaimerButton && modalDisclaimer && closeModalDisclaimer) {
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
});
