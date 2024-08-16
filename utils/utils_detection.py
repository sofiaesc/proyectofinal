import cv2 as cv
import numpy as np

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def circle_detection(image, radius, g_kernel):
    height, width = image.shape[:2]
    blurred_image = cv.GaussianBlur(image, (g_kernel, g_kernel), 0)
    circles = cv.HoughCircles(blurred_image, cv.HOUGH_GRADIENT, dp=2.5, minDist=radius*2+5, param1=23, param2=0.9, minRadius=radius, maxRadius=radius)
    wells = []
    if circles is not None:
        circles = np.round(circles[0, :]).astype("int")
        for circle in circles:
            x, y, r = circle
            if r <= x <= (width - r) and r <= y <= (height - r):
                wells.append((x, y, r))
    
    return wells

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def find_extreme_circles(wells, n, direction):
    if direction == "left":
        wells_sorted = sorted(wells, key=lambda c: c[0])
    elif direction == "right":
        wells_sorted = sorted(wells, key=lambda c: c[0], reverse=True)
    elif direction == "top":
        wells_sorted = sorted(wells, key=lambda c: c[1])
    elif direction == "bottom":
        wells_sorted = sorted(wells, key=lambda c: c[1], reverse=True)
    else:
        raise ValueError("Direction must be 'left', 'right', 'top', or 'bottom'")

    return wells_sorted[:n]

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def calculate_best_fit_line(circles):
    points = np.array([circle[:2] for circle in circles], dtype=np.float32)
    [vx, vy, x, y] = cv.fitLine(points, cv.DIST_L2, 0, 0.01, 0.01)
    slope = vy / vx
    intercept = y - slope * x
    return slope[0], intercept[0]

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def calculate_distance(circles, slope, intercept):
    total_distance = 0
    for (x, y, r) in circles:
        x = int(x)
        y = int(y)

        A = slope
        B = -1
        C = intercept
        
        distance = abs(A * x + B * y + C) / np.sqrt(A**2 + B**2)
        total_distance += distance
        
    return total_distance

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def calculate_intersection(slope1, intercept1, slope2, intercept2):
    if slope1 != slope2:  # Verifies that the lines aren't parallel
        x = (intercept2 - intercept1) / (slope1 - slope2)
        y = slope1 * x + intercept1
        return int(x), int(y)
    else:
        return None  # If the lines are paralel, there's no intersection
    
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def generate_grid_points(left_limit, right_limit, top_limit, bottom_limit, n_cols=12, n_rows=8):
    # Generate mesh points
    x_coords = np.linspace(left_limit, right_limit, n_cols)
    y_coords = np.linspace(top_limit, bottom_limit, n_rows)
    grid_points = [(int(x), int(y)) for y in y_coords for x in x_coords]
    return grid_points

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#