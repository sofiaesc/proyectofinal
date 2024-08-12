import cv2
import numpy as np

def gamma_correction(image):
    # Pasar imagen a grises si esta cargada a color
    if len(image.shape) == 3:
        gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    else:
        gray = image
    
    mean_intensity = np.mean(gray) / 255.0      # Intensidad media de la imagen
    gamma = 1.0 / (mean_intensity + 1e-8)       # Gamma inversamente proporcional a la media de intensidad
    gamma = np.clip(gamma, 0.5, 2.0)            # Limitar gamma

    mean_intensity_cap = 1.0 / gamma
    table = np.array([(i / 255.0) ** mean_intensity_cap * 255 for i in np.arange(0, 256)]).astype("uint8")
    
    return cv2.LUT(image, table)


nombre_imagen = 'sillas_oscuro'
image_path = 'correccion_gamma/Imagenes base/' + nombre_imagen + '.jpg'
imagen = cv2.imread(image_path)
if imagen is None:
    print(f"No se pudo cargar la imagen. Verifica la ruta.")
else:
    imagen_corregida = gamma_correction(imagen)
    cv2.imwrite('correccion_gamma/Imagenes resultado/'+nombre_imagen+'_gammaCorregido.jpg', imagen_corregida)
