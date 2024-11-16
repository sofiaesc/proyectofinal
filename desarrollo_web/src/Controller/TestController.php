<?php

// src/Controller/TestController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Test;

class TestController extends AbstractController
{
    #[Route('/test_list', name: 'app_test_list')]
    public function test_list(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtén el término de búsqueda desde la consulta de la URL
        $searchTerm = $request->query->get('search', '');

        // Creamos la consulta base
        $queryBuilder = $entityManager->getRepository(Test::class)->createQueryBuilder('t')
            ->orderBy('t.fechaHora', 'DESC');

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
        $item = $entityManager->getRepository(Test::class)->find($id);
        if (!$item) {
            throw $this->createNotFoundException('Test no encontrado');
        }

        return $this->render('/front/test/test_show.html.twig', [
            'item' => $item,
        ]);
    }
}
