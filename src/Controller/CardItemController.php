<?php

namespace App\Controller;

use App\Entity\CardItem;
use App\Form\CardItemType;
use App\Repository\CardItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/card/item')]
class CardItemController extends AbstractController
{
    #[Route('/', name: 'app_card_item_index', methods: ['GET'])]
    public function index(CardItemRepository $cardItemRepository): Response
    {
        return $this->render('card_item/index.html.twig', [
            'card_items' => $cardItemRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_card_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CardItemRepository $cardItemRepository): Response
    {
        $cardItem = new CardItem();
        $form = $this->createForm(CardItemType::class, $cardItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cardItemRepository->save($cardItem, true);

            return $this->redirectToRoute('app_card_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card_item/new.html.twig', [
            'card_item' => $cardItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_card_item_show', methods: ['GET'])]
    public function show(CardItem $cardItem): Response
    {
        return $this->render('card_item/show.html.twig', [
            'card_item' => $cardItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_card_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CardItem $cardItem, CardItemRepository $cardItemRepository): Response
    {
        $form = $this->createForm(CardItemType::class, $cardItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cardItemRepository->save($cardItem, true);

            return $this->redirectToRoute('app_card_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card_item/edit.html.twig', [
            'card_item' => $cardItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_card_item_delete', methods: ['POST'])]
    public function delete(Request $request, CardItem $cardItem, CardItemRepository $cardItemRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cardItem->getId(), $request->request->get('_token'))) {
            $cardItemRepository->remove($cardItem, true);
        }

        return $this->redirectToRoute('app_card_item_index', [], Response::HTTP_SEE_OTHER);
    }
}