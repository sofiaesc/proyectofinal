#####--- Libraries ---#####
from utils.utils_crop import *
from utils.utils_validation import *
from utils.utils_graphics import *
from utils.utils_preprocessing import *
from circle_detection import *
from analyze_wells import *

# Initial input of the image
image = cv.imread('base_1.jpg')
images = list(select_and_crop_elisa_plate(image))

# Validation to check if it's an ELISA test:
bool_elisa = is_elisa_test(images[0])

if not bool_elisa:
    raise ValueError("The loaded image is not an ELISA test. Exiting the program.")

# Resolution changes to normalize circle detection:
for i in range(len(images)):
    images[i] = change_resolution(edge_reduction(images[i]))

# Detecting the plate's circles or wells:
centers, radius, image = circle_detection_corrected(images)

# Pre-processing the image before determining the results:
image = shadow_removing(image)
image = gamma_correction(image)

# Obtaining the results for each well:
_ = analyze_wells(image, centers, radius)