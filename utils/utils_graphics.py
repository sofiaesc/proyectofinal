import cv2 as cv
import numpy as np
from matplotlib import pyplot as plt

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def circle_graph(image, wells):
    img_with_circles = image.copy()
    for (x, y, r) in wells:
        cv.circle(img_with_circles, (x, y), r, (127, 0, 255), 4)
    return img_with_circles

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def histogram_well_graph(yellow_channel, detected_centers):
    for i in range(96):
        center = detected_centers[i]
        x,y = (2400,1000)
        if (np.abs(x-center[0])+np.abs(y-center[1])) < 200:
            break

    x,y,r = detected_centers[i]

    mask = np.zeros(yellow_channel.shape[:2], dtype=np.uint8)
    cv.circle(mask, (x,y), r, (255), thickness=-1)

    mask_canal = cv.bitwise_and(yellow_channel, yellow_channel, mask=mask)
    hist = cv.calcHist([yellow_channel], [0], mask, [256], [0, 256])

    fig,ax = plt.subplots(1,2,figsize=(16,4))
    ax[0].imshow(mask_canal,vmin=0,vmax=255)
    ax[0].axis('off')
    ax[1].plot(hist)
    plt.tight_layout()
    plt.show()
    
#####--- CUESTIONES A TENER EN CUENTA ---#####
'''
Probablemente hay que cambiar los valores de x e y
'''
    
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def histogram_comparisson_graph(hist1,hist2,hist3,hist4):
    plt.plot(hist1, color='deepskyblue')
    plt.plot(hist2, color='mediumslateblue')
    plt.plot(hist3, color='deeppink')
    plt.plot(hist4, color='darkred')
    plt.legend()
    plt.title('Histogramas en canal amarillo')
    plt.show()
    
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def plot(image, name):
    cv.imwrite('Imagenes Resultado/'+name+'.jpg', image)
    
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
    
def plot_w_wells(image, wells, name='5_wells_detected'):
    output_image = image.copy()
    for (x, y, r) in wells:
        cv.circle(output_image, (x, y), r, (0, 0, 255), 2)
    plot(output_image, name)
    
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def draw_line_and_circles(line_image, circles, slope, intercept):
    # Dibujar la línea de mejor ajuste
    h, w = line_image.shape[:2]
    pt1 = (0, int(intercept))
    pt2 = (w, int(slope * w + intercept))
    cv.line(line_image, pt1, pt2, (0, 255, 0), 2)
    
    # Dibujar los círculos y las líneas de distancia
    for (x, y, r) in circles:
        x = int(x)
        y = int(y)
        # Dibujar el círculo
        cv.circle(line_image, (x, y), r, (0, 0, 255), 2)
        
        # Coefficients of the line equation (Ax + By + C = 0)
        A = slope
        B = -1
        C = intercept
        
        # Calcular el punto en la línea más cercano al círculo
        if A != 0:  # Evitar división por cero
            x_line = (B * (B * x - A * y) - A * C) / (A**2 + B**2)
            y_line = (A * (-B * x + A * y) - B * C) / (A**2 + B**2)
            point_on_line = (int(x_line), int(y_line))
            # Dibujar la línea desde el círculo hasta el punto en la línea de mejor ajuste
            cv.line(line_image, (x, y), point_on_line, (255, 0, 0), 2)
    
    return line_image

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#