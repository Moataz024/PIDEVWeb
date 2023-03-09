<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Repository\TerrainRepository;

class ReservationApiController extends AbstractController
{
    #[Route('/reservationApiAfficher', name: 'app_reservation_api_all')]
    public function getReservations(ReservationRepository $reservationRepository, SerializerInterface $serializer): JsonResponse
    {
        $reservations = $reservationRepository->findAll();
        $data = $serializer->serialize($reservations, 'json', ['groups' => ['Reservations', 'users']]);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/reservationApiAfficher/{id_reservation}', name: 'app_reservation_api_details')]
    public function getReservation(ReservationRepository $reservationRepository,$id_reservation, SerializerInterface $serializer): JsonResponse
    {
        $reservation = $reservationRepository->find($id_reservation);
        $data = $serializer->serialize($reservation, 'json', ['groups' => ['Reservations', 'users']]);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/reservationApiAjoute/{id_user}/{id_terrain}', name: 'app_reservation_api_ajouter')]
    public function CreateReservation(Request $request,TerrainRepository $terrainRepository,ReservationRepository $reservationRepository,UserRepository $userRepository,$id_user,$id_terrain, SerializerInterface $serializer): JsonResponse
    {
        $user = $userRepository->find($id_user);
        $terrain = $terrainRepository->find($id_terrain);
        $data = [
            'dateReservation' => $request->query->get('dateReservation'),
            'startTime' => $request->query->get('startTime'),
            'endTime' => $request->query->get('endTime'),
            'nbPerson' => $request->query->get('nbPerson'),
        ];
        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }
        $reservation = new Reservation();
        $reservation->setClient($user);
        $reservation->setTerrain($terrain);
        $reservation->setDateReservation(new \DateTime($data['dateReservation']));
        $reservation->setStartTime(new \DateTime($data['startTime']));
        $reservation->setEndTime(new \DateTime($data['endTime']));
        $reservation->setResStatus(false);
        $reservation->setNbPerson($data['nbPerson']);
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
                    $response = [
                        'status' => 'error',
                        'message' => $errorMessage
                    ];
                    return new JsonResponse($response, 400);
                }
            }
        $reservationRepository->save($reservation,true);
        $data = $serializer->serialize($reservation, 'json', ['groups' => ['Reservations', 'users']]);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/reservationApiModifier/{id_reservation}', name: 'app_reservation_api_modifier')]
    public function EditReservation(Request $request,$id_reservation,ReservationRepository $reservationRepository, SerializerInterface $serializer): JsonResponse
    {
        $reservation=$reservationRepository->find($id_reservation);
        $data = [
            'dateReservation' => $request->query->get('dateReservation'),
            'startTime' => $request->query->get('startTime'),
            'endTime' => $request->query->get('endTime'),
            'nbPerson' => $request->query->get('nbPerson'),
        ];
        
        $reservation->setDateReservation(new \DateTime($data['dateReservation']));
        $reservation->setStartTime(new \DateTime($data['startTime']));
        $reservation->setEndTime(new \DateTime($data['endTime']));
        $reservation->setNbPerson($data['nbPerson']);
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
                    $response = [
                        'status' => 'error',
                        'message' => $errorMessage
                    ];
                    return new JsonResponse($response, 400);
                }
            }
        $reservationRepository->save($reservation,true);
        $data = $serializer->serialize($reservation, 'json', ['groups' => ['Reservations', 'users']]);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/reservationApiSupprimer/{id_reservation}', name: 'app_reservation_api_supprimer')]
    public function DeleteReservation($id_reservation,ReservationRepository $reservationRepository, SerializerInterface $serializer): JsonResponse
    {
        $reservation=$reservationRepository->find($id_reservation);
        $data = $serializer->serialize($reservation, 'json', ['groups' => ['Reservations', 'users']]);
        $reservationRepository->remove($reservation,true);
        return new JsonResponse($data, 200, [], true);
    }
}
