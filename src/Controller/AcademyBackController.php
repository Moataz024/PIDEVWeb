<?php

namespace App\Controller;

use App\Entity\AcademyBack;
use App\Form\AcademyBack1Type;
use App\Repository\AcademyBackRepository;
use App\Repository\AcademyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/academy/back')]
class AcademyBackController extends AbstractController
{
    #[Route('/', name: 'app_academy_back_index', methods: ['GET'])]
    public function index(AcademyBackRepository $academyBackRepository,AcademyRepository $acrepo): Response
    {
        return $this->render('academy_back/index.html.twig', [
            'academy_backs' => $academyBackRepository->findAll(),
            'academy_fronts' => $acrepo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_academy_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AcademyBackRepository $academyBackRepository): Response
    {
        $academyBack = new AcademyBack();
        $form = $this->createForm(AcademyBack1Type::class, $academyBack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $academyBackRepository->save($academyBack, true);

            return $this->redirectToRoute('app_academy_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('academy_back/new.html.twig', [
            'academy_back' => $academyBack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academy_back_show', methods: ['GET'])]
    public function show(AcademyBack $academyBack): Response
    {
        return $this->render('academy_back/show.html.twig', [
            'academy_back' => $academyBack,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_academy_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AcademyBack $academyBack, AcademyBackRepository $academyBackRepository): Response
    {
        $form = $this->createForm(AcademyBack1Type::class, $academyBack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $academyBackRepository->save($academyBack, true);

            return $this->redirectToRoute('app_academy_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('academy_back/edit.html.twig', [
            'academy_back' => $academyBack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academy_back_delete', methods: ['POST'])]
    public function delete(Request $request, AcademyBack $academyBack, AcademyBackRepository $academyBackRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$academyBack->getId(), $request->request->get('_token'))) {
            $academyBackRepository->remove($academyBack, true);
        }

        return $this->redirectToRoute('app_academy_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
