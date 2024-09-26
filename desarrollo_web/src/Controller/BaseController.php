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
        return $this->render('base/carga_imagen.html.twig', [
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
            
                return $this->render('base/resultados.html.twig', [
                    'intensities' => $intensities  // Pasar la lista correctamente a Twig
                ]);
            } else {
                return new Response('Error al procesar la imagen en la aplicación Python', 500);
            }
        }

        return new Response('No se ha cargado ninguna imagen', 400);
    }


    #[Route('/base', name: 'app_base')]
    public function index(): Response
    {
        return $this->render('base/index.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }
}
