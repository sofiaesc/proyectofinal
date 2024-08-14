
from utils.utils_detection import *
from utils.utils_graphics import *

def circle_detection_corrected(image): 
    
    most_accurate = float('inf')
    best_radius = None
    best_inter_tl = None
    best_inter_tr = None
    best_inter_bl = None
    best_inter_br = None

    gray_image = cv.cvtColor(image, cv.COLOR_BGR2GRAY)
    for radius in range(85, 105):                   #   Modificar estos bucles, cambiar rangos e incluso el parámetro en sí para encontrar el Hugh mas general posible
        for kernel in range(5, 6, 2):               #   Modificar estos bucles, cambiar rangos e incluso el parámetro en sí para encontrar el Hugh mas general posible
            wells = circle_detection(gray_image, radius, kernel)
            
            rightmost_wells  = find_extreme_circles(wells, 8, "right")
            leftmost_wells   = find_extreme_circles(wells, 8, "left")
            topmost_wells    = find_extreme_circles(wells, 12, "top")
            bottommost_wells = find_extreme_circles(wells, 12, "bottom")
            
            slope_right, intercept_right    = calculate_best_fit_line(rightmost_wells)
            slope_left, intercept_left      = calculate_best_fit_line(leftmost_wells)
            slope_top, intercept_top        = calculate_best_fit_line(topmost_wells)
            slope_bottom, intercept_bottom  = calculate_best_fit_line(bottommost_wells)
            
            total_dist =   (calculate_distance(rightmost_wells,  slope_right,  intercept_right) + 
                            calculate_distance(leftmost_wells,   slope_left,   intercept_left)  +
                            calculate_distance(topmost_wells,    slope_top,    intercept_top)   +
                            calculate_distance(bottommost_wells, slope_bottom, intercept_bottom))
            
            print(radius, kernel, "Distancia total: ", total_dist)
            
            inter_top_left     = calculate_intersection(slope_left,  intercept_left,  slope_top,    intercept_top)
            inter_top_right    = calculate_intersection(slope_right, intercept_right, slope_top,    intercept_top)
            inter_bottom_left  = calculate_intersection(slope_left,  intercept_left,  slope_bottom, intercept_bottom)
            inter_bottom_right = calculate_intersection(slope_right, intercept_right, slope_bottom, intercept_bottom)
            
            if total_dist < most_accurate and inter_bottom_left and inter_bottom_right and inter_top_left and inter_top_right:
                most_accurate = total_dist
                best_inter_tl = inter_top_left
                best_inter_tr = inter_top_right
                best_inter_bl = inter_bottom_left
                best_inter_br = inter_bottom_right
                best_radius = radius
            
            #--- Sección de graficación ---# COMENTAR PARA PRODUCCION
            image_draw = image.copy()
            image_draw = draw_line_and_circles(image_draw, rightmost_wells,  slope_right,  intercept_right)  # Dibujar línea y círculos
            image_draw = draw_line_and_circles(image_draw, leftmost_wells,   slope_left,   intercept_left)
            image_draw = draw_line_and_circles(image_draw, topmost_wells,    slope_top,    intercept_top)
            image_draw = draw_line_and_circles(image_draw, bottommost_wells, slope_bottom, intercept_bottom)
            for pt in [inter_top_left, inter_bottom_left, inter_top_right, inter_bottom_right]:
                if pt is not None:
                    cv.circle(image_draw, pt, 20, (0, 255, 255), -1)  # Puntos amarillos para marcar las intersecciones
                    
            plot_w_wells(image_draw, wells, f'{int(total_dist)}_r_{radius}k_{kernel}')
            
    if(inter_bottom_left and inter_bottom_right and inter_top_left and inter_top_right):
        print(best_inter_tl)
        print(best_inter_tr)
        print(best_inter_bl)
        print(best_inter_br)
        left_limit = (best_inter_bl[0] + best_inter_tl[0])/2
        right_limit = (best_inter_br[0] + best_inter_tr[0])/2
        top_limit = (best_inter_tl[1] + best_inter_tr[1])/2
        bottom_limit = (best_inter_bl[1] + best_inter_br[1])/2
        print(left_limit)
        print(right_limit)
        print(top_limit)
        print(bottom_limit)
        grid_points = generate_grid_points(left_limit, right_limit, top_limit, bottom_limit)
        image_circles = image.copy()
        for pt in grid_points:
            cv.circle(image_circles, pt, best_radius, (255, 0, 0), 4)  # Dibujar puntos de la malla en azul
        plot(image_circles, "Imagen con circulos o algo asi")
        
    return 