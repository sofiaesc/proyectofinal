#####--- Librerias ---#####
import numpy as np
import cv2 as cv
import sys
import imutils
from matplotlib import pyplot as plt
from ipywidgets import interact, IntSlider, FloatSlider, RadioButtons, Checkbox
from utils.utils_detection import *
from utils.utils_preprocessing import *
from utils.utils_trim import *
from utils.utils_graphics import *


image = cv.imread('base_1_borderless_2.jpg')
# El usuario delimita la imagen
image       = change_resolution(image)
plot(image, '1_new_resol')
image       = edge_reduction(image)
plot(image, '2_only circles')
#image       = shadow_removing(image)
#plot(image, '3_no_shadow')
#image       = gamma_correction(image)
#plot(image, '4_gamma_corrected')


gray_image = cv.cvtColor(image, cv.COLOR_BGR2GRAY)
wells = circle_detection(gray_image)
print(len(wells))

plot_w_wells(image, wells)



# 
# ?????????????????????????????????????