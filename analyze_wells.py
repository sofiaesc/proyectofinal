import cv2 as cv
import numpy as np
import matplotlib.pyplot as plt

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def analyze_wells(image, grid_points, well_radius):
    # Calculate the yellow channel from the BGR image
    B, _, _ = cv.split(image)
    yellow_channel = 255 - B
    yellow_channel = cv.normalize(yellow_channel, None, 0, 255, cv.NORM_MINMAX)
    yellow_channel = np.uint8(yellow_channel)

    intensities = np.zeros((8, 12), dtype=float)  # To store the average intensity values for each well
    radius = int(well_radius * 0.6)

    for i, pt in enumerate(grid_points):
        # Create a circular mask for the current well
        well_mask = np.zeros(yellow_channel.shape, dtype=np.uint8)
        cv.circle(well_mask, pt, radius, 255, -1)
        
        # Extract the circular region from the yellow channel
        well_region = cv.bitwise_and(yellow_channel, yellow_channel, mask=well_mask)
        
        # Calculate the mean intensity within the well
        mean_intensity = cv.mean(well_region, mask=well_mask)[0]
        
        # Store the intensity in the correct position (8x12 grid)
        row = i // 12
        col = i % 12
        intensities[row, col] = round(mean_intensity, 2)
    
    # Plot the results
    plot_wells_with_intensity(image, grid_points, well_radius, intensities)

    return intensities

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def plot_wells_with_intensity(image, grid_points, well_radius, intensities):
    plt.figure(figsize=(10, 8))
    plt.imshow(cv.cvtColor(image, cv.COLOR_BGR2RGB))

    for _, (pt, intensity) in enumerate(zip(grid_points, intensities.flatten())):
        # Determine the circle color based on the intensity
        if intensity > 100:
            circle_color = 'green'
        else:
            circle_color = 'red'
        
        # Draw the circle for the well
        circle = plt.Circle((pt[0], pt[1]), well_radius, color=circle_color, fill=False, linewidth=2)
        plt.gca().add_patch(circle)
        
        # Add the intensity value as text
        plt.text(pt[0], pt[1], str(int(intensity)), color='black', fontsize=12, ha='center', va='center')

    plt.axis('off')
    plt.show()
