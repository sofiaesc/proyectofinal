<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImagenController extends AbstractController
{
    #[Route('/procesamiento', name: 'app_image_processing')]
    public function image_processing(): Response
    {
        return $this->render('imagen/index.html.twig', [
            'controller_name' => 'ImagenController',
        ]);
    }
}
