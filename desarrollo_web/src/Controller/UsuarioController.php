<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // Cambiado a Annotation
use App\Form\UsuarioType;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UsuarioController extends AbstractController
{
    #[Route('/new_user', name: 'app_new_user')]
    public function new_user(Request $request, 
                             EntityManagerInterface $entityManager, 
                             UserPasswordHasherInterface $passwordHasher): Response    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);

        // Procesar el formulario con la solicitud HTTP
        $form->handleRequest($request);

        // Verificar si el formulario fue enviado y es válido
        if ($form->isSubmitted() && $form->isValid()) {
            // Hashear la contraseña antes de guardarla
            $hashedPassword = $passwordHasher->hashPassword(
                $usuario,
                $usuario->getPassword() // Obtiene la contraseña en texto plano del objeto Usuario
            );
            $usuario->setPassword($hashedPassword); // Establece la contraseña hasheada

            // Guardar la entidad en la base de datos
            $entityManager->persist($usuario);
            $entityManager->flush();

            // Opcionalmente, redirigir a otra página después de guardar
            return $this->redirectToRoute('some_route_name');
        }

        return $this->render('front/user/new_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        // Obtén el error de inicio de sesión, si hay uno
        $error = $authenticationUtils->getLastAuthenticationError();

        // Último nombre de usuario ingresado (puede ser útil para el formulario)
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('front/user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        // Symfony manejará esto automáticamente, no necesitas escribir nada aquí
    }
}
