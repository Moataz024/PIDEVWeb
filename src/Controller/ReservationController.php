<?php

namespace App\Controller;

use App\Service\StripeService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Repository\TerrainRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twilio\Rest\Client;


#[Route('/reservation')]
class ReservationController extends AbstractController
{
    
    #[Route('/all', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    #[Route('/{id_user}/myReservation', name: 'app_reservation_list', methods: ['GET'])]
    public function client_reservations(UserRepository $UserRepository,$id_user): Response
    {
        $user = $UserRepository->find($id_user);
        if(!$user)
        {
        throw $this->createNotFoundException('The user does not exist');
        }
        $reservations = $user->getReservations();
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{id_user}/{id_terrain}/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReservationRepository $reservationRepository,UserRepository $UserRepository,$id_user,TerrainRepository $TerrainRepository,$id_terrain): Response
    {
       
        $user = $UserRepository->find($id_user);
        $terrain = $TerrainRepository->find($id_terrain);
        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }
        if (!$terrain) {
            throw $this->createNotFoundException('The terrain does not exist');
        }

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conflictingReservations = $reservationRepository->findConflictingReservations($reservation);

            if (!empty($conflictingReservations)) {
                $conflictingTimes = [];
                $hasConfirmedConflict = false;
                foreach ($conflictingReservations as $conflictingReservation) {
                    if ($conflictingReservation->isResStatus() == true) {
                        $hasConfirmedConflict = true;
                        $conflictingTimes[] = $conflictingReservation->getStartTime()->format('Y-m-d H:i:s').' - '.$conflictingReservation->getEndTime()->format('Y-m-d H:i:s');
                    }  
                }
                if ($hasConfirmedConflict) {
                    $errorMessage = 'Selected date and time range is not available due to a confirmed reservation conflict.';
                    if (!empty($conflictingTimes)) {
                        $errorMessage .= ' Other conflicting reservations:';
                        $errorMessage .= implode(', ', $conflictingTimes);
                    }
                    $this->addFlash('error', $errorMessage);
                    return $this->redirectToRoute('app_reservation_new', ['id_user' => $id_user, 'id_terrain' => $id_terrain]);
                }
            }
            $reservation->setClient($user);
            $reservation->setTerrain($terrain);
            $reservation->setResStatus(false);
            $reservationRepository->save($reservation, true);
            return $this->redirectToRoute('app_reservation_list', ['id_user' => $id_user]);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/{id_user}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, ReservationRepository $reservationRepository,UserRepository $UserRepository,$id_user): Response
    {
        $id_terrain = $reservation->getTerrain()->getId();
        $user = $UserRepository->find($id_user);
        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conflictingReservations = $reservationRepository->findConflictingReservations($reservation);

            if (!empty($conflictingReservations)) {
                $conflictingTimes = [];
                $hasConfirmedConflict = false;
                foreach ($conflictingReservations as $conflictingReservation) {
                    if ($conflictingReservation->isResStatus() == true) {
                        $hasConfirmedConflict = true;
                        $conflictingTimes[] = $conflictingReservation->getStartTime()->format('Y-m-d H:i:s').' - '.$conflictingReservation->getEndTime()->format('Y-m-d H:i:s');
                    }  
                }
                if ($hasConfirmedConflict) {
                    $errorMessage = 'Selected date and time range is not available due to a confirmed reservation conflict.';
                    if (!empty($conflictingTimes)) {
                        $errorMessage .= ' Other conflicting reservations:';
                        $errorMessage .= implode(', ', $conflictingTimes);
                    }
                    $this->addFlash('error', $errorMessage);
                    return $this->redirectToRoute('app_reservation_new', ['id_user' => $id_user, 'id_terrain' => $id_terrain]);
                }
            }
            $reservationRepository->save($reservation, true);

            return $this->redirectToRoute('app_reservation_list', ['id_user' => $id_user]);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/{id_user}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository,UserRepository $UserRepository,$id_user): Response
    {
        $currentUser = $UserRepository->find($id_user);
        if (!$currentUser) {
            throw $this->createNotFoundException('The user does not exist');
        }
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation, true);
        }

        return $this->redirectToRoute('app_reservation_list', ['id_user' => $id_user]);
    }



    // CHeckout test : 
    #[Route('/{id}/checkout', name: 'reservation_checkout')]
    public function checkout(Reservation $reservation, StripeService $stripeService): Response
    {
        if ($reservation->isResStatus()) {
            return $this->redirectToRoute('app_reservation_show', ['id' => $reservation->getId()]);
        }

        $amount = (int)(($reservation->getTerrain()->getRentPrice()/0.3) * $reservation->getNbPerson()*10);
        $currency = 'EUR';
        if($reservation->getEquipments()!=null)
        {
            foreach($reservation->getEquipments() as $e)
            {
                $amount+=((int)(($e->getPrice()*10)/0.3));
            }
        }
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
            'price_data' => [
                'currency' => $currency,
                'unit_amount' => $amount,
                'product_data' => [
                    'name' => 'Reservation',
                    'description' => 'Terrain reservation',
                ],
            ],
            'quantity' => 1,
            ]],'mode' => 'payment',
            'success_url' => $this->generateUrl('reservation_payment_success', ['id' => $reservation->getId()],UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('reservation_payment_cancel', ['id' => $reservation->getId()],UrlGeneratorInterface::ABSOLUTE_URL),
            'metadata' => [
                'reservation_id' => $reservation->getId(),
            ],
        ]);


        return $this->redirect($session->url,303);
    }

   
    #[Route('/{id}/payment/success', name: 'reservation_payment_success', methods: ['GET'])]
    public function paymentSuccess(Reservation $reservation, Client $twilio): Response
    {
        $reservation->setResStatus(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();
         // Send SMS to client
        $accountSid = $_ENV['TWILIO_ACCOUNT_SID'];
        $authToken = $_ENV['TWILIO_AUTH_TOKEN'];
        $twilioNumber = "+15075650863";
        $client = new Client($accountSid, $authToken);
        $message = $client->messages->create(
            '+216'.$reservation->getClient()->getPhone(), // Client's phone number
            [
                'from' => $twilioNumber,
                'body' => 'Sportify-APP: Thank you for making a reservation with us! We look forward to seeing you on'. $reservation->getDateReservation()->format('m/d/Y') . ' at ' . $reservation->getStartTime()->format('h:i A') . '.',
            ]
        );

        return $this->render('reservation/payment_success.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/payment/cancel', name: 'reservation_payment_cancel', methods: ['GET'])]
    public function paymentCancel(Reservation $reservation): Response
    {
        return $this->render('reservation/payment_cancel.html.twig', [
            'reservation' => $reservation,
        ]);
    }
    #[Route('/pdf/{id_reservation}', name: 'app_export_recu_pdf', methods: ['GET', 'POST'])]
    public function exportTerrainPdf($id_reservation, ReservationRepository $reservationRepository, Pdf $snappy)
    {
        $reservation = $reservationRepository->find($id_reservation);

        // Render the terrain in HTML using a template
        $html = $this->renderView('pdf/reservation_pdf.html.twig', [
            'reservation' => $reservation,
        ]);
           // Set Snappy options for the PDF page size and margins
        $snappy->setOption('page-size', 'A4');
        $snappy->setOption('margin-top', '20mm');
        $snappy->setOption('margin-right', '15mm');
        $snappy->setOption('margin-bottom', '20mm');
        $snappy->setOption('margin-left', '15mm');
        // Generate the PDF using Snappy
        $pdf = $snappy->getOutputFromHtml($html);

        // Set the response headers
        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $reservation->getClient()->getFirstname() . $reservation->getClient()->getLastname() . '.pdf"');
        $response->setContent($pdf);

        return $response;
    }
    
}
