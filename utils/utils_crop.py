import cv2

def select_and_crop_elisa_plate(image):

    # Get image dimensions
    image_height, image_width = image.shape[:2]

    # Define maximum window size 
    max_window_width = 800
    max_window_height = 600

    # Calculate the scaling factor to fit the image to the window
    scale_factor = min(max_window_width / image_width, max_window_height / image_height)
    
    # Calculate the new window dimensions
    window_width = int(image_width * scale_factor)
    window_height = int(image_height * scale_factor)

    # Resize the image to fit the window
    resized_image = cv2.resize(image, (window_width, window_height))

    # Resize the window
    window_name = "Select the ELISA Plate"
    cv2.namedWindow(window_name, cv2.WINDOW_NORMAL)
    cv2.resizeWindow(window_name, window_width, window_height)

    # Display the image and allow the user to select the rectangle
    print("Select the region containing the ELISA plate and press ENTER or SPACE.")
    roi = cv2.selectROI(window_name, resized_image)

    # Adjust the crop coordinates to the original image
    x, y, w, h = roi
    x_original = int(x / scale_factor)
    y_original = int(y / scale_factor)
    w_original = int(w / scale_factor)
    h_original = int(h / scale_factor)

    # Crop the image according to the user's selection
    crop = image[y_original:y_original+h_original, x_original:x_original+w_original]

    return crop