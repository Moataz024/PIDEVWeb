<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorieback')]
class CategoriebackController extends AbstractController
{
//  #[Route('/categorieback', name: 'app_categorieback')]
//     public function index(): Response
//     {
//         return $this->render('categorieback/index.html.twig', [
//             'controller_name' => 'CategoriebackController',
//         ]);
//     }
    #[Route('/show', name: 'app_categorie_back', methods: ['GET'])]
    function indeback(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorieback/showback.html.twig', [
            'categories' => $categorieRepository->findAll(),
            ]);
    }

    #[Route('/newback', name: 'app_categorieback_new', methods: ['GET', 'POST'])]
    public function newb(Request $request, CategorieRepository $categorieRepository): Response
    {
     $categorie = new Categorie();
     $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $categorieRepository->save($categorie, true);

        return $this->redirectToRoute('app_categorie_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorieback/newback.html.twig', [
        'categorie' => $categorie,
        'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorieback_edit', methods: ['GET', 'POST'])]
    public function editb(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('app_categorie_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/editback.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_categorieback_show', methods: ['GET'])]
    public function showb(Categorie $categorie): Response
    {
        return $this->render('categorieback/showdetback.html.twig', [
            'categorie' => $categorie,
        ]);
    }
    #[Route('/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $categorieRepository->remove($categorie, true);
        }

        return $this->redirectToRoute('app_categorie_back', [], Response::HTTP_SEE_OTHER);
    }
}
