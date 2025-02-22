        const fileInput = document.querySelector('input[type="file"]');
        const canvas = document.getElementById('imageCanvas');
        const ctx = canvas.getContext('2d');
        let img, imgScale, imgX, imgY;
        let draggingPoint = null;


        // Obtener los campos ocultos del formulario
        const inputX1 = document.querySelector('input[name="test[x1]"]');
        const inputY1 = document.querySelector('input[name="test[y1]"]');
        const inputX2 = document.querySelector('input[name="test[x2]"]');
        const inputY2 = document.querySelector('input[name="test[y2]"]');

        // Variables para las esquinas del rectángulo (x, y)
        let rectPoints = [];

        const pointRadius = 10;  // Tamaño aumentado de los puntos grises

        // Variables para las proporciones relativas de los puntos
        let pointRatios = []; 

        function adjustCanvasSize() {
            const canvasStyleWidth = canvas.clientWidth;
            const canvasStyleHeight = canvas.clientHeight;
            canvas.width = canvasStyleWidth;
            canvas.height = canvasStyleHeight;
        
            if (img) {
                // Recalcular escala y posición de la imagen
                imgScale = Math.min(canvas.width / img.width, canvas.height / img.height);
                imgX = (canvas.width - img.width * imgScale) / 2;
                imgY = (canvas.height - img.height * imgScale) / 2;
            
                // Si los puntos ya están definidos, recalcular sus posiciones según las proporciones
                if (pointRatios.length === 4) {
                    rectPoints = pointRatios.map(({ xRatio, yRatio }) => ({
                        x: imgX + xRatio * (img.width * imgScale),
                        y: imgY + yRatio * (img.height * imgScale)
                    }));
                } else {
                    // Inicializar los puntos en proporción 1/3 y 2/3
                    const rectX1 = imgX + (img.width * imgScale) * (1 / 3);
                    const rectX2 = imgX + (img.width * imgScale) * (2 / 3);
                    const rectY1 = imgY + (img.height * imgScale) * (1 / 3);
                    const rectY2 = imgY + (img.height * imgScale) * (2 / 3);
                
                    rectPoints = [
                        { x: rectX1, y: rectY1 },
                        { x: rectX2, y: rectY1 },
                        { x: rectX2, y: rectY2 },
                        { x: rectX1, y: rectY2 }
                    ];
                }
            
                // Dibujar el rectángulo interactivo
                updateHiddenInputs();
                drawInteractiveRectangle();
            }
        }

        // Guardar las proporciones relativas antes del resize
        function savePointRatios() {
            pointRatios = rectPoints.map(point => ({
                xRatio: (point.x - imgX) / (img.width * imgScale),
                yRatio: (point.y - imgY) / (img.height * imgScale)
            }));
        }

        // Detectar cambios de tamaño en la ventana
        let lastWidth = window.innerWidth;
        let lastHeight = window.innerHeight;

        window.addEventListener('resize', function () {
            const currentWidth = window.innerWidth;
            const currentHeight = window.innerHeight;

            if (currentWidth !== lastWidth || currentHeight !== lastHeight) {
                savePointRatios(); // Guardar las proporciones antes de ajustar el tamaño
                adjustCanvasSize();
                lastWidth = currentWidth;
                lastHeight = currentHeight;
            }
        });

        adjustCanvasSize();

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                img = new Image();
                img.onload = function() {
                    adjustCanvasSize();
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    const canvasWidth = canvas.width;
                    const canvasHeight = canvas.height;

                    imgScale = Math.min(canvasWidth / img.width, canvasHeight / img.height);
                    imgX = (canvasWidth - img.width * imgScale) / 2;
                    imgY = (canvasHeight - img.height * imgScale) / 2;

                    // Dibujar la imagen
                    ctx.drawImage(img, imgX, imgY, img.width * imgScale, img.height * imgScale);

                    // Inicializar los puntos del rectángulo en proporción 1/3 y 2/3 de la imagen
                    const rectX1 = imgX + (img.width * imgScale) * (1 / 3);  // 1/3 del ancho de la imagen
                    const rectX2 = imgX + (img.width * imgScale) * (2 / 3);  // 2/3 del ancho de la imagen
                    const rectY1 = imgY + (img.height * imgScale) * (1 / 3); // 1/3 de la altura de la imagen
                    const rectY2 = imgY + (img.height * imgScale) * (2 / 3); // 2/3 de la altura de la imagen
                    
                    rectPoints = [
                        { x: rectX1, y: rectY1 },  // Esquina superior izquierda
                        { x: rectX2, y: rectY1 },  // Esquina superior derecha
                        { x: rectX2, y: rectY2 },  // Esquina inferior derecha
                        { x: rectX1, y: rectY2 }   // Esquina inferior izquierda
                    ];

                    // Dibujar el rectángulo interactivo
                    updateHiddenInputs();
                    drawInteractiveRectangle();
                };
                img.src = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });

        // Función para dibujar el rectángulo con puntos interactivos y área de selección
        function drawInteractiveRectangle() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Volver a dibujar la imagen
            ctx.drawImage(img, imgX, imgY, img.width * imgScale, img.height * imgScale);
            
            // Dibujar el área de selección con transparencia
            ctx.fillStyle = 'rgba(157,180,197, 0.45)'; 
            ctx.beginPath();
            ctx.moveTo(rectPoints[0].x, rectPoints[0].y);
            ctx.lineTo(rectPoints[1].x, rectPoints[1].y);
            ctx.lineTo(rectPoints[2].x, rectPoints[2].y);
            ctx.lineTo(rectPoints[3].x, rectPoints[3].y);
            ctx.closePath();
            ctx.fill();

            // Dibujar líneas negras del rectángulo
            ctx.beginPath();
            ctx.moveTo(rectPoints[0].x, rectPoints[0].y);
            for (let i = 1; i < rectPoints.length; i++) {
                ctx.lineTo(rectPoints[i].x, rectPoints[i].y);
            }
            ctx.closePath();
            ctx.strokeStyle = "rgba(89,112,133,255)";
            ctx.lineWidth = 2;
            ctx.stroke();

            // Dibujar puntos grises en las esquinas
            for (const point of rectPoints) {
                ctx.beginPath();
                ctx.arc(point.x, point.y, pointRadius, 0, Math.PI * 2);
                ctx.fillStyle = "rgba(55, 67, 82, 255)";
                ctx.fill();
                ctx.closePath();
            }
        }




        // Evento para detectar si se está arrastrando una esquina (táctil y ratón)
        canvas.addEventListener('mousedown', startDrag);
        canvas.addEventListener('touchstart', startDrag, { passive: false });

        canvas.addEventListener('mousemove', drag);
        canvas.addEventListener('touchmove', drag, { passive: false });

        canvas.addEventListener('mouseup', endDrag);
        canvas.addEventListener('touchend', endDrag);

        // Función para detectar el inicio del arrastre
        function startDrag(event) {
            const { x, y } = getMouseOrTouchPos(event);
        
            // Detectar si el clic o toque está sobre algún punto del rectángulo
            for (let i = 0; i < rectPoints.length; i++) {
                const point = rectPoints[i];
                const distance = Math.sqrt((x - point.x) ** 2 + (y - point.y) ** 2);
                if (distance < pointRadius) {
                    draggingPoint = i;
                    event.preventDefault(); // Prevenir comportamientos por defecto en táctil
                    break;
                }
            }
        }

        // Función para mover el punto arrastrado
        function drag(event) {
            if (draggingPoint !== null) {
                const { x, y } = getMouseOrTouchPos(event);
            
                // Verificar si el nuevo punto está dentro de los límites de la imagen
                const minX = imgX; // Límite izquierdo de la imagen
                const maxX = imgX + img.width * imgScale; // Límite derecho de la imagen
                const minY = imgY; // Límite superior de la imagen
                const maxY = imgY + img.height * imgScale; // Límite inferior de la imagen
            
                // Actualizar las coordenadas del punto en función de cuál esquina se está moviendo
                let newX = x;
                let newY = y;
            
                // Asegurarse de que el nuevo punto no salga de los límites de la imagen
                if (newX < minX) newX = minX;
                if (newX > maxX) newX = maxX;
                if (newY < minY) newY = minY;
                if (newY > maxY) newY = maxY;
            
                switch (draggingPoint) {
                    case 0: // Esquina superior izquierda
                        rectPoints[draggingPoint].x = newX;
                        rectPoints[draggingPoint].y = newY;
                        rectPoints[1].y = newY; // Esquina superior derecha
                        rectPoints[3].x = newX; // Esquina inferior izquierda
                        break;
                    case 1: // Esquina superior derecha
                        rectPoints[draggingPoint].x = newX;
                        rectPoints[draggingPoint].y = newY;
                        rectPoints[0].y = newY; // Esquina superior izquierda
                        rectPoints[2].x = newX; // Esquina inferior derecha
                        break;
                    case 2: // Esquina inferior derecha
                        rectPoints[draggingPoint].x = newX;
                        rectPoints[draggingPoint].y = newY;
                        rectPoints[1].x = newX; // Esquina superior derecha
                        rectPoints[3].y = newY; // Esquina inferior izquierda
                        break;
                    case 3: // Esquina inferior izquierda
                        rectPoints[draggingPoint].x = newX;
                        rectPoints[draggingPoint].y = newY;
                        rectPoints[0].x = newX; // Esquina superior izquierda
                        rectPoints[2].y = newY; // Esquina inferior derecha
                        break;
                }
                updateHiddenInputs();
                drawInteractiveRectangle();
            }
        }

        // Función para finalizar el arrastre
        function endDrag() {
            draggingPoint = null;
        }

        // Función para obtener la posición del ratón o toque
        function getMouseOrTouchPos(event) {
            const rect = canvas.getBoundingClientRect();
            const x = (event.touches ? event.touches[0].clientX : event.clientX) - rect.left;
            const y = (event.touches ? event.touches[0].clientY : event.clientY) - rect.top;
            return { x, y };
        }

        function updateHiddenInputs() {
            inputX1.value = (rectPoints[0].x - imgX) / imgScale;
            inputY1.value = (rectPoints[0].y - imgY) / imgScale;
            inputX2.value = (rectPoints[2].x - imgX) / imgScale;
            inputY2.value = (rectPoints[2].y - imgY) / imgScale;
        }

        
        