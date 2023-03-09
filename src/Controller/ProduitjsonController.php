<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Repository\CategorieRepository;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Card;
use App\Entity\CardItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;




#[Route('/produitjson')]
class ProduitjsonController extends AbstractController
{
    #[Route('/sh', name: 'app_produitjson')]
    public function index(ProduitRepository $produitRepository,SerializerInterface $serializer)
    {
        $produits=$produitRepository->findAll();
        $json = $serializer->serialize($produits , 'json', [ 'groups' => "produits" ]);
        return new Response($json);
       
    }
    
    #[Route("/addprodjson",name:"addprodjson")]
    public function addprodjson(Request $req, SerializerInterface $serializer)
{   
    $em = $this->getDoctrine()->getManager();
    $produit = new Produit();

    $categorieId = $req->get('categorie_id');
    $categorie = $em->getRepository(Categorie::class)->find($categorieId);
    $produit->setCategorie($categorie);
    $produit->setLibelle($req->get('libelle'));
    $produit->setPrix($req->get('prix'));
    $produit->setStock($req->get('stock'));
    $produit->setRef($req->get('ref'));
    
    $em->persist($produit);
    $em->flush();
    
    $jsonContent = $serializer->serialize($produit, 'json', ['groups' => 'produits']);
    return new JsonResponse($jsonContent);
}
    
    

      #[Route("/editprodjson/{id}",name:"editprodjson")]
        public function editprodjson(Request $req, NormalizerInterface $normalizer, int $id)
    {
    $em=$this->getDoctrine()->getManager();
    $produit = $em->getRepository(Produit::class)->find(intval($id));

    if (!$produit) {
        throw $this->createNotFoundException('Produit non trouvé pour l\'identifiant '.$id);
    }

    $produit->setLibelle($req->get('libelle'));
    $produit->setPrix($req->get('prix'));
    $produit->setStock($req->get('stock'));
    $produit->setRef($req->get('ref'));
    $produit->setImageName($req->get('imgName'));
    $produit->setImageFile($req->get('imgFile'));

    $categorie_id = $req->get('categorie_id');
    $categorie = $em->getRepository(Categorie::class)->find($categorie_id);

    if (!$categorie) {
        throw $this->createNotFoundException('Catégorie non trouvée pour l\'identifiant '.$categorie_id);
    }

    $produit->setCategorie($categorie);

    $em->flush();

    $jsonContent = $normalizer->normalize($produit,'json',['groups' => 'produits']);
    return new Response(json_encode($jsonContent));
    }  

    #[Route("/{id}", name:"api_delete_produit" )]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): JsonResponse
{
    $entityManager->remove($produit);
    $entityManager->flush();

    return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
}

    #[Route('/add-to-cart/{id}', name: 'app_cart_addjs')]
    public function addToCart(Request $request, int $id, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        // get the current user
        $user = $this->getUser();

        // get the product you want to add to the cart
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
        if ($produit !== null) {
            $libelle = $produit->libelle;
        }

        // check if the produit exists
        if (!$produit) {
            return new JsonResponse(['error' => 'Produit not found'], 404);
        }

        // check if the user has a cart
        $cart = $user->getCard();
        if (!$cart) {
            $cart = new Card();
            $cart->setUser($user);
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        // check if the produit already exists in the cart
        $cartItem = $cart->getCartItemByProduit($produit);
        if ($cartItem) {
            // increment the quantity of the existing cart item
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            // create a new cart item and add it to the existing cart
            $cartItem = new CardItem();
            $cartItem->setCard($cart);
            $cartItem->setProduit($produit);
            $cartItem->setQuantity(1);
            $entityManager->persist($cartItem);
            $cart->addCardItem($cartItem);
        }

        // save the changes to the cart
        $entityManager->flush();

        // serialize the cart to json response
        $data = $serializer->serialize($cart, 'json', ['groups' => 'cart']);

        return new JsonResponse($data, 200, [ "ok" ], true);
    }




}
