<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\CardItem;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;


#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/show.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/myproducts', name: 'app_produit', methods: ['GET'])]
    public function indexown(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
public function new(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
{
    $produit = new Produit();
    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($produit);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    $errors = $validator->validate($produit);
    
    return $this->render('produit/new.html.twig', [
        'produit' => $produit,
        'form' => $form->createView(),
        'errors'=> $errors,
    ]);
}

  
    
    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/product_details.html.twig', [
            'produit' => $produit,
        ]);
    }

    

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitRepository->add($produit);
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

   

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produitRepository->remove($produit);
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/add-to-cart/{id}', name: 'app_cart_add')]
    public function addToCart(Request $request, int $id , EntityManagerInterface $entityManager): Response
    {
        // get the current user
        $user = $this->getUser();

        // get the product you want to add to the cart
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);

        // check if the produit exists
        if (!$produit) {
            throw $this->createNotFoundException('Produit not found');
        }

        // check if the user has a cart
        $cart = $user->getCard();
        if (!$cart) {
            $cart = new Card();
            $cart->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        // check if the produit already exists in the cart
        $cartItem = $cart->getCartItemByProduit($produit);
        if ($cartItem) {
            // increment the quantity of the existing cart item
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            // create a new cart item
            $cartItem = new CardItem();
            $cartItem->setCard($cart);
            $cartItem->setProduit($produit);
            $cartItem->setQuantity(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cartItem);
        }

        // save the changes to the cart
        $entityManager->flush();

        // redirect to the cart page
        return       $this->redirectToRoute('app_produit_index');

    }
    // #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
    // public function addToCart2(Request $request, Produit $produit, Cart $cart): Response
    // {
    //     $quantity = $request->request->get('quantity', 1);

    //     $cartItem = new CartItem();
    //     $cartItem->setProduit($produit);
    //     $cartItem->setQuantity($quantity);

    //      $cart->addItem($cartItem);

    //     // Update the cart in the database
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $entityManager->persist($cart);
    //     $entityManager->flush();

    //     // Redirect the user to the cart page
    //     return $this->redirectToRoute('cart');
    // }

       
}


