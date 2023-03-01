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

    #[Route('/{id}', name: 'app_card_delete', methods: ['POST'])]
    public function delete(Request $request, Card $card, CardRepository $cardRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $cardRepository->remove($card, true);
        }

        return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/item/{itemId}/delete', name: 'cart_item_delete1', methods: ['DELETE'])]
    public function deleteCartItem(int $itemId, EntityManagerInterface $entityManager): Response
    {
        $item = $entityManager->getRepository(CardItem::class)->find($Id);

        if (!$item) {
            throw $this->createNotFoundException('Item not found.');
        }

        $entityManager->remove($item);
        $entityManager->flush();

        $this->addFlash('success', 'Item deleted successfully.');

        return $this->redirectToRoute('cart_by_user');
    }

//     #[Route('/checkout', name: 'cart_checkout')]
//         public function checkout(Request $request , CommandeRepository $commanderepository ,EntityManagerInterface $entityManager ): Response
//         {
//              $user = $this->getUser();
//             $card = $user->getCard();
    
//             $commande = new Commande();
//             $form = $this->createForm(CommandeType::class, $commande);
//             $form->handleRequest($request);
    
//             if ($form->isSubmitted() && $form->isValid()) {
//                 $commande->setUser($user);
//                 $commande->setItems($card->getCardItems());
                
//                 $entityManager = $this->getDoctrine()->getManager();
//                 $entityManager->persist($commande);
//                 $entityManager->flush();
//                 // $commanderepository->save($commande,true);
//                 // Clear the user's card
//                 $card->clearItems();
        
//                 // Redirect to the command success page
//                 return $this->redirectToRoute('commande_success');
//             }

//     return $this->render('commande/newc.html.twig', [
//         'form' => $form->createView(),
//     ]);
// }

}