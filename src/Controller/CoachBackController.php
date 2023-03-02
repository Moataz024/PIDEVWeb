<?php

namespace App\Controller;

use App\Entity\Academy;
use App\Entity\Coach;
use App\Form\CoachType;
use App\Repository\CoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/coach/back')]
class CoachBackController extends AbstractController
{
    #[Route('/', name: 'app_coach_back_index', methods: ['GET'])]
    public function index(CoachRepository $coachrepo): Response
    {
        return $this->render('coach_back/index.html.twig', [
            'coach_backs' => $coachrepo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_coach_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CoachRepository $coachBackRepository): Response
    {
        $coachBack = new Coach();
        $form = $this->createForm(CoachType::class, $coachBack);
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

    #[Route('/{id}/show', name: 'app_coach_back_show', methods: ['GET','POST'])]
    public function show(Coach $coachBack): Response
    {
        return $this->render('coach_back/show.html.twig', [
            'coach_back' => $coachBack,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_coach_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Coach $coachBack, CoachRepository $coachBackRepository): Response
    {
        $form = $this->createForm(CoachType::class, $coachBack);
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
    public function delete(Request $request, Coach $coachBack, CoachRepository $coachBackRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$coachBack->getId(), $request->request->get('_token'))) {
            $coachBackRepository->remove($coachBack, true);
        }

        return $this->redirectToRoute('app_coach_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
