import cv2 as cv
import numpy as np

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def shadow_removing(bgr_image):
    
    # Convert the image to the HSV color space
    hsv_image = cv.cvtColor(bgr_image, cv.COLOR_BGR2HSV)
    h, s, v = cv.split(hsv_image)

    # Apply shadow correction on the value (V) channel
    v_log = np.log1p(v.astype(np.float32))                      # Calculate the logarithm of the value component to increase the range of bright values and decrease the dark ones
    v_log_blurred = cv.GaussianBlur(v_log, (29, 29), 0)         # Apply a blur filter to remove fine details like shadows
    v_log_shadow_removed = v_log - v_log_blurred                # Subtract the blurred image from the original image, keeping only the important details
    v_shadow_removed = np.expm1(v_log_shadow_removed)           # Return to the original value distribution

    # Normalize the corrected luminosity channel
    v_shadow_removed = cv.normalize(v_shadow_removed, None, 0, 255, cv.NORM_MINMAX)
    v_shadow_removed = np.uint8(v_shadow_removed)

    # Recombine the H, S, and corrected V channels
    hsv_image_corrected = cv.merge([h, s, v_shadow_removed])

    # Convert back to the RGB color space
    rgb_image_corrected = cv.cvtColor(hsv_image_corrected, cv.COLOR_HSV2BGR)

    return rgb_image_corrected

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def gamma_correction(image):
    # Convert image to grayscale if it's loaded in color
    if len(image.shape) == 3:
        gray = cv.cvtColor(image, cv.COLOR_BGR2GRAY)
    else:
        gray = image
    
    mean_intensity = np.mean(gray) / 255.0      # Average intensity of the image
    gamma = 1.0 / (mean_intensity + 1e-8)       # Gamma inversely proportional to the average intensity
    gamma = np.clip(gamma, 0.5, 2.0)            # Limit gamma

    mean_intensity_cap = 1.0 / gamma
    table = np.array([(i / 255.0) ** mean_intensity_cap * 255 for i in np.arange(0, 256)]).astype("uint8")
    
    return cv.LUT(image, table)

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
