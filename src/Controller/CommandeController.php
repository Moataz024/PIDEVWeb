<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Card;
use App\Entity\CardItem;
use App\Entity\User;
use App\Entity\Historique;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\CardRepository;
use App\Repository\CardItemRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Snappy\Pdf;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/shall', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }
    #[Route('/success/{id}', name: 'commande_success', methods: ['GET','POST'])]
    public function commandeSuccess(Request $request,$id,CommandeRepository $CommandeRep): Response
    {
        $commande = $CommandeRep->find($id);
    return $this->render('commande/success.html.twig',[
        'commande' => $commande,
    ]);
    }
    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommandeRepository $commandeRepository): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/b', name: 'app_commandeb_show', methods: ['GET'])]
    public function showb(Commande $commande): Response
    {
        return $this->render('commande/show_commande.html.twig', [
            'commande' => $commande,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

   
    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $commandeRepository->remove($commande, true);
        }
        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/{id_user}/checkoutt', name: 'app_checkout', methods: ['GET', 'POST'])]
public function checkout(Request $request, Card $card, CardItemRepository $itemRepo, CommandeRepository $commandeRepo, UserRepository $userRepo, $id_user , ValidatorInterface $validator): Response
{
    $user = $userRepo->find($id_user);
    $total = $card->getTotal();
    $cardItems = $card->getCardItems();

    $commande = new Commande();
    $commande->setTotal($total);
   

    // create Historique objects for each CardItem and add them to the Commande
    foreach ($cardItems as $cardItem) {
        $historique = new Historique();
        $historique->setLibelle($cardItem->getLibelle());
        $historique->setPrix($cardItem->getPrix());
        $historique->setQuantity($cardItem->getQuantity());
        $commande->addHistorique($historique);
    }

    $form = $this->createForm(CommandeType::class, $commande);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $commande->setUser($user);
        $commande->setCard($card);

        // Persist the new Commande entity
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commande);
        $entityManager->flush();

        // Remove the CardItems from the CardItemRepository
        foreach ($cardItems as $cardItem) {
            $itemRepo->remove($cardItem, true);
        }

        // Redirect to the confirmation page with the new Commande ID
        return $this->redirectToRoute('commande_success',['id' => $commande->getId()]);
    }
    $errors = $validator->validate($commande);
    return $this->render('commande/checkout.html.twig', [
        'total' => $total,
        'form' => $form->createView(),
        'errors'=> $errors,
    ]);
}

#[Route('/{id}/pdf', name: 'app_commande_pdf', methods: ['GET', 'POST'])]
public function generateReceipt(Commande $commande,Pdf $snappy)
{
    
    // Get the HTML for the receipt
    $html = $this->renderView('commande/receipt.html.twig', [
        'commande' => $commande,
    ]);
    
    // Set options for the PDF
    $snappy->setOption('margin-top', '20mm');
    $snappy->setOption('margin-right', '20mm');
    $snappy->setOption('margin-bottom', '20mm');
    $snappy->setOption('margin-left', '20mm');
    
    
    // Generate the PDF from the HTML
    $pdfData =  $snappy->getOutputFromHtml($html);
    
    // Send the PDF as a response
    return new Response($pdfData, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="receipt.pdf"',
    ]);
}


    // #[Route('/{id}/{id_user}/checkoutt', name: 'app_checkout', methods: ['GET', 'POST'])]
    // public function checkout(Request $request, Card $card, CardItemRepository $itemRepo, CommandeRepository $commandeRepo, UserRepository $userRepo, $id_user): Response
    // {
    //     $user = $userRepo->find($id_user);
    //     $total = $card->getTotal();
    //     $cardItems = $card->getCardItems();
    
    //     // Create a new array of CardItem objects
    //     $newCardItems = [];
    //     foreach ($cardItems as $cardItem) {
    //         $newCardItem = new CardItem();
    //         $newCardItem->setLibelle($cardItem->getLibelle());
    //         $newCardItem->setPrix($cardItem->getPrix());
    //         $newCardItem->setQuantity($cardItem->getQuantity());
    //         // Add the new CardItem object to the new array
    //         $newCardItems[] = $newCardItem;
    //     }
    
    //     $commande = new Commande();
    //     $commande->setTotal($total);
    //     // Pass the new array of CardItem objects to the setCardItems() method
    //     $commande->setCardItems($newCardItems);
    //     $form = $this->createForm(CommandeType::class, $commande);
    //     $form->handleRequest($request);
    
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $commande->setUser($user);
    //         $commande->setCard($card);
    
    //         // Persist the new Commande entity
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($commande);
    //         $entityManager->flush();
    
    //         foreach ($card->getCardItems() as $card_item) {
    //             $itemRepo->remove($card_item, true);
    //         }
    
    //         // Redirect to the confirmation page with the new Commande ID
    //         return $this->redirectToRoute('commande_success');
    //     }
    
    //     return $this->render('commande/checkout.html.twig', [
    //         'total' => $total,
    //         'form' => $form->createView(),
    //     ]);
    // }
    
