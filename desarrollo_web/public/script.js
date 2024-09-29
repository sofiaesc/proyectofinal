document.addEventListener('DOMContentLoaded', () => {

    const showPopupBtn = document.querySelector(".login-btn");
    const formPopup = document.querySelector(".form-popup");
    const menuBtn = document.querySelector(".menu-btn");
    const navLinks = document.querySelector(".nav-links");

    // Toggle the mobile menu
    menuBtn.addEventListener("click", () => {
        navLinks.classList.toggle("show-menu");
    });

    // Close the menu when a link is clicked only on small screens
    navLinks.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", () => {
            if (window.innerWidth < 900) {
                navLinks.classList.remove("show-menu");
            }
        });
    });

    if (formPopup) {
        const hidePopupBtn = formPopup.querySelector(".close-btn");
        const signupLoginLink = formPopup.querySelectorAll(".bottom-link a");

        // Mostrar popup
        showPopupBtn.addEventListener("click", () => {
            document.body.classList.add("show-popup");
            formPopup.classList.remove("show-signup"); // Mostrar login por defecto
        });

        // Ocultar popup
        hidePopupBtn.addEventListener("click", () => {
            document.body.classList.remove("show-popup");
        });

        // Alternar entre signup y login
        signupLoginLink.forEach(link => {
            link.addEventListener("click", (e) => {
                e.preventDefault();
                formPopup.classList.toggle("show-signup", link.id === 'signup-link');
            });
        });
    } else {
        console.warn(".form-popup element not found, skipping popup logic");
    }

    // CARRUSEL:
    const items = document.querySelectorAll('.carousel-item'); // Get all carousel items
    const caption = document.querySelector('.carousel-caption'); // Get the caption element
    let currentIndex = 0; // Start at the first item
    const totalItems = items.length;

    // Function to show an item based on index
    function showItem(index) {
        items[currentIndex].classList.remove('active'); // Remove 'active' class from current item
        currentIndex = (index + totalItems) % totalItems; // Update index and ensure it loops
        items[currentIndex].classList.add('active'); // Add 'active' class to the new current item
        updateCarousel(); // Adjust carousel position
        updateCaption(); // Update the caption text
    }

    // Function to update the carousel position (sliding effect)
    function updateCarousel() {
        const offset = -currentIndex * 100; // Calculate the offset for sliding
        document.querySelector('.carousel-inner').style.transform = `translateX(${offset}%)`; // Slide the carousel
    }

    // Function to update the caption text
    function updateCaption() {
        const text = items[currentIndex].getAttribute('data-text'); // Get the data-text attribute from the current item
        caption.textContent = text; // Set the caption text to the data-text
    }

    // Show the next item
    function showNextItem() {
        showItem(currentIndex + 1); // Show the next item by increasing index
    }

    // Show the previous item
    function showPrevItem() {
        showItem(currentIndex - 1); // Show the previous item by decreasing index
    }

    // Add keyboard support for navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') {
            showNextItem();
        } else if (e.key === 'ArrowLeft') {
            showPrevItem();
        }
    });

    // Add event listeners to the buttons
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', showNextItem); // Next button click event
    }
    if (prevBtn) {
        prevBtn.addEventListener('click', showPrevItem); // Previous button click event
    }

    // Initialize the carousel by showing the first item and setting its caption
    showItem(currentIndex); // Show the initial item when the page loads
});
