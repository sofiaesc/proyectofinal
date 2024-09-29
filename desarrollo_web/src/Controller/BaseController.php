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

class BaseController extends AbstractController
{
    
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
        ]);
    }
    
    #[Route('/image_upload', name: 'app_image_upload', methods: ['GET', 'POST'])]
public function image_upload(Request $request): Response
{
    $test = new Test();
    $form = $this->createForm(TestType::class, $test);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Procesar la imagen
        $foto = $form->get('foto')->getData();

        // Obtener las coordenadas directamente del formulario
        $x1 = $form->get('x1')->getData();
        $y1 = $form->get('y1')->getData();
        $x2 = $form->get('x2')->getData();
        $y2 = $form->get('y2')->getData();

        // Puedes hacer lo que necesites con la imagen y las coordenadas aquí

        // En lugar de redirigir, loguear las variables
        dump($foto, $x1, $y1, $x2, $y2); // Muestra en la consola
        die(); // Detiene la ejecución para que veas los resultados

        // O, si prefieres seguir adelante después de ver los datos
        // return $this->redirectToRoute('success_route');
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
}
