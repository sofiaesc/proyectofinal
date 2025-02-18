# ELISATester

**ELISATester** es el producto final del proyecto de carrera en Ingeniería en Informática, diseñado para ofrecer una solución innovadora en el análisis de resultados de pruebas ELISA (Enzyme-Linked Immunosorbent Assay) para el diagnóstico de la leishmaniasis en perros. Esta aplicación web, que también cuenta con soporte móvil, permite a los usuarios cargar fotografías de las pruebas y obtener resultados más rápidos y precisos en comparación con la interpretación manual, reduciendo la dependencia de métodos tradicionales y herramientas costosas como el espectrofotómetro.

![image](https://github.com/user-attachments/assets/1a00bf95-5b4c-4c95-9944-ace5845b1aa7)

## Características principales:

- **Análisis automatizado de imágenes**: ELISATester utiliza técnicas de procesamiento digital de imágenes, incluyendo manejo del lienzo, correlación de imágenes, normalización y corrección de gamma. Esto permite obtener resultados consistentes aún en condiciones de iluminación variables.

- **Interfaz de usuario intuitiva y responsive**: La aplicación web ofrece una experiencia amigable para el usuario con animaciones de guía e instrucciones en la página principal. Además, se ajusta para su correcta visualización y funcionalidad para computadoras de escritorio o dispositivos móviles.

- **Gestión de usuarios**: ELISATester incluye un sistema de gestión de usuarios, donde uno debe registrarse e iniciar sesión para acceder a las funcionalidades de la aplicación. De esta manera, se garantiza a los usuarios facilidad en el almacenamiento de datos y la privacidad de los mismos.

## Arquitectura técnica:

- Módulo de procesamiento de imágenes realizado en **Python** con las librerías OpenCV, Matplotlib y Numpy. Se integra al sistema mediante la librería **Flask**.

- Desarrollo backend en **PHP Symfony**, con integración en **MySQL** para el manejo de datos.
  
- Frontend implementado utilizando tecnologías web estándar **JavaScript**, **HTML** y **CSS**.

## ¿Cómo funciona?

![ELISATester](https://github.com/user-attachments/assets/a2775c82-6871-4bc1-a5cd-71b2acdce269)

1. **Carga de imagen**: El usuario, una vez iniciada la sesión, puede cargar la imagen de su test.
2. **Procesamiento de imagen**: El usuario determina los pocillos de la placa que quiere analizar. Luego, el sistema aplica el algoritmo de procesamiento de imágenes para obtener resultados precisos sobre dichos pocillos.
3. **Lista de resultados**: Los resultados del análisis se muestran de manera instantánea, y el usuario puede acceder a ellos desde su historial o eliminarlos.
4. **Detalle de test**: El usuario puede consultar el detalle de cada uno de sus propios tests almacenados. Aquí, puede modificar información del test consultado como el nombre y la descripción, o descargar un informe con la información completa del mismo.
