from flask import Flask, request, jsonify
from utils.utils_crop import *
from utils.utils_validation import *
from utils.utils_graphics import *
from utils.utils_preprocessing import *
from circle_detection import *
from analyze_wells import *
import os

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

        # Leer la imagen con OpenCV directamente desde el archivo de imagen
        image_file_bytes = np.frombuffer(image_file.read(), np.uint8)  # Convertir el archivo a bytes
        image = cv.imdecode(image_file_bytes, cv.IMREAD_COLOR)  # Decodificar la imagen desde los bytes

        # Verificar si la imagen es nula
        if image is None:
            print("Received image is null.")
            return jsonify({"error": "Received image is null."}), 200

        ## Guardar la imagen en un directorio específico usando OpenCV
        #save_directory = 'uploads'
        #os.makedirs(save_directory, exist_ok=True)  # Crear el directorio si no existe
        #image_path = os.path.join(save_directory, f"{os.path.splitext(image_file.filename)[0]}_processed.png")
        #cv.imwrite(image_path, image)  # Guardar la imagen

        # Convertir a escala de grises si es necesario
        if len(image.shape) == 3 and image.shape[2] == 3:  # Comprobar si tiene 3 canales (RGB)
            image_gray = cv.cvtColor(image, cv.COLOR_BGR2GRAY)
        else:
            image_gray = image

        # Recortar la imagen (usando tus utilidades)
        images = list(crop_elisa_plate(image, top_left_x, top_left_y, bottom_right_x, bottom_right_y))

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
        
        output_image_path = "desarrollo_web/uploads/output_image.png"
        plot_wells_with_intensity(image, centers, radius, intensities, output_image_path)

        # Retornar los resultados
        return jsonify({"intensities": intensities})

    except Exception as e:
        print(f"Error: {e}")  # Imprimir el error en consola
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)