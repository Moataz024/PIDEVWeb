<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Repository\TerrainRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $reservation->setClient($user);
        $reservation->setTerrain($terrain);

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conflictingReservations = $reservationRepository->findConflictingReservations($reservation);

            if (!empty($conflictingReservations)) {
                $conflictingTimes = [];
                foreach ($conflictingReservations as $conflictingReservation) {
                    $conflictingTimes[] = $conflictingReservation->getStartTime()->format('Y-m-d H:i:s').' - '.$conflictingReservation->getEndTime()->format('Y-m-d H:i:s');
                }

                $errorMessage = sprintf('Selected date and time range is not available. Conflicts with reservation(s): %s', implode(', ', $conflictingTimes));
                $this->addFlash('error', $errorMessage);
                return $this->redirectToRoute('app_reservation_new', ['id_user' => $id_user, 'id_terrain' => $id_terrain]);
            }

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
                foreach ($conflictingReservations as $conflictingReservation) {
                    $conflictingTimes[] = $conflictingReservation->getStartTime()->format('Y-m-d H:i:s').' - '.$conflictingReservation->getEndTime()->format('Y-m-d H:i:s');
                }

                $errorMessage = sprintf('Selected date and time range is not available. Conflicts with reservation(s): %s', implode(', ', $conflictingTimes));
                $this->addFlash('error', $errorMessage);
                return $this->redirectToRoute('app_reservation_edit', ['id' => $reservation->getId(), 'id_user' => $id_user]);
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
}
