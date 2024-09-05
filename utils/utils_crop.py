import cv2 as cv

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

import cv2 as cv

def select_and_crop_elisa_plate(image):
    # Obtener dimensiones de la imagen
    image_height, image_width = image.shape[:2]

    # Definir el tamaño máximo de la ventana
    max_window_width = 800
    max_window_height = 600

    # Calcular el factor de escalado para ajustar la imagen a la ventana
    scale_factor = min(max_window_width / image_width, max_window_height / image_height)

    # Calcular las nuevas dimensiones de la ventana
    window_width = int(image_width * scale_factor)
    window_height = int(image_height * scale_factor)

    # Redimensionar la imagen para ajustarla a la ventana
    resized_image = cv.resize(image, (window_width, window_height))

    # Redimensionar la ventana
    window_name = "Select the ELISA Plate"
    cv.namedWindow(window_name, cv.WINDOW_NORMAL)
    cv.resizeWindow(window_name, window_width, window_height)

    # Mostrar la imagen y permitir que el usuario seleccione el rectángulo
    print("Select the region containing the ELISA plate and press ENTER or SPACE.")
    roi = cv.selectROI(window_name, resized_image)

    # Ajustar las coordenadas de recorte a la imagen original
    x, y, w, h = roi
    x_original = int(x / scale_factor)
    y_original = int(y / scale_factor)
    w_original = int(w / scale_factor)
    h_original = int(h / scale_factor)

    # Recorte original
    crop = image[y_original:y_original+h_original, x_original:x_original+w_original]

    # Cálculo del 2% para ampliación y reducción
    delta_w = int(w_original * 0.02)
    delta_h = int(h_original * 0.02)

    # Recorte ampliado (hacia afuera)
    x_enlarged = max(0, x_original - delta_w)
    y_enlarged = max(0, y_original - delta_h)
    w_enlarged = min(image_width - x_enlarged, w_original + 2 * delta_w)
    h_enlarged = min(image_height - y_enlarged, h_original + 2 * delta_h)
    enlarged_crop = image[y_enlarged:y_enlarged+h_enlarged, x_enlarged:x_enlarged+w_enlarged]

    # Recorte reducido (hacia adentro)
    x_reduced = x_original + delta_w
    y_reduced = y_original + delta_h
    w_reduced = max(1, w_original - 2 * delta_w)
    h_reduced = max(1, h_original - 2 * delta_h)
    reduced_crop = image[y_reduced:y_reduced+h_reduced, x_reduced:x_reduced+w_reduced]

    return crop, enlarged_crop, reduced_crop

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def edge_reduction(image):  # Reducing the plate's edges off the image to obtain only the wells.
  porcental_x = 0.06
  porcental_y = 0.06

  H, W, _ = image.shape
  edge_H = int(H*porcental_y)
  edge_W = int(W*porcental_x)
  end_H = H - edge_H
  end_W = W - edge_W

  return image[edge_H:end_H,edge_W:end_W]

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def trim_external_circles(image, pocillos):

    new_trim_image = image.copy()

    # Find the trim limits based on the detected circles.
    min_x = min(x - r for x, y, r in pocillos)
    max_x = max(x + r for x, y, r in pocillos)
    min_y = min(y - r for x, y, r in pocillos)
    max_y = max(y + r for x, y, r in pocillos)

    # Making sure the limits are inside the image size
    min_x = max(min_x, 0)
    max_x = min(max_x, new_trim_image.shape[1])
    min_y = max(min_y, 0)
    max_y = min(max_y, new_trim_image.shape[0])

    # Update the coordinates of the detected circles after the trim
    new_wells = [(x - min_x, y - min_y, r) for x, y, r in pocillos]
    new_trim_image = new_trim_image[min_y:max_y, min_x:max_x]

    return new_trim_image, new_wells

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def change_resolution(image):
    return cv.resize(image, (2880, 1920))

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#