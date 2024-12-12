<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    #[Route('/', name: 'index_redirect')]
    public function redirectToIndex(): Response
    {
        return $this->render('front/index.html.twig', [
        ]);
    }

    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('front/contact.html.twig', [
        ]);
    }
}
