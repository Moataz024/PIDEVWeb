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

#[Route('/sponsorBack')]
class SponsorBackController extends AbstractController
{
    #[Route('/sponsor/back', name: 'app_sponsor_back', methods: ['GET', 'POST'])]
    public function index(SponsorERepository $sponsorERepository): Response
    {
        return $this->render('sponsor_back/indexBack.html.twig', [
            'sponsor_es' => $sponsorERepository->findAll(),
        ]);
    }


    #[Route('/newBack', name: 'app_sponsor_newBack', methods: ['GET', 'POST'])]
    public function newBack(Request $request, SponsorERepository $sponsorERepository,ValidatorInterface $validator): Response
    {

        $sponsorE = new SponsorE();
        $form = $this->createForm(SponsorEType::class, $sponsorE);
        $form->handleRequest($request);
        $errors = null;
        if ($form->isSubmitted()) {
            if ($form->isValid()){
                $sponsorERepository->save($sponsorE, true);
                return $this->redirectToRoute('app_sponsor_back', [], Response::HTTP_SEE_OTHER);
            }else {
                $errors = $validator->validate($sponsorE);
            }
        }


        return $this->renderForm('sponsor_back/newBack.html.twig', [
            'sponsor_e' => $sponsorE,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_sponsor_showBack', methods: ['GET'])]
    public function showBack(SponsorE $sponsorE): Response
    {
        return $this->render('sponsor_back/showBack.html.twig', [
            'sponsor_e' => $sponsorE,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_sponsorBack_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SponsorE $sponsorE, SponsorERepository $sponsorERepository): Response
    {
        $form = $this->createForm(SponsorEType::class, $sponsorE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sponsorERepository->save($sponsorE, true);

            return $this->redirectToRoute('app_sponsor_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sponsor_back/editBack.html.twig', [
            'sponsor_e' => $sponsorE,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_sponsorBack_delete', methods: ['POST'])]
    public function delete(Request $request, SponsorE $sponsorE, SponsorERepository $sponsorERepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sponsorE->getId(), $request->request->get('_token'))) {
            $sponsorERepository->remove($sponsorE, true);
        }

        return $this->redirectToRoute('app_sponsor_back', [], Response::HTTP_SEE_OTHER);
    }



}



