<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use App\Form\TestType;
use App\Entity\Test;
use App\Form\UsuarioType;
use App\Entity\Usuario;

class ImagenController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/image_upload', name: 'app_image_upload', methods: ['GET', 'POST'])]
    public function image_upload(Request $request, HttpClientInterface $client): Response
    {
        $test = new Test();
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);
    
        $respuesta_python = null;  // Inicializar la variable
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Procesar la imagen
            $foto = $form->get('foto')->getData();
            $x1 = (int) $form->get('x1')->getData();
            $y1 = (int) $form->get('y1')->getData();
            $x2 = (int) $form->get('x2')->getData();
            $y2 = (int) $form->get('y2')->getData();
    
            // Enviar la imagen y coordenadas al servidor Flask
            $imagePath = $foto->getPathname(); // Ruta temporal del archivo subido
    
            // Crear un array de datos para enviar
            $formData = [
                'top_left_x' => $x1,
                'top_left_y' => $y1,
                'bottom_right_x' => $x2,
                'bottom_right_y' => $y2,
                'image' => fopen($imagePath, 'r'), // Añadir la imagen aquí
            ];
    
            // Hacer la petición
            $response = $client->request('POST', 'http://localhost:5000/process', [
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                ],
                'body' => $formData, // Usar 'body' en lugar de 'multipart'
            ]);
    
            // Procesar la respuesta
            if ($response->getStatusCode() === 200) {
                $respuesta_python = $response->toArray(); // Obtener datos de la respuesta
            } else {
                $errorData = $response->toArray(); // Obtener datos de error
                // Maneja el error usando $errorData['message']...
            }
        }
    
        return $this->render('front/image_upload.html.twig', [
            'respuesta_python' => $respuesta_python, // Pasar respuesta de Python
            'form' => $form->createView(),
        ]);
    }
}
