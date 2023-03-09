<?php

namespace App\Controller;

use App\Entity\Academy;
use App\Form\AcademyType;
use App\Repository\AcademyRepository;
use App\Repository\CoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AcademyAPIController extends AbstractController
{
    /**
     * @Route("/academy/api/list", name="list", methods={"GET"})
     */
    public function index(AcademyRepository $acrepo, SerializerInterface $serializer): Response
    {
        $academies = $acrepo->findAll();
        $jsonContent = $serializer->serialize($academies, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/academy/api/add", name="new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $academy = new Academy();
        $academy->setName($request->request->get('name'));
        $academy->setCategory($request->request->get('category'));

        $entityManager->persist($academy);
        $entityManager->flush();

        return new Response('Academy added');
    }

    /**
     * @Route("/academy/api/edit/{id}", name="edit", methods={"PUT"})
     */
    public function edit(Request $request, AcademyRepository $acrepo, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $academy = $acrepo->find($id);

        if (!$academy) {
            throw $this->createNotFoundException('Academy not found');
        }

        $academy->setName($request->request->get('name'));
        $academy->setCategory($request->request->get('category'));

        $entityManager->flush();

        return new Response('Academy updated');
    }

    /**
     * @Route("/academy/api/delete/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, AcademyRepository $acrepo, SerializerInterface $serializer, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $academy = $acrepo->find($id);

        if (!$academy) {
            throw $this->createNotFoundException('Academy not found');
        }

        $entityManager->remove($academy);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($academy, 'json');

        return new Response($jsonContent, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }
}
