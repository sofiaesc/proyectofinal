<?php

// src/Controller/TestController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Test;
use Dompdf\Dompdf;
use Dompdf\Options;

class TestController extends AbstractController
{

    #[Route('/test_list', name: 'app_test_list')]
    public function test_list(Request $request, EntityManagerInterface $entityManager): Response
    {
        $usuario = $this->getUser()->getId();  
        
        // Se crea la consulta base
        $queryBuilder = $entityManager->getRepository(Test::class)->createQueryBuilder('t')
            ->orderBy('t.fechaHora', 'DESC')
            // Se filtra por el usuario relacionado
            ->andWhere('t.usuario = :usuario')  // Relación con la entidad Usuario
            ->setParameter('usuario', $usuario);  // Establecemos el valor del parámetro
    
        // Si hay un término de búsqueda, se filtra por nombre_alt
        $searchTerm = $request->query->get('search', '');
        if ($searchTerm) {
            $queryBuilder->andWhere('t.nombre_alt LIKE :searchTerm')
                         ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    
        // Se obtiene la query y los resultados de la misma
        $query = $queryBuilder->getQuery();
        $items = $query->getResult();
    
        // Render con los elementos filtrados
        return $this->render('/front/test/test_list.html.twig', [
            'items' => $items,
            'search' => $searchTerm, // Pasamos el término de búsqueda para mantenerlo en el formulario
        ]);
    }


    #[Route('test_show/{id}', name: 'app_test_show')]
    public function test_show(int $id, EntityManagerInterface $entityManager): Response
    {

        $usuarioId = $this->getUser()->getId();
        $test = $entityManager->getRepository(Test::class)->find($id);
    
        // Si no se encuentra el item se lanza un 404
        if (!$test) {
            throw $this->createNotFoundException('Test no encontrado');
        }
    
        // Verificar si el test le pertenece al usuario autenticado
        if ($test->getUsuario()->getId() !== $usuarioId) {
            return $this->redirectToRoute('app_test_list');
        }
    
        // Render de la plantilla
        return $this->render('/front/test/test_show.html.twig', [
            'test' => $test,
        ]);
    }
    

    #[Route('"/test/{id}/edit-name', name: 'app_test_edit_name')]
    public function editName(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $test = $entityManager()->getRepository(Test::class)->find($id);

        // Si no se encuentra el test se lanza un 404
        if (!$test) {
            return new JsonResponse(['status' => 'error', 'message' => 'Test no encontrado'], 404);
        }

        // Obtener el nuevo nombre desde la request
        $newName = $request->request->get('nombreAlt');

        // Actualizar nombre
        $test->setNombreAlt($newName);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Nombre actualizado correctamente']);
    }


    #[Route('/test/{id}/delete', name: 'app_test_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        // Obtener el test desde la base de datos
        $test = $entityManager->getRepository(Test::class)->find($id);
    
        // Si no se encuentra el test, lanzamos una excepción 404
        if (!$test) {
            throw $this->createNotFoundException('Test no encontrado');
        }
    
        // Verificar si el test le pertenece al usuario autenticado
        $usuarioId = $this->getUser()->getId();
        if ($test->getUsuario()->getId() !== $usuarioId) {
            $this->addFlash('error', 'No tienes permiso para eliminar este test.');
            return $this->redirectToRoute('app_test_list');
        }
    
        // Eliminar el test
        $entityManager->remove($test);
        $entityManager->flush();
    
        // Agregar un mensaje flash para confirmar la eliminación
        $this->addFlash('success', 'Test eliminado correctamente.');
    
        // Redirigir al listado de tests
        return $this->redirectToRoute('app_test_list');
    }



    #[Route('/generar_pdf/{id}', name: 'app_generar_pdf')]
    public function generarPdf(int $id, EntityManagerInterface $entityManager): Response
    {
        $test = $entityManager->getRepository(Test::class)->find($id);
    
        // Verificar si el test existe y le pertenece al usuario autenticado
        if (!$test) {
            throw $this->createNotFoundException('El test no existe.');
        }
        $usuarioId = $this->getUser()->getId();
        if ($test->getUsuario()->getId() !== $usuarioId) {
            $this->addFlash('error', 'No tienes permiso para generar el PDF de este test.');
            return $this->redirectToRoute('app_test_list');
        }
    
        // Conseguir las imagenes para el pdf
        $logoBase64 = $this->convertirImagenABase64($this->getParameter('kernel.project_dir') . '/public/images/logo.png');
        $imagenBase64 = $this->convertirImagenABase64($test->getRutaImagen());   
    
        // Configurar Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Open Sans'); // Establecer Lora como la fuente por defecto
        $dompdf = new Dompdf($options);
    
        $referencias = [
            $this->convertirImagenABase64($this->getParameter('kernel.project_dir') . '/public/images/ref_green.png'),
            $this->convertirImagenABase64($this->getParameter('kernel.project_dir') . '/public/images/ref_orange.png'),
            $this->convertirImagenABase64($this->getParameter('kernel.project_dir') . '/public/images/ref_red.png'),
        ];
    
        $css = file_get_contents($this->getParameter('kernel.project_dir') . '/public/css/pdf.css');
        // Cargar y renderizar el contenido del PDF
        $html = $this->renderView('resultado.html.twig', [
            'test' => $test,
            'logoBase64' => $logoBase64,
            'imagenBase64' => $imagenBase64,
            'referencias' => $referencias,
            'css' => $css,
        ]);
    
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // Generar el nombre del archivo basado en test.nombreAlt
        $nombreArchivo = sprintf('%s.pdf', $test->getNombreAlt());
    
        // Enviar el PDF como archivo descargable
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $nombreArchivo),
        ]);

        // EN CASO DE FALLA AUMENTAR EL LIMITE DE MEMORIA EN EL php.ini
    }
    

    private function convertirImagenABase64($rutaImagen) {
        $tipo = pathinfo($rutaImagen, PATHINFO_EXTENSION);
        $datos = file_get_contents($rutaImagen);
        $base64 = 'data:image/' . $tipo . ';base64,' . base64_encode($datos);
        return $base64;
    }

}
