#####--- Librerias ---#####
import numpy as np
import cv2 as cv
import sys
import imutils
from matplotlib import pyplot as plt
from ipywidgets import interact, IntSlider, FloatSlider, RadioButtons, Checkbox
from utils import utils_detection 
from utils import utils_preprocessing 
from utils import utils_trim


image = cv.imread('base_1.jpg')


# ?????????????????????????????????????