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
