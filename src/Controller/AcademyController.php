<?php

namespace App\Controller;

use App\Entity\Academy;
use App\Form\AcademyType;
use App\Repository\AcademyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/academy')]
class AcademyController extends AbstractController
{
    #[Route('/', name: 'app_academy_index', methods: ['GET'])]
    public function index(AcademyRepository $academyRepository): Response
    {
        return $this->render('academy/index.html.twig', [
            'academies' => $academyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_academy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AcademyRepository $academyRepository): Response
    {
        $academy = new Academy();
        $form = $this->createForm(AcademyType::class, $academy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $academyRepository->save($academy, true);

            return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('academy/new.html.twig', [
            'academy' => $academy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academy_show', methods: ['GET'])]
    public function show(Academy $academy): Response
    {
        return $this->render('academy/show.html.twig', [
            'academy' => $academy,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_academy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Academy $academy, AcademyRepository $academyRepository): Response
    {
        $form = $this->createForm(AcademyType::class, $academy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $academyRepository->save($academy, true);

            return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('academy/edit.html.twig', [
            'academy' => $academy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academy_delete', methods: ['POST'])]
    public function delete(Request $request, Academy $academy, AcademyRepository $academyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$academy->getId(), $request->request->get('_token'))) {
            $academyRepository->remove($academy, true);
        }

        return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
    }
}
