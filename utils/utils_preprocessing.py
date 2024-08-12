import cv2 as cv
import numpy as np

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def shadow_removing(imagen_bgr):
    
    # Convertir la imagen al espacio de color HSV
    imagen_hsv = cv.cvtColor(imagen_bgr, cv.COLOR_BGR2HSV)
    h, s, v = cv.split(imagen_hsv)

    # Aplicar la corrección de sombras en el canal de value (V)
    v_log = np.log1p(v.astype(np.float32))                      # Calculo el logaritmo de la componente de value para aumentar el rango del value claro y disminuir el oscuro
    v_log_blurred = cv.GaussianBlur(v_log, (29, 29), 0)         # Aplico un filtro de desenfoque para eliminar los detalles finos como las sombras
    v_log_shadow_removed = v_log - v_log_blurred                # Resto la imagen desenfocada a la imagen original, para quedarme solo con los detalles importantes
    v_shadow_removed = np.expm1(v_log_shadow_removed)           # vuelvo a la distribucion original de value

    # Normalizar el canal de luminosidad corregido
    v_shadow_removed = cv.normalize(v_shadow_removed, None, 0, 255, cv.NORM_MINMAX)
    v_shadow_removed = np.uint8(v_shadow_removed)

    # Recombinar los canales H, S y V corregido
    imagen_hsv_corrected = cv.merge([h, s, v_shadow_removed])

    # Convertir de nuevo al espacio de color RGB
    imagen_rgb_corrected = cv.cvtColor(imagen_hsv_corrected, cv.COLOR_HSV2BGR)

    return imagen_rgb_corrected


#####--- CUESTIONES A TENER EN CUENTA ---#####
'''
Se puede modificar el tamaño del kernel en Gaussian Blur segun convenga


'''

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#