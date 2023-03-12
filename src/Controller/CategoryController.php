<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
    #[Route('/categorys_mobile', name: 'app_categorys_mobile_all', methods: ['GET'])]
    public function categorys_mobile_all(CategoryRepository $categoryRepository , NormalizerInterface $Normalizer ): Response
    {
        $categorys = $categoryRepository->findAll();
        
        $jsonContent = $Normalizer->normalize($categorys , 'json', ['groups' => 'category']);

        return new Response(json_encode($jsonContent));
      
    }
    #[Route('/delete_categorys_mobile', name: 'app_delete_categorys_mobile', methods: ['GET'])]
    public function deletecategorys_mobile(Request $request,CategoryRepository $categoryRepository , NormalizerInterface $Normalizer ): Response
    {
        $category = $categoryRepository->find((int)$request->get("id"));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        $categorys = $categoryRepository->findAll();
        
        $jsonContent = $Normalizer->normalize($categorys , 'json', ['groups' => 'category']);

        return new Response(json_encode($jsonContent));
      
    }
    #[Route('/add_categorys_mobile', name: 'app_add_categorys_mobile', methods: ['GET'])]
    public function add_categorys_mobile(Request $request,CategoryRepository $categoryRepository , NormalizerInterface $Normalizer ): Response
    {
        $category = new Category();
        $category->setNom($request->get("name"));
/*        $category->setImageC($request->get("name"));*/
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository->save($category, true);
        $entityManager->flush();
        $categorys = $categoryRepository->findAll();
        
        $jsonContent = $Normalizer->normalize($categorys , 'json', ['groups' => 'category']);

        return new Response(json_encode($jsonContent));
      
    }
    #[Route('/update_categorys_mobile', name: 'app_update_categorys_mobile', methods: ['GET'])]
    public function update_categorys_mobile(Request $request,CategoryRepository $categoryRepository , NormalizerInterface $Normalizer ): Response
    {
        $category = $categoryRepository->find((int)$request->get("id"));
        $entityManager = $this->getDoctrine()->getManager();
        $category->setNom($request->get("name"));
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository->save($category, true);
        $categorys = $categoryRepository->findAll();
        $jsonContent = $Normalizer->normalize($categorys , 'json', ['groups' => 'category']);
        return new Response(json_encode($jsonContent));
      
    }

    #[Route('/front', name: 'app_category_index_front', methods: ['GET'])]
    public function indexF(CategoryRepository $categoryRepository): Response
    {
        return $this->render('front/category.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}