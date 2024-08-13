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

more_accurate = float('inf')
opt_kernel = None
opt_radius = None
gray_image = cv.cvtColor(image, cv.COLOR_BGR2GRAY)
for radius in range(87, 93):
    for kernel in range(3, 6, 2):
        wells = circle_detection(gray_image, radius, kernel)
        
        image_draw = image.copy()
        rightmost_wells                 = find_rightmost_circles(wells)
        result_image_right, dist_right  = draw_best_fit_line(image_draw, rightmost_wells)
        leftmost_wells                  = find_leftmost_circles(wells)
        result_image_left, dist_left    = draw_best_fit_line(result_image_right, leftmost_wells)
        topmost_wells                   = find_topmost_circles(wells)
        result_image_up, dist_up        = draw_best_fit_line(result_image_left, topmost_wells)
        bottommost_wells                = find_bottommost_circles(wells)
        result_image_down, dist_down    = draw_best_fit_line(result_image_up, bottommost_wells)
        
        total_dist = dist_left+dist_right+dist_up+dist_down
        if total_dist<more_accurate:
            more_accurate = total_dist
            opt_kernel = kernel
            opt_radius = radius
        print(radius, kernel, "Distancia total: ", total_dist)
        plot_w_wells(result_image_down, wells, str(len(wells)) + ' finded_' + str(radius) + '-' + str(kernel))



# 
# ?????????????????????????????????????