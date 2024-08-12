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