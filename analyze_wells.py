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
    
    return intensities

#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#
#--------------------------------------------------------------------------#

def plot_wells_with_intensity(image, grid_points, well_radius, intensities, output_path):
    # Hacer una copia de la imagen original para no modificarla
    output_image = image.copy()
    
    # Convertir intensities a un numpy array si es necesario
    if isinstance(intensities, list):
        intensities = np.array(intensities)
    
    # Asegurarse de que el radio del pozo sea un entero
    well_radius = int(well_radius)
    
    # Obtener las dimensiones de la imagen
    height, width = output_image.shape[:2]
    
    # Iterar a través de los puntos y las intensidades
    for pt, intensity in zip(grid_points, intensities.flatten()):
        if 0 <= pt[0] < width and 0 <= pt[1] < height:
            # Determinar el color del círculo basado en la intensidad
            if intensity > 105:
                circle_color = (0, 255, 0)  # Verde en BGR
            elif intensity < 95:
                circle_color = (0, 0, 255)  # Rojo en BGR
            else:
                circle_color = (0, 165, 255)  # Naranja en BGR

            # Dibujar el círculo
            cv.circle(output_image, (int(pt[0]), int(pt[1])), well_radius, circle_color, 2)

            # Agregar el valor de la intensidad como texto
            cv.putText(output_image, str(int(intensity)), (int(pt[0]), int(pt[1])),
                       cv.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 0), 1, cv.LINE_AA)
    
    # Guardar la imagen en la ruta especificada
    cv.imwrite(output_path, output_image)


