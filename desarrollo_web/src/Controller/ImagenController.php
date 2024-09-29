<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImagenController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/procesamiento', name: 'app_image_processing', methods: ['GET', 'POST'])]
    public function image_processing(Request $request): Response
    {
        $result = null;

        if ($request->isMethod('POST')) {
            // Obtener la imagen y los parámetros del formulario
            $imageFile = $request->files->get('image');
            $topLeftX = $request->request->get('top_left_x');
            $topLeftY = $request->request->get('top_left_y');
            $bottomRightX = $request->request->get('bottom_right_x');
            $bottomRightY = $request->request->get('bottom_right_y');

            // Enviar la imagen y los parámetros a la API de Python
            $response = $this->httpClient->request('POST', 'http://localhost:5000/process', [
                'headers' => ['Content-Type' => 'multipart/form-data'],
                'body' => [
                    'image' => fopen($imageFile->getPathname(), 'r'),
                    'top_left_x' => $topLeftX,
                    'top_left_y' => $topLeftY,
                    'bottom_right_x' => $bottomRightX,
                    'bottom_right_y' => $bottomRightY,
                ],
            ]);

            // Obtener la respuesta de la API
            $result = $response->toArray(); // Convertir a array
        }

        return $this->render('imagen/index.html.twig', [
            'controller_name' => 'ImagenController',
            'result' => $result, // Pasar los resultados a la vista
        ]);
    }
}
