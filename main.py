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

plot(images[0], "0")
plot(images[1], "1")
plot(images[2], "2")

# Detecting the plate's circles or wells:
centers, radius, image = circle_detection_corrected(images)

# Pre-processing the image before determining the results:
preprocessed_image = shadow_removing(image)
preprocessed_image = gamma_correction(preprocessed_image)

# Obtaining the results for each well:
intensities = analyze_wells(preprocessed_image, centers, radius)

# Plot the results with the original image
plot_wells_with_intensity(image, centers, radius, intensities)