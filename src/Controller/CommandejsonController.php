<?php

namespace App\Controller;

use App\Entity\CardItem;
use App\Entity\Commande;
use App\Entity\Card;
use Doctrine\Common\Collections\Collection;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;


#[Route('/commandejson')]
class CommandejsonController extends AbstractController
{
    

#[Route('/{id}/{id_user}/checkoutt', name: 'app_checkoutjs', methods: ['GET', 'POST'])]
public function checkout(Request $request, Card $card, CommandeRepository $commandeRepo, UserRepository $userRepo, $id_user): JsonResponse
{

    try {
        $user = $userRepo->find($id_user);
        $total = $card->getTotal();
        $cardItems = $card->getCardItems()->toArray();
    
        $commande = new Commande();
        $commande->setTotal($total);
    
       
        $commande->setCardItems($cardItems);
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setUser($user);
            $commande->setCard($card);
    
            // Persist the new Commande entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();
    
            // Redirect to the confirmation page with the new Commande ID
            return $this->redirectToRoute('commande_success');
        }
    
        $data = [
            'total' => $total,
            'commande' => $commande,
            'form' => $form->createView(),
        ];
    
        return new JsonResponse($data);
    } catch (\InvalidArgumentException $e) {
        // Handle the exception and return an error response
        $errorMessage = 'Error creating JSON response: ' . $e->getMessage();
        $response = new JsonResponse(['error' => $errorMessage], 400);
        return $response;
    }
}
    
    
    
    
    
    
//     $user = $userRepo->find($id_user);
//     $total = $card->getTotal();
//     $cardItems = $card->getCardItems()->toArray();

//     $commande = new Commande();
//     $commande->setTotal($total);

   
//     $commande->setCardItems($cardItems);
//     $form = $this->createForm(CommandeType::class, $commande);
//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {
//         $commande->setUser($user);
//         $commande->setCard($card);

//         // Persist the new Commande entity
//         $entityManager = $this->getDoctrine()->getManager();
//         $entityManager->persist($commande);
//         $entityManager->flush();

//         // Redirect to the confirmation page with the new Commande ID
//         return $this->redirectToRoute('commande_success');
//     }

//     $data = [
//         'total' => $total,
//         'commande' => $commande,
//         'form' => $form->createView(),
//     ];

//     return new JsonResponse($data);
// }

}
 // Create new CardItems from the existing ones and add them to the Commande entity
    // foreach ($cardItems as $cardItem) {
    //     $commandeItem = new CardItem();
    //     $commandeItem->setProduit($cardItem->getProduit());
    //     $commandeItem->setQuantity($cardItem->getQuantity());
    //     $commande->setCardItems($commandeItem);
    // }