<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

class BaseController extends AbstractController
{
    #[Route('/carga_imagen', name: 'app_carga_imagen')]
    public function carga_imagen(): Response
    {
        return $this->render('carga_imagen.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }

    #[Route('/procesar_imagen', name: 'app_procesar_imagen', methods: ['POST'])]
    public function procesarImagen(Request $request): Response
    {
        // Obtener el archivo de la solicitud
        $file = $request->files->get('photo');

        if ($file) {
            // Mover la foto a un directorio temporal
            $filePath = '/tmp/' . $file->getClientOriginalName();
            $file->move('/tmp/', $file->getClientOriginalName());

            // Enviar la imagen a la aplicación Python
            $client = new Client();
            $response = $client->request('POST', 'http://localhost:5000/procesar-foto', [
                'multipart' => [
                    [
                        'name' => 'photo',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => $file->getClientOriginalName(),
                    ]
                ]
            ]);
            
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(), true); // Asegúrate de decodificarlo como array asociativo
                $intensities = $data['intensities'];
            
                return $this->render('resultados.html.twig', [
                    'intensities' => $intensities  // Pasar la lista correctamente a Twig
                ]);
            } else {
                return new Response('Error al procesar la imagen en la aplicación Python', 500);
            }
        }

        return new Response('No se ha cargado ninguna imagen', 400);
    }


    #[Route('/pocillos_validos', name: 'app_pocillos_validos')]
    public function pocillos_validos(): Response
    {
        return $this->render('pocillos_validos.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }

    #[Route('/index', name: 'app_base')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }
}
