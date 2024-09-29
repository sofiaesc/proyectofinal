<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use App\Form\TestType;

class BaseController extends AbstractController
{
    
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
        ]);
    }
    
<<<<<<< HEAD
    #[Route('/image_upload', name: 'app_image_upload')]
    public function image_upload(): Response
    {
        return $this->render('front/image_upload.html.twig', [
        ]);
    }


    #[Route('/test_list', name: 'app_test_list')]
    public function test_list(): Response
    {

        return $this->render('front/test/test_list.html.twig', [
        ]);
    }



    #[Route('/procesar_imagen', name: 'app_procesar_imagen', methods: ['POST'])]
    public function procesarImagen(Request $request): Response
=======
    #[Route('/image_upload', name: 'app_image_upload', methods: ['GET', 'POST'])]
    public function image_upload(Request $request): Response
>>>>>>> 9de0472b7d36d6b320d2d7bfda54550c3dee1ab9
    {
        $form = $this->createForm(TestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photo')->getData();

            if ($file) {
                // Procesar la imagen (ej. mover a un directorio)
                $filePath = '/tmp/' . $file->getClientOriginalName();
                $file->move('/tmp/', $file->getClientOriginalName());

                // Aquí puedes agregar la lógica para enviar la imagen a la aplicación Python

                return new Response('Imagen cargada con éxito.');
            }
        }

        return $this->render('front/image_upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/test_list', name: 'app_test_list')]
    public function test_list(): Response
    {

        return $this->render('front/test/test_list.html.twig', [
        ]);
    }


    #[Route('/pocillos_validos', name: 'app_pocillos_validos')]
    public function pocillos_validos(): Response
    {
        return $this->render('pocillos_validos.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 9de0472b7d36d6b320d2d7bfda54550c3dee1ab9
