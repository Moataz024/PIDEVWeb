<?php

namespace App\Controller;

use App\Entity\CoachBack;
use App\Form\CoachBack1Type;
use App\Repository\CoachBackRepository;
use App\Repository\CoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/coach/back')]
class CoachBackController extends AbstractController
{
    #[Route('/', name: 'app_coach_back_index', methods: ['GET'])]
    public function index(CoachBackRepository $coachBackRepository,CoachRepository $coachrepo): Response
    {
        return $this->render('coach_back/index.html.twig', [
            'coach_backs' => $coachBackRepository->findAll(),
            'coach_fronts' => $coachrepo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_coach_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CoachBackRepository $coachBackRepository): Response
    {
        $coachBack = new CoachBack();
        $form = $this->createForm(CoachBack1Type::class, $coachBack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coachBackRepository->save($coachBack, true);

            return $this->redirectToRoute('app_coach_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('coach_back/new.html.twig', [
            'coach_back' => $coachBack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_coach_back_show', methods: ['GET'])]
    public function show(CoachBack $coachBack): Response
    {
        return $this->render('coach_back/show.html.twig', [
            'coach_back' => $coachBack,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_coach_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CoachBack $coachBack, CoachBackRepository $coachBackRepository): Response
    {
        $form = $this->createForm(CoachBack1Type::class, $coachBack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coachBackRepository->save($coachBack, true);

            return $this->redirectToRoute('app_coach_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('coach_back/edit.html.twig', [
            'coach_back' => $coachBack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_coach_back_delete', methods: ['POST'])]
    public function delete(Request $request, CoachBack $coachBack, CoachBackRepository $coachBackRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$coachBack->getId(), $request->request->get('_token'))) {
            $coachBackRepository->remove($coachBack, true);
        }

        return $this->redirectToRoute('app_coach_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
