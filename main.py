#####--- Librerias ---#####
import cv2 as cv
from utils.utils_preprocessing import *
from utils.utils_trim import *
from utils.utils_graphics import *
from utils.utils_crop import *
from circle_detection import *


image = cv.imread('base_1.jpg')
image = select_and_crop_elisa_plate(image)
image = edge_reduction(image)
image = change_resolution(image)
#image       = shadow_removing(image)
#plot(image, '3_no_shadow')
#image       = gamma_correction(image)
#plot(image, '4_gamma_corrected')
circle_detection_corrected(image)
# 
# ?????????????????????????????????????