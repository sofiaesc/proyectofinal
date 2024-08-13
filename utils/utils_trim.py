import cv2 as cv
import numpy as np

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def recorte_a_placa(image):
    # Bordes con Canny
    gray_image = cv.cvtColor(image, cv.COLOR_BGR2GRAY)
    blurred_image = cv.GaussianBlur(gray_image, (11,11), 0)
    edges = cv.Canny(blurred_image, 20, 20, L2gradient=False)

    # Aplicar operaciones morfológicas para cerrar gaps
    kernel = np.ones((18,18), np.uint8)
    edges2 = cv.morphologyEx(edges, cv.MORPH_CLOSE, kernel)

    # Encontrar contornos
    contours, _ = cv.findContours(edges2, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE)

    # Asumiendo que tenemos un contorno dominante (el del rectángulo rotado)
    contour = max(contours, key=cv.contourArea)

    # Encontrar el rectángulo de área mínima que circunscribe el contorno
    rect = cv.minAreaRect(contour)

    # Obtener los vértices del rectángulo
    box = cv.boxPoints(rect)
    box = np.intp(box)

    # Corregir ángulo de la image
    dx = box[1][0] - box[0][0]
    dy = box[1][1] - box[0][1]
    angle = np.degrees(np.arctan2(dy, dx))

    # ajustes p/ q quede horizontal
    if angle < -45:
        angle += 90
    elif angle > 45:
        angle -= 90

    # Obtener el centro de la image
    (h, w) = image.shape[:2]
    center = (w // 2, h // 2)

    # Crear la matriz de rotación
    M = cv.getRotationMatrix2D(center, angle, 1.0)

    # Aplicar la rotación a la image
    rotated_image = cv.warpAffine(image, M, (w, h))

    # Rotar las coordenadas del rectángulo
    rotated_vertices = np.dot(box - center, M[:, :2].T) + center

    # Encontrar el bounding box del rectángulo rotado
    x, y, w, h = cv.boundingRect(rotated_vertices.astype(np.int32))

    # Recortar la image usando el bounding box
    cropped_image = rotated_image[y:y+h, x:x+w]

    return cropped_image

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def edge_reduction(image):
  porcental_x = 77/1206
  porcental_y = 49/792

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

    # Encontrar los límites de recorte basados en los círculos detectados
    min_x = min(x - r for x, y, r in pocillos)
    max_x = max(x + r for x, y, r in pocillos)
    min_y = min(y - r for x, y, r in pocillos)
    max_y = max(y + r for x, y, r in pocillos)

    # Asegurarse de que los límites están dentro del tamaño de la image
    min_x = max(min_x, 0)
    max_x = min(max_x, new_trim_image.shape[1])
    min_y = max(min_y, 0)
    max_y = min(max_y, new_trim_image.shape[0])

    # Actualizar las coordenadas de los círculos detectados después del recorte
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