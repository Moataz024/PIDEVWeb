<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Repository\TerrainRepository;

class ReservationApiController extends AbstractController
{
    #[Route('/reservation/api', name: 'app_reservation_api')]
    public function index(): Response
    {
        return $this->render('reservation_api/index.html.twig', [
            'controller_name' => 'ReservationApiController',
        ]);
    }
}
