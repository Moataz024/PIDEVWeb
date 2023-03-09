<?php

namespace App\Controller;

use App\Entity\CardItem;
use App\Form\CardItemType;
use App\Repository\CardItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;

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
    #[Route('/items/{id}/update-quantity', name:'update_quantity')]
    public function updateQuantity(Request $request, $id): Response
    {
        // Retrieve the item from the database using $id
        $item = $this->getDoctrine()->getRepository(CardItem::class)->find($id);
        //$idc=$this->getDoctrine()->getRepository(Card::class)->find($id);
        // Get the new quantity from the request
        $newQuantity = $request->request->get('newQuantity');

        // Update the quantity of the item
        $item->setQuantity($newQuantity);

        // Save the changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($item);
        $entityManager->flush();

        // Redirect back to the page displaying the item
        //return $this->redirectToRoute('app_card_show', ['idc' => $idc ]);
        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/add-q/{id}', name: 'app_q_add')]
    public function addToCart(Request $request, CardItem $cartItem , EntityManagerInterface $entityManager): Response
{   $user = $this->getUser();
    // get the current user
    

    // get the product you want to add to the cart
    
    // check if the produit exists
    

    // check if the user has a cart
    $cart = $user->getCard();
    

    // check if the produit already exists in the cart
   // $cartItem = $cart->getCartItemByProduit($produit->getLibelle());
    if ($cartItem) {
        // increment the quantity of the existing cart item
        $cartItem->setQuantity($cartItem->getQuantity() + 1);
    } 

    // save the changes to the cart
    $entityManager->flush();
    $entityManager->persist($cart);

    // redirect to the cart page
    return $this->redirectToRoute('app_produit_index');
}
    #[Route('/{id_card_item}', name: 'app_card_item_delete', methods: ['GET','POST'])]
    public function delete(Request $request,$id_card_item, CardItemRepository $cardItemRepository): Response
    {
        $cardItem= $cardItemRepository->find($id_card_item);
        if ($this->isCsrfTokenValid('delete'.$cardItem->getId(), $request->request->get('_token'))) {
            $cardItemRepository->remove($cardItem, true);
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}