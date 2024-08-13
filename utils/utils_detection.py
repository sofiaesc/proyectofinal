import cv2 as cv
import numpy as np
from sklearn.linear_model import LinearRegression

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def circle_detection(image, radius, g_kernel):
    height, width = image.shape[:2]
    blurred_image = cv.GaussianBlur(image, (g_kernel, g_kernel), 0)
    circles = cv.HoughCircles(blurred_image, cv.HOUGH_GRADIENT, dp=2.5, minDist=radius*2+5, param1=23, param2=0.9, minRadius=radius, maxRadius=radius)
    wells = []
    if circles is not None:
        circles = np.round(circles[0, :]).astype("int")
        for circle in circles:
            x, y, r = circle
            if r <= x <= (width - r) and r <= y <= (height - r):
                wells.append((x, y, r))
    
    return wells

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def find_rightmost_circles(wells, n=8):
    wells_sorted = sorted(wells, key=lambda c: c[0], reverse=True)
    return wells_sorted[:n]

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def find_leftmost_circles(wells, n=8):
    wells_sorted = sorted(wells, key=lambda c: c[0])
    return wells_sorted[:n]

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def find_topmost_circles(wells, n=12):
    wells_sorted = sorted(wells, key=lambda c: c[1])
    return wells_sorted[:n]

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def find_bottommost_circles(wells, n=12):
    wells_sorted = sorted(wells, key=lambda c: c[1], reverse=True)
    return wells_sorted[:n]

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def draw_best_fit_line(line_image, circles):
    # Extraer coordenadas de los centros de los círculos
    points = np.array([circle[:2] for circle in circles], dtype=np.float32)
    
    # Ajuste de línea usando la función de ajuste por mínimos cuadrados
    if len(points) < 2:
        return line_image
    
    [vx, vy, x, y] = cv.fitLine(points, cv.DIST_L2, 0, 0.01, 0.01)
    slope = vy / vx
    intercept = y - slope * x
    
    # Determinar los puntos de la línea en la imagen
    h, w = line_image.shape[:2]
    pt1 = (0, int(intercept))
    pt2 = (w, int(slope * w + intercept))
    
    # Dibujar la línea en la imagen
    cv.line(line_image, pt1, pt2, (0, 255, 0), 2)
    
    # Dibujar líneas de distancia desde los círculos a la línea
    total_distance = 0
    for (x, y, r) in circles:
        x = int(x)
        y = int(y)
        cv.circle(line_image, (x, y), r, (0, 0, 255), 2)
        
        # Coefficients of the line equation (Ax + By + C = 0)
        A = slope
        B = -1
        C = intercept
        
        # Calcular la distancia desde el círculo a la línea
        distance = abs(A * x + B * y + C) / np.sqrt(A**2 + B**2)
        total_distance+=distance
        # Punto en la línea más cercano al círculo
        if A != 0:  # Avoid division by zero
            x_line = (B * (B * x - A * y) - A * C) / (A**2 + B**2)
            y_line = (A * (-B * x + A * y) - B * C) / (A**2 + B**2)
            point_on_line = (int(x_line), int(y_line))
            cv.line(line_image, (x, y), point_on_line, (255, 0, 0), 2)
    
    return line_image, total_distance



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