<?php

namespace App\Controller;

use App\Entity\Rami;
use App\Form\RamiType;
use App\Repository\RamiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rami')]
class RamiController extends AbstractController
{
    #[Route('/', name: 'app_rami_index', methods: ['GET'])]
    public function index(RamiRepository $ramiRepository): Response
    {
        return $this->render('rami/index.html.twig', [
            'ramis' => $ramiRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rami_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RamiRepository $ramiRepository): Response
    {
        $rami = new Rami();
        $form = $this->createForm(RamiType::class, $rami);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ramiRepository->save($rami, true);

            return $this->redirectToRoute('app_rami_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rami/new.html.twig', [
            'rami' => $rami,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rami_show', methods: ['GET'])]
    public function show(Rami $rami): Response
    {
        return $this->render('rami/show.html.twig', [
            'rami' => $rami,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rami_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rami $rami, RamiRepository $ramiRepository): Response
    {
        $form = $this->createForm(RamiType::class, $rami);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ramiRepository->save($rami, true);

            return $this->redirectToRoute('app_rami_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rami/edit.html.twig', [
            'rami' => $rami,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rami_delete', methods: ['POST'])]
    public function delete(Request $request, Rami $rami, RamiRepository $ramiRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rami->getId(), $request->request->get('_token'))) {
            $ramiRepository->remove($rami, true);
        }

        return $this->redirectToRoute('app_rami_index', [], Response::HTTP_SEE_OTHER);
    }
}
