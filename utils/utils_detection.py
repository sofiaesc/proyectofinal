import cv2 as cv
import numpy as np

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def circle_detection(image):
    print("punto 1")
    blurred_image = cv.GaussianBlur(image, (5, 5), 0) #La imagen tiene que ser gris
    circles = cv.HoughCircles(blurred_image, cv.HOUGH_GRADIENT, dp=2.5, minDist=207, param1=23, param2=11, minRadius=100, maxRadius=100)
    print("punto 2")
    wells = []
    if circles is not None:
        circles = np.round(circles[0, :]).astype("int")
        for circle in circles:
            x, y, r = circle
            wells.append((x, y, r))
    return wells

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def completar_wells(radio, lista_wells, tolerancia, ancho, alto):
    # Inicializar la matriz de círculos teóricos para almacenar las coordenadas
    circulos_teoricos = [[[0, 0] for _ in range(8)] for _ in range(12)]

    # Calcular los espaciados horizontal y vertical entre los círculos
    h_ancho = (ancho - (12 * 2 * radio)) / 13
    h_alto = (alto - (8 * 2 * radio)) / 9

    # Calcular las coordenadas de los centros de los círculos
    for i in range(12):
        for j in range(8):
            circulos_teoricos[i][j][0] = radio + (2 * radio + h_ancho) * i + h_ancho
            circulos_teoricos[i][j][1] = radio + (2 * radio + h_alto) * j + h_alto

    # Listas para almacenar los círculos correctos e incorrectos
    circulos_correctos = []
    circulos_incorrectos = []

    # Comparar los círculos detectados con la matriz de referencia
    for i in range(12):
        for j in range(8):
            centro_x, centro_y = circulos_teoricos[i][j]
            coincidencia = False
            for (x, y, r) in lista_wells:
                if abs(x - centro_x) <= tolerancia and abs(y - centro_y) <= tolerancia:
                    circulos_correctos.append((int(x), int(y), int(r)))
                    coincidencia = True
                    break
            if not coincidencia:
                circulos_incorrectos.append((int(centro_x), int(centro_y), int(radio)))

    return circulos_teoricos, circulos_correctos, circulos_incorrectos

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
