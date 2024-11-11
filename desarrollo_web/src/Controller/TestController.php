<?php

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
    public function test_list(Request $request, PaginatorInterface $paginator, EntityManagerInterface $entityManager): Response
    {
        // Número de elementos por página
        $itemsPerPage = 6;

        // Obtenemos la consulta de los items
        $query = $entityManager->getRepository(Test::class)->createQueryBuilder('t')
            ->orderBy('t.fechaHora', 'DESC') 
            ->getQuery();

        // Aplicamos la paginación
        $page = $request->query->getInt('page', 1);
        $pagination = $paginator->paginate(
            $query,      
            $page,      
            $itemsPerPage 
        );

        // Renderizamos la plantilla y pasamos la paginación como 'items'
        return $this->render('/front/test/test_list.html.twig', [
            'items' => $pagination,
            'current_page' => $page,
            'total_pages' => ceil($pagination->getTotalItemCount() / $itemsPerPage),
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