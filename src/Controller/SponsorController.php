<?php

namespace App\Controller;

use App\Entity\SponsorE;
use App\Form\SponsorEType;
use App\Repository\SponsorERepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/sponsor')]
class SponsorController extends AbstractController
{

    #[Route('/', name: 'app_sponsor_index', methods: ['GET'])]
    public function index(SponsorERepository $sponsorERepository): Response
    {
        return $this->render('sponsor/index.html.twig', [
            'sponsor_es' => $sponsorERepository->findAll(),
        ]);
    }



    #[Route('/new', name: 'app_sponsor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SponsorERepository $sponsorERepository,ValidatorInterface $validator): Response
    {

        $sponsorE = new SponsorE();
        $form = $this->createForm(SponsorEType::class, $sponsorE);
        $form->handleRequest($request);
        $errors = null;
        if ($form->isSubmitted()) {
            if ($form->isValid()){
                $sponsorERepository->save($sponsorE, true);
                return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
            }else {
                $errors = $validator->validate($sponsorE);
            }
        }
        return $this->renderForm('sponsor/new.html.twig', [
            'sponsor_e' => $sponsorE,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_sponsor_show', methods: ['GET'])]
    public function show(SponsorE $sponsorE): Response
    {
        return $this->render('sponsor/show.html.twig', [
            'sponsor_e' => $sponsorE,
        ]);
    }




    #[Route('/{id}/edit', name: 'app_sponsor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SponsorE $sponsorE, SponsorERepository $sponsorERepository): Response
    {
        $form = $this->createForm(SponsorEType::class, $sponsorE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sponsorERepository->save($sponsorE, true);

            return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sponsor/edit.html.twig', [
            'sponsor_e' => $sponsorE,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sponsor_delete', methods: ['POST'])]
    public function delete(Request $request, SponsorE $sponsorE, SponsorERepository $sponsorERepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sponsorE->getId(), $request->request->get('_token'))) {
            $sponsorERepository->remove($sponsorE, true);
        }

        return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
    }
}
