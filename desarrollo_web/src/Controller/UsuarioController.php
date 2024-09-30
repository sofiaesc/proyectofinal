<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UsuarioType;
use App\Entity\Usuario;

class UsuarioController extends AbstractController
{
    #[Route('/new_user', name: 'app_new_user')]
    public function new_user(): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        return $this->render('front/user/new_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
