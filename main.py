from flask import Flask, request, jsonify
from utils.utils_crop import *
from utils.utils_validation import *
from utils.utils_graphics import *
from utils.utils_preprocessing import *
from circle_detection import *
from analyze_wells import *
import io
from PIL import Image

app = Flask(__name__)

@app.route('/process', methods=['POST'])
def process_data():
    # Recibir la imagen y parámetros
    image_file = request.files['image']  # Obtener la imagen
    top_left_x = int(request.form['top_left_x'])
    top_left_y = int(request.form['top_left_y'])
    bottom_right_x = int(request.form['bottom_right_x'])
    bottom_right_y = int(request.form['bottom_right_y'])
    
    # Abrir la imagen
    image = Image.open(image_file.stream)

    # Puedes hacer el recorte si es necesario
    image = image.crop((top_left_x, top_left_y, bottom_right_x, bottom_right_y))

    # Procesar la imagen
    images = [image]  # Si necesitas múltiples imágenes, ajusta esto
    bool_elisa = is_elisa_test(images[0])

    if not bool_elisa:
        return jsonify({"error": "The loaded image is not an ELISA test."}), 400

    # Continuar con el procesamiento
    for i in range(len(images)):
        images[i] = change_resolution(edge_reduction(images[i]))

    centers, radius, image = circle_detection_corrected(images)

    preprocessed_image = shadow_removing(image)
    preprocessed_image = gamma_correction(preprocessed_image)

    intensities = analyze_wells(preprocessed_image, centers, radius)

    # Retornar los resultados
    return jsonify({"intensities": intensities})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
