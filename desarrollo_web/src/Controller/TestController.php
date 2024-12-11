<?php

// src/Controller/TestController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Test;


class TestController extends AbstractController
{

    #[Route('/test_list', name: 'app_test_list')]
    public function test_list(Request $request, EntityManagerInterface $entityManager): Response
    {
        $usuario = $this->getUser()->getId();  // Obtén el ID del usuario actual
        // Obtén el término de búsqueda desde la consulta de la URL
        $searchTerm = $request->query->get('search', '');
    
        // Creamos la consulta base
        $queryBuilder = $entityManager->getRepository(Test::class)->createQueryBuilder('t')
            ->orderBy('t.fechaHora', 'DESC')
            // Filtramos por el usuario relacionado (usamos 'usuario' para la relación)
            ->andWhere('t.usuario = :usuario')  // Relación con la entidad Usuario
            ->setParameter('usuario', $usuario);  // Establecemos el valor del parámetro
    
        // Si hay un término de búsqueda, filtramos por nombre_alt
        if ($searchTerm) {
            $queryBuilder->andWhere('t.nombre_alt LIKE :searchTerm')
                         ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    
        // Obtenemos la consulta final
        $query = $queryBuilder->getQuery();
    
        // Ejecutamos la consulta para obtener los resultados completos
        $items = $query->getResult();
    
        // Renderizamos la plantilla y pasamos los elementos encontrados
        return $this->render('/front/test/test_list.html.twig', [
            'items' => $items,
            'search' => $searchTerm, // Pasamos el término de búsqueda para mantenerlo en el formulario
        ]);
    }


    #[Route('test_show/{id}', name: 'app_test_show')]
    public function test_show(int $id, EntityManagerInterface $entityManager): Response
    {
        // Obtén el ID del usuario autenticado
        $usuarioId = $this->getUser()->getId();
    
        // Buscar el test por su ID
        $item = $entityManager->getRepository(Test::class)->find($id);
    
        // Si no se encuentra el test, lanzamos un error 404
        if (!$item) {
            throw $this->createNotFoundException('Test no encontrado');
        }
    
        // Verificar si el test le pertenece al usuario autenticado
        if ($item->getUsuario()->getId() !== $usuarioId) {
            // Si no es el mismo usuario, redirigir a la página de inicio (app_index)
            return $this->redirectToRoute('app_test_list');
        }
    
        // Renderizamos la plantilla con el test encontrado
        return $this->render('/front/test/test_show.html.twig', [
            'item' => $item,
        ]);
    }
    

    #[Route('"/test/{id}/edit-name', name: 'app_test_edit_name')]
    public function editName(int $id, Request $request): JsonResponse
    {
        // Buscar el test por su ID
        $test = $this->getDoctrine()->getRepository(Test::class)->find($id);

        if (!$test) {
            return new JsonResponse(['status' => 'error', 'message' => 'Test no encontrado'], 404);
        }

        // Obtener el nuevo nombre desde la solicitud AJAX
        $newName = $request->request->get('nombreAlt');

        // Actualizar el nombre
        $test->setNombreAlt($newName);

        // Guardar los cambios
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Nombre actualizado correctamente']);
    }


    #[Route('/generar_pdf/{id}', name: 'app_generar_pdf')]
    public function generarPdf(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        // Buscar el test en la base de datos
        $test = $entityManager->getRepository(Test::class)->find($id);
    
        if (!$test) {
            throw $this->createNotFoundException('El test no existe.');
        }
    
        $rutaResultado = $test->getRutaImagen();
        $logoUrl = '/images/logo.png';
    
        ...
    }

}
