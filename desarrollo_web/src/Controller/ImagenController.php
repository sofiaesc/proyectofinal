<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use App\Form\TestType;
use App\Entity\Test;
use App\Form\UsuarioType;
use App\Entity\Usuario;
use App\Entity\Pocillo;
use DateTime;
use DateTimeZone;

class ImagenController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/image_upload', name: 'app_image_upload', methods: ['GET', 'POST'])]
    public function image_upload(Request $request, HttpClientInterface $client, EntityManagerInterface $entityManager): Response
    {
        $test = new Test();
        $form = $this->createForm(TestType::class, $test, [
            'allow_extra_fields' => true, 
        ]);
        $form->handleRequest($request);

        $respuesta_python = null;  // Inicializar la variable

        if ($form->isSubmitted() && $form->isValid()) {
            // Procesar la imagen

            $foto = $form->get('foto')->getData();
            $x1 = (int) $form->get('x1')->getData();
            $y1 = (int) $form->get('y1')->getData();
            $x2 = (int) $form->get('x2')->getData();
            $y2 = (int) $form->get('y2')->getData();
            $selected_wells = $form->get('pocillos_hab')->getData();

            // Enviar la imagen y coordenadas al servidor Flask
            $imagePath = $foto->getPathname(); // Ruta temporal del archivo subido

            // Crear un array de datos para enviar
            $formData = [
                'top_left_x' => $x1,
                'top_left_y' => $y1,
                'bottom_right_x' => $x2,
                'bottom_right_y' => $y2,
                'image' => fopen($imagePath, 'r'),
            ];

            // Hacer la petición
            $response = $client->request('POST', 'http://localhost:5000/process', [
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                ],
                'body' => $formData,
            ]);

            if ($response->getStatusCode() === 200) {
                $respuesta_python = $response->toArray();
                $selected_wells_array = str_split($selected_wells); 

                // Iniciar la transacción para persistir en la base de datos
                try {
                    // Recorrer la matriz de intensidades devuelta por Python
                    foreach ($respuesta_python['intensities'] as $rowIndex => $row) {
                        foreach ($row as $colIndex => $value) {
                            // Calcular el índice en selected_wells correspondiente a la posición actual
                            $index = $rowIndex * 12 + $colIndex;
                    
                            // Verificar si el índice existe en $selected_wells_array
                            if (isset($selected_wells_array[$index])) {
                                if ($selected_wells_array[$index] === '0') {
                                    $respuesta_python['intensities'][$rowIndex][$colIndex] = -1;
                                } else {
                                    $n_pocillo = new Pocillo();
                                    $n_pocillo->setFila($rowIndex);
                                    $n_pocillo->setColumna($colIndex);
                                    $n_pocillo->setIntensidad($respuesta_python['intensities'][$rowIndex][$colIndex]);
                                    $n_pocillo->setTest($test); // Establecer la relación entre Test y Pocillo
                                    $test->addPocillo($n_pocillo); // Añadir el Pocillo a la colección de Test, si es una relación bidireccional
                                    $entityManager->persist($n_pocillo);
                                }
                            }
                        }
                    }
                    // Persistir el Test y todos los Pocillos asociados
                    $output_image_path = $this->getParameter('kernel.project_dir') . '/uploads/output_image.png';
                    $foto = file_get_contents($output_image_path);
                    $test->setFoto($foto);
                    $test->setFechaHora(new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires')));
                    $test->setPocillosHab($selected_wells);
                    $entityManager->persist($test);
                    $entityManager->flush(); // Ejecutar la transacción

                    //si la transacción es correcta

                    return $this->render('front/test/test_list.html.twig', [
                        'respuesta_python' => $respuesta_python, // Pasar la matriz modificada a la vista
                        'form' => $form->createView(),
                    ]);
                } catch (\Exception $e) {
                    return $this->render('front/image_upload.html.twig', [
                        'respuesta_python' => $respuesta_python, 
                        'form' => $form->createView(),
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                $errorData = $response->toArray(); 
                return $this->render('front/image_upload.html.twig', [
                    'respuesta_python' => $respuesta_python, 
                    'form' => $form->createView(),
                    'error' =>  $errorData, 
                ]);
            }
        }

        return $this->render('front/image_upload.html.twig', [
            'respuesta_python' => $respuesta_python, // Pasar respuesta de Python
            'form' => $form->createView(),
        ]);
    }
}
