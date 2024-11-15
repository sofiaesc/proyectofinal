<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\TestType;
use App\Entity\Test;
use App\Entity\Pocillo;

class ImagenController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/image_upload', name: 'app_image_upload', methods: ['GET', 'POST'])]
    public function image_upload(Request $request, HttpClientInterface $client, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_index'); 
        }
        $user_id = $user->getId();
        $image_id = uniqid();
         
        $test = new Test();
        
        $form = $this->createForm(TestType::class, $test, [
            'allow_extra_fields' => true,
        ]);

        $form->handleRequest($request);

        $respuesta_python = null;

        if ($form->isSubmitted() && $form->isValid()) {
            set_time_limit(60);

            // Obtener datos del formulario
            $foto = $form->get('foto')->getData();
            $x1 = (int) $form->get('x1')->getData();
            $y1 = (int) $form->get('y1')->getData();
            $x2 = (int) $form->get('x2')->getData();
            $y2 = (int) $form->get('y2')->getData();
            $selected_wells = $form->get('pocillos_hab')->getData();
            
            // Enviar la imagen y coordenadas al servidor Flask
            $formData = [
                'top_left_x' => $x1,
                'top_left_y' => $y1,
                'bottom_right_x' => $x2,
                'bottom_right_y' => $y2,
                'image' => fopen($foto->getPathname(), 'r'),
                'selected_wells' => $selected_wells,
                'user_id' => $user_id,
                'image_id' => $image_id,
            ];

            $response = $client->request('POST', 'http://localhost:5000/process', [
                'headers' => ['Content-Type' => 'multipart/form-data'],
                'body' => $formData,
            ]);

            if ($response->getStatusCode() === 200) {
                $respuesta_python = $response->toArray();
                $selected_wells_array = str_split($selected_wells);
                try {
                    // Persistir los datos de intensidad
                    $this->persistIntensityData($respuesta_python['intensities'], $selected_wells_array, $test, $entityManager);

                    // Guardar la ruta de la imagen en la base de datos
                    $rutaImagen = 'test_images/' . $user_id . '/' . $image_id . '.png';
                    $test->setRutaImagen($rutaImagen);
                    $test->setFechaHora(new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires')));
                    $test->setPocillosHab($selected_wells);
                    $test->setUsuario($user);

                    $testRepository = $entityManager->getRepository(Test::class);
                    $testCount = $testRepository->createQueryBuilder('t')
                        ->select('COUNT(t.id)')
                        ->where('t.usuario = :usuario')
                        ->setParameter('usuario', $user_id)
                        ->getQuery()
                        ->getSingleScalarResult();
                    $test->setNombreAlt('TestN°'.($testCount + 1));

                    $entityManager->persist($test);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_test_list');
                } catch (\Exception $e) {
                    return $this->renderWithErrors($respuesta_python, $form, $e->getMessage());
                }
            } else {
                $errorData = $response->toArray();
                return $this->renderWithErrors($respuesta_python, $form, $errorData);
            }
        }

        return $this->render('front/image/image_upload.html.twig', [
            'respuesta_python' => $respuesta_python,
            'form' => $form->createView(),
        ]);
    }

    private function persistIntensityData(array $intensities, array $selected_wells_array, Test $test, EntityManagerInterface $entityManager)
    {
        foreach ($intensities as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $index = $rowIndex * 12 + $colIndex;

                if (isset($selected_wells_array[$index]) && $selected_wells_array[$index] !== '0') {
                    $n_pocillo = new Pocillo();
                    $n_pocillo->setFila($rowIndex);
                    $n_pocillo->setColumna($colIndex);
                    $n_pocillo->setIntensidad($value);
                    $n_pocillo->setTest($test);
                    $test->addPocillo($n_pocillo);
                    $entityManager->persist($n_pocillo);
                }
            }
        }
    }

    private function renderWithErrors($respuesta_python, $form, $error): Response
    {
        return $this->render('front/image/image_upload.html.twig', [
            'respuesta_python' => $respuesta_python,
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
}