//     #[Route('/{id}/{id_user}/checkoutt', name: 'app_checkout', methods: ['GET', 'POST'])]
//     public function checkout(Request $request, Card $card,CardItemRepository $itemRepo,CommandeRepository $commandeRepo, UserRepository $userRepo, $id_user): Response
// {
//     $user = $userRepo->find($id_user);
//     $total = $card->getTotal();
//    $cardItems = $card->getCardItems();

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
//         foreach($card->getCardItems() as $card_item){
//             $itemRepo->remove($card_item,true);
//         }
//         // Redirect to the confirmation page with the new Commande ID
//         return $this->redirectToRoute('commande_success');
//     }

//     return $this->render('commande/checkout.html.twig', [
//         'total' => $total,
//         'form' => $form->createView(),
//     ]);
// }
// public function checkout(Request $request, Card $card,CardRepository $cardRepo, CommandeRepository $commandeRepo, UserRepository $userRepo, $id_user): Response
// {
//     $user = $userRepo->find($id_user);
//     $total = $card->getTotal();
//     //$cardItems = $card->getCardItems()->toArray();

//     $commande = new Commande();
//     $commande->setTotal($total);

//     // Create new CardItems from the existing ones
//     // $newCardItems = [];
//     // foreach ($cardItems as $cardItem) {
//     //     $newCardItem = new CardItem();
//     //     $newCardItem->setProduit($cardItem->getProduit());
//     //     $newCardItem->setQuantity($cardItem->getQuantity());
//     //     $newCardItems[] = $newCardItem;
//     // }

//     //$commande->setCardItems($newCardItems);

//     $form = $this->createForm(CommandeType::class, $commande);
//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {
//         $commande->setUser($user);
//         $commande->setCard($card);

//         // Persist the new Commande entity
//         // $entityManager = $this->getDoctrine()->getManager();
//         // $entityManager->persist($commande);
//         // $entityManager->flush();
//          // Detach the card from any associated commandes
//     foreach ($card->getCommands() as $commande) {
//         $commande->setCard(null);
//     }
//         $commandeRepo->save($commande,true);
//         // Remove the CardItems from the Card entity
        
//         $cardRepo->remove($card,true);
        

//         //$entityManager->flush();

//         // Redirect to the confirmation page with the new Commande ID
//         return $this->redirectToRoute('commande_success');
//     }

//     return $this->render('commande/checkout.html.twig', [
//         'total' => $total,
//         'form' => $form->createView(),
//     ]);
// }


//     #[Route('/{id}/{id_user}/checkoutt', name: 'app_checkout', methods: ['GET', 'POST'])]
//     public function checkout(Request $request,Card $card,CommandeRepository $CommandeRepo , UserRepository $userRepo,$id_user): Response
//         {
//             // $card = $this->getUser()->getCard();
//             $user = $userRepo->find($id_user);
//             $total = $card->getTotal();
//             $cardItems=$card->getCardItems()->toArray();;
            
//             // Create new CardItems to avoid removing them from the commande when removing them from the card
//     $newCardItems = [];
//     foreach ($cardItems as $cardItem) {
//         $newCardItem = new CardItem();
//         $newCardItem->setProduct($cardItem->getProduct());
//         $newCardItem->setQuantity($cardItem->getQuantity());
//         $newCardItems[] = $newCardItem;
//     }

//             $commande = new Commande();
//             $commande->setTotal($total);
//             $commande->setCardItems($newCardItems);

//             $form = $this->createForm(CommandeType::class, $commande);
//             $form->handleRequest($request);

//         if ($form->isSubmitted() && $form->isValid()) {
//         // Set the user, card, and date for the new commande entity
//         // $user = $this->getUser();
//         $commande->setUser($user);
//         $commande->setCard($card);
//         // $commande->setCardItems($cardItemss);
//         //$commande->setDate(new \DateTime());

//         // Persist the new commande entity to the database
//         // $entityManager = $this->getDoctrine()->getManager();
//         /*$entityManager->persist($commande);
//         $entityManager->flush();*/
//         // Remove the card items from the card entity
//         $CommandeRepo->save($commande,true);
//         // foreach ($card->getCardItems() as $cardItem) {
            
//         //     $card->removeCardItem($cardItem);
//         //     $entityManager->remove($cardItem);
//         // }
//         // $entityManager->persist($commande);
//         // $entityManager->flush();

//         // Redirect to the confirmation page with the new commande ID
//      return $this->redirectToRoute('commande_success');
//     }
//     // $entityManager = $this->getDoctrine()->getManager();
//     // $entityManager->persist($commande);
//     //  $entityManager->flush();

//     return $this->render('commande/checkout.html.twig', [
//         'total' => $total,
//         'form' => $form->createView(),
//     ]);
// }

}
