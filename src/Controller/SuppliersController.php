<?php

namespace App\Controller;

use App\Entity\Suppliers;
use App\Form\SuppliersType;
use App\Repository\SuppliersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/suppliers')]
class SuppliersController extends AbstractController
{
    #[Route('/', name: 'app_suppliers_index', methods: ['GET'])]
    public function index(SuppliersRepository $suppliersRepository ): Response
    {
         
        return $this->render('suppliers/index.html.twig', [
            'suppliers' => $suppliersRepository->findAll(),
        ]);
        
    }
    #[Route('/suppliers_mobile', name: 'app_suppliers_mobile_all', methods: ['GET'])]
    public function suppliers_mobile_all(SuppliersRepository $suppliersRepository , NormalizerInterface $Normalizer ): Response
    {
        $suppliers = $suppliersRepository->findAll();
        
        $jsonContent = $Normalizer->normalize($suppliers , 'json', ['groups' => 'suppliers']);

        return new Response(json_encode($jsonContent));
      
    }
    #[Route('/delete_suppliers_mobile', name: 'app_delete_suppliers_mobile', methods: ['GET'])]
    public function delete_suppliers_mobile(Request $request,SuppliersRepository $suppliersRepository , NormalizerInterface $Normalizer ): Response
    {
        $supplier = $suppliersRepository->find((int)$request->get("id"));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($supplier);
        $entityManager->flush();
        $suppliers = $suppliersRepository->findAll();
        $jsonContent = $Normalizer->normalize($suppliers , 'json', ['groups' => 'suppliers']);

        return new Response(json_encode($jsonContent));
      
    }
    #[Route('/add_suppliers_mobile', name: 'app_add_suppliers_mobile', methods: ['GET'])]
    public function add_suppliers_mobile(Request $request,SuppliersRepository $suppliersRepository , NormalizerInterface $Normalizer ): Response
    {
        $supplier = new Suppliers();
        $supplier->setName($request->get("name"));
        $supplier->setAdress($request->get("adress"));
        $supplier->setPhone($request->get("phone"));
        $supplier->setEmail($request->get("email"));
        $supplier->setNotes($request->get("notes"));
        $entityManager = $this->getDoctrine()->getManager();
        $suppliersRepository->save($supplier, true);
        $entityManager->flush();
        $suppliers = $suppliersRepository->findAll();
        $jsonContent = $Normalizer->normalize($suppliers , 'json', ['groups' => 'suppliers']);

        return new Response(json_encode($jsonContent));
      
    }
    #[Route('/update_suppliers_mobile', name: 'app_update_suppliers_mobile', methods: ['GET'])]
    public function update_suppliers_mobile(Request $request,SuppliersRepository $suppliersRepository , NormalizerInterface $Normalizer ): Response
    {
        $supplier = $suppliersRepository->find((int)$request->get("id"));
        $entityManager = $this->getDoctrine()->getManager();
        $supplier->setName($request->get("name"));
        $supplier->setAdress($request->get("adress"));
        $supplier->setPhone($request->get("phone"));
        $supplier->setEmail($request->get("email"));
        $supplier->setNotes($request->get("notes"));
        $entityManager = $this->getDoctrine()->getManager();
        $suppliersRepository->save($supplier, true);

        $suppliers = $suppliersRepository->findAll();
        $jsonContent = $Normalizer->normalize($suppliers , 'json', ['groups' => 'suppliers']);

        return new Response(json_encode($jsonContent));
      
    }
    #[Route('/new', name: 'app_suppliers_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SuppliersRepository $suppliersRepository): Response
    {
        $supplier = new Suppliers();
        $form = $this->createForm(SuppliersType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $suppliersRepository->save($supplier, true);

            return $this->redirectToRoute('app_suppliers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suppliers/new.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suppliers_show', methods: ['GET'])]
    public function show(Suppliers $supplier): Response
    {
        return $this->render('suppliers/show.html.twig', [
            'supplier' => $supplier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_suppliers_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Suppliers $supplier, SuppliersRepository $suppliersRepository): Response
    {
        $form = $this->createForm(SuppliersType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $suppliersRepository->save($supplier, true);

            return $this->redirectToRoute('app_suppliers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suppliers/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suppliers_delete', methods: ['POST'])]
    public function delete(Request $request, Suppliers $supplier, SuppliersRepository $suppliersRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$supplier->getId(), $request->request->get('_token'))) {
            $suppliersRepository->remove($supplier, true);
        }

        return $this->redirectToRoute('app_suppliers_index', [], Response::HTTP_SEE_OTHER);
    }
}
