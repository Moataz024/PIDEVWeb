<?php

namespace App\Controller;


use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Name;
use App\Entity\Produit;
#[Route('/back')]
class ProduitbackController extends AbstractController

{
    #[Route('/produitback', name: 'app_produitback')]
    public function index(): Response
    {
        return $this->render('produitback/index.html.twig', [
            'controller_name' => 'ProduitbackController',
        ]);
    }

    #[Route('/show', name: 'app_produit_back', methods: ['GET'])]
    public function indeback(ProduitRepository $produitRepository): Response
    {
        return $this->render('produitback/showback.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/newback', name: 'app_produitback_new', methods: ['GET', 'POST'])]
    public function newback(Request $request, ValidatorInterface $validator): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_produit_back', [], Response::HTTP_SEE_OTHER);
        }
    
        $errors = $validator->validate($produit);
        
        return $this->render('produitback/newback.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
            'errors'=> $errors,
        ]);
    }

    #[Route('/{id}', name: 'app_produitback_show', methods: ['GET'])]
        public function showback(Produit $produit):Response
        {
            return $this->render('produitback/product_detailsback.html.twig', [
                'produit' => $produit,
            ]);
        }
    #[Route('/editb/{id}', name: 'app_produitback_edit', methods: ['GET', 'POST'])]
    public function editback(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitRepository->add($produit,true);
            return $this->redirectToRoute('app_produit_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produitback/editback.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_produitback_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produitRepository->remove($produit);
        }

        return $this->redirectToRoute('app_produit_back', [], Response::HTTP_SEE_OTHER);
    }

}
