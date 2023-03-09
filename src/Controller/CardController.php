<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Controller\SecurityController;
use App\Repository\CardRepository;
use App\Entity\CardItem;
use App\Repository\CardItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Entity\Commande;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



#[Route('/card')]
class CardController extends AbstractController
{
    #[Route('/', name: 'app_card_index', methods: ['GET'])]
    public function index(CardRepository $cardRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'cards' => $cardRepository->findAll(),
        ]);
    }

    #[Route('/cart/user/{userId}', name: 'cart_by_user')]
    #[ParamConverter('user', class: 'App\Entity\User', options: ['id' => 'userId'])]
    public function showByUser(User $user, CardRepository $cartRepository): Response
    {
        $cart = $cartRepository->findCartByUser($user->getId());
        
        if (!$cart) {
            throw $this->createNotFoundException('Cart not found.');
        }

        return $this->render('cart/showcard.html.twig', [
            'cart' => $cart,

        ]);
    }


    #[Route('/new', name: 'app_card_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CardRepository $cardRepository): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cardRepository->save($card, true);

            return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card/new.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_card_show', methods: ['GET'])]
    public function show(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_card_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Card $card, CardRepository $cardRepository): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cardRepository->save($card, true);

            return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card/edit.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_card_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Card $card, CardRepository $cardRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $cardRepository->remove($card, true);
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    // #[Route('/item/{itemId}/delete', name: 'cart_item_delete1', methods: ['POST'])]
    // public function deleteCartItem(int $itemId, EntityManagerInterface $entityManager): Response
    // {
    //     $item = $entityManager->getRepository(CardItem::class)->find($itemId);

    //     if (!$item) {
    //         throw $this->createNotFoundException('Item not found.');
    //     }

    //     $entityManager->remove($item);
    //     $entityManager->flush();

    //     $this->addFlash('success', 'Item deleted successfully.');

    //     return $this->redirectToRoute('cart_by_user', ['userId' => $userId]);
    // }

    #[Route('/item/{itemId}/delete', name: 'cart_item_delete1', methods: ['POST'])]
    public function deleteCartItem(int $itemId, EntityManagerInterface $entityManager, Request $request): Response
    {
        $item = $entityManager->getRepository(CardItem::class)->find($itemId);

        if (!$item) {
            throw $this->createNotFoundException('Item not found.');
        }

        $cart = $item->getCard();
        $cart->removeCardItem($item);
        $entityManager->persist($cart);
        $entityManager->flush();
        $this->addFlash('success', 'Item deleted successfully.');

        $userId = $request->request->get('userId');
        return $this->redirectToRoute('app_produit_index');
    }

    #[Route('/commande/success', name: 'commande_success')]
    public function commandeSuccess(): Response
    {
    return $this->render('commande/success.html.twig');
    }



}