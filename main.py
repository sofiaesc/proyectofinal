from flask import Flask, request, jsonify
from utils.utils_crop import *
from utils.utils_validation import *
from utils.utils_graphics import *
from utils.utils_preprocessing import *
from circle_detection import *
from analyze_wells import *
import io
import os
from PIL import Image

app = Flask(__name__)

@app.route('/process', methods=['POST'])
def process_data():
    try:
        # Recibir la imagen y parámetros
        image_file = request.files['image']  # Obtener la imagen
        top_left_x = int(request.form['top_left_x'])
        top_left_y = int(request.form['top_left_y'])
        bottom_right_x = int(request.form['bottom_right_x'])
        bottom_right_y = int(request.form['bottom_right_y'])

        # Verificar el tipo de contenido de la imagen
        if image_file.content_type not in ['image/jpeg', 'image/png']:
            return jsonify({"error": "Unsupported file type. Please upload a JPEG or PNG image."}), 200

        # Abrir la imagen
        image = Image.open(image_file.stream)

        # Verificar si la imagen es nula
        if image is None:
            print("Received image is null.")
            return jsonify({"error": "Received image is null."}), 200
        
        # Convertir la imagen a un array de NumPy
        image_array = np.array(image)

        # Guardar la imagen en un directorio específico
        save_directory = 'uploads'
        os.makedirs(save_directory, exist_ok=True)  # Crear el directorio si no existe
        image_path = os.path.join(save_directory, f"{os.path.splitext(image_file.filename)[0]}_processed.png")
        image.save(image_path)  # Guardar la imagen

        images = list(crop_elisa_plate(image_array, top_left_x, top_left_y, bottom_right_x, bottom_right_y))

        # Procesar la imagen 
        bool_elisa = is_elisa_test(images[0])

        if not bool_elisa:
            print("La imagen no se reconoce como un test ELISA.")
            return jsonify({"status": "error", "message": "La imagen no se reconoce como un test ELISA."}), 200

        # Continuar con el procesamiento
        for i in range(len(images)):
            images[i] = change_resolution(edge_reduction(images[i]))

        centers, radius, image = circle_detection_corrected(images)

        preprocessed_image = shadow_removing(image)
        preprocessed_image = gamma_correction(preprocessed_image)

        # Obtener las intensidades
        intensities = analyze_wells(preprocessed_image, centers, radius)

        # Convertir intensities a una lista si es un ndarray
        if isinstance(intensities, np.ndarray):
            intensities = intensities.tolist()
        print(intensities)
        # Retornar los resultados
        return jsonify({"intensities": intensities})

    except Exception as e:
        print(f"Error: {e}")  # Imprimir el error en consola
        return jsonify({"error": str(e)}), 500