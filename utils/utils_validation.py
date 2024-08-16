import cv2

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def calculate_histogram(image):
    # Calculate and normalize the histogram
    hist = cv2.calcHist([image], [0], None, [256], [0, 256])
    cv2.normalize(hist, hist)
    return hist

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def is_elisa_test(input_image, threshold=0.65):
    # Reference images are fixed
    reference_images = [
        cv2.imread('utils/references/reference_1.jpg', cv2.IMREAD_GRAYSCALE),
        cv2.imread('utils/references/reference_2.jpg', cv2.IMREAD_GRAYSCALE),
        cv2.imread('utils/references/reference_3.jpg', cv2.IMREAD_GRAYSCALE),
        cv2.imread('utils/references/reference_4.jpg', cv2.IMREAD_GRAYSCALE)
    ]

    # Compare the input image histogram with each reference image
    for ref_image in reference_images:
        # Resize the input image to match the current reference image dimensions
        input_image_resized = cv2.resize(input_image, (ref_image.shape[1], ref_image.shape[0]))

        # Calculate the histogram for the resized input image
        hist_input = calculate_histogram(input_image_resized)
        hist_ref = calculate_histogram(ref_image)
        
        # Compare histograms
        correlation = cv2.compareHist(hist_input, hist_ref, cv2.HISTCMP_CORREL)
        
        if correlation > threshold:
            return True

    return False

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#