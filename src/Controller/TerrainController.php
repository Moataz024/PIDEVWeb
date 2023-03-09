<?php

namespace App\Controller;

use App\Entity\Terrain;
use App\Form\TerrainType;
use App\Repository\TerrainRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Knp\Snappy\Pdf;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/terrain')]
class TerrainController extends AbstractController
{
    private $security;
    public function __construct(Security $security){
        $this->security = $security;
    }
    //FRONT-END
    
    #[Route('/search', name: 'search_terrain', methods: ['GET'])]
    public function searchAction(Request $request,SerializerInterface $serializer,TerrainRepository $terrainRepository)
    {
        $searchString = $request->query->get('value');
        $terrains = $terrainRepository->findByString($searchString);
        $json_data=$serializer->serialize($terrains, 'json', ['groups' => 'Terrains']);
        return new Response($json_data);
    }
    #[Route('/statistics', name: 'statistics_terrain')]
    public function chartData(Request $request): Response
    {

        $user = $this->security->getUser();
        $terrains = $user->getTerrains();
        $data = [];

        foreach ($terrains as $terrain) {
            $revenue = 0;
            $reservations = $terrain->getReservations();
            foreach ($reservations as $reservation) {
                $nbPerson = $reservation->getNbPerson();
                $revenue += $nbPerson * $terrain->getRentPrice();
                $equipments = $reservation->getEquipments();
                foreach ($equipments as $equipment) {
                    $revenue += $equipment->getPrice();
                }     
            }
            $app_profit=((20 * $revenue)/100);
            $revenue -= $app_profit;
            $data[] = [
                'label' => $terrain->getName(),
                'revenue' => $revenue
            ];
        }

        return $this->render('terrain/revenue.html.twig', [
            'chartData' => json_encode($data)
        ]);
    }

    #[Route('/recent', name: 'app_terrain_index', methods: ['GET'])]
    public function index(TerrainRepository $terrainRepository): Response
    {
        return $this->render('terrain/book_terrain.html.twig', [
            'terrains' => $terrainRepository->findRecentTerrains(),
        ]);
    }
    #[Route('/all', name: 'app_terrain_all', methods: ['GET'])]
    public function explore_all(Request $req,TerrainRepository $terrainRepository,PaginatorInterface $paginator): Response
    {
        $data =$terrainRepository->findAll();
        $terrains = $paginator->paginate(
            $data,
            $req->query->getInt('page',1),
            5
        );
        return $this->render('terrain/all_terrain.html.twig', [
            'terrains' => $terrains,
        ]);
    }
    
    
    #[Route('/filter', name: 'app_terrains_filter')]
    public function filterTerrains(Request $request)
    {
        $location = $request->query->get('location');
        $sportType = $request->query->get('sportType');
        $rentPrice = floatval($request->query->get('rentPrice'));

        $terrains = $this->getDoctrine()
            ->getRepository(Terrain::class)
            ->findByFilters($location, $sportType, $rentPrice);

        return $this->render('terrain/filter.html.twig', [
            'terrains' => $terrains,
            'city' => $location,
            'sportType' => $sportType,
            'rentPrice' => $rentPrice,
        ]);
    }
    #[Route('/list', name: 'app_terrain_list', methods: ['GET'])]
    public function owner_terrain(): Response
    {
        $user = $this->security->getUser();
        $terrains = $user->getTerrains();
        $terrainReservations = array();
        foreach ($terrains as $terrain) {
            $reservations = $terrain->getReservations();
            $terrainReservations[$terrain->getId()] = count($reservations);
        }
        
        return $this->render('terrain/index.html.twig', [
            'terrains' => $terrains,
            'terrainReservations' => $terrainReservations,
        ]);
    }

    #[Route('/listReservations/{id_terrain}', name: 'app_terrain_list_Reservations', methods: ['GET'])]
    public function reservations_terrain($id_terrain, TerrainRepository $terrainRepository): Response
    {
        $terrain = $terrainRepository->find($id_terrain);
        $reservations = $terrain->getReservations();
      
        
        return $this->render('terrain/reservations_terrain_show.html.twig', [
            'reservations' => $reservations,
        ]);
    }
   

    #[Route('/new', name: 'app_terrain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TerrainRepository $terrainRepository): Response
    {
        $user = $this->security->getUser();

        $terrain = new Terrain();
        $terrain->setOwner($user);
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $terrainRepository->save($terrain, true);

            return $this->redirectToRoute('app_terrain_list');
        }

        return $this->renderForm('terrain/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

   
    #[Route('/{id_terrain}/display', name: 'app_display_terrain', methods: ['GET'])]
    public function display_show($id_terrain, TerrainRepository $terrainRepository): Response
    {
        $terrain = $terrainRepository->find($id_terrain);
        return $this->render('terrain/display_terrain.html.twig', [
            'terrain' => $terrain,
        ]);
    }
    #[Route('/edit/{id_terrain}', name: 'app_terrain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,$id_terrain, TerrainRepository $terrainRepository): Response
    {
        $terrain = $terrainRepository->find($id_terrain);
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $terrainRepository->save($terrain, true);

            return $this->redirectToRoute('app_terrain_list');
        }

        return $this->renderForm('terrain/edit.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/{id_terrain}', name: 'app_terrain_delete', methods: ['POST'])]
    public function delete(Request $request,$id_terrain, TerrainRepository $terrainRepository): Response
    {
        $terrain = $terrainRepository->find($id_terrain);
        if ($this->isCsrfTokenValid('delete'.$terrain->getId(), $request->request->get('_token'))) {
            $terrainRepository->remove($terrain, true);
        }

        return $this->redirectToRoute('app_terrain_list');
    }

    #[Route('/pdf/{id_terrain}', name: 'app_export_terrain_pdf', methods: ['GET', 'POST'])]
    public function exportTerrainPdf($id_terrain, TerrainRepository $terrainRepository, Pdf $snappy)
    {
        $terrain = $terrainRepository->find($id_terrain);

        $terrainReservations = array();
        $reservations = $terrain->getReservations();
        $terrainReservations[$terrain->getId()] = count($reservations);

        // Render the terrain in HTML using a template
        $html = $this->renderView('pdf/terrain_pdf.html.twig', [
            'terrain' => $terrain,
            'terrainReservations' => $terrainReservations,
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
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $terrain->getName() . '.pdf"');
        $response->setContent($pdf);

        return $response;
    }
    #[Route('/{id_terrain}', name: 'app_terrain_show', methods: ['GET'])]
    public function show($id_terrain, TerrainRepository $terrainRepository): Response
    {
        $terrain = $terrainRepository->find($id_terrain);
        return $this->render('terrain/show.html.twig', [
            'terrain' => $terrain,
        ]);
    }

    #[Route('/{id_terrain}/calendar', name: 'app_terrain_Calendar', methods: ['GET'])]  
    public function displayCalendar($id_terrain, TerrainRepository $terrainRepo)
    {
        // Retrieve the terrain entity
        $terrain = $terrainRepo->find($id_terrain);

        // Retrieve the reservations for the terrain
        $reservations = $terrain->getReservations();

        // Format the reservations as fullCalendar events
        $events = array();
        foreach ($reservations as $reservation) {
            $event = array(
                'title' => 'Reservation ' . $reservation->getStartTime()->format('H:i') . ' - ' . $reservation->getEndTime()->format('H:i'),
                'start' => $reservation->getDateReservation()->format('Y-m-d') . 'T' . $reservation->getStartTime()->format('H:i:s'),
                'end' => $reservation->getDateReservation()->format('Y-m-d') . 'T' . $reservation->getEndTime()->format('H:i:s')
            );
            array_push($events, $event);
        }

        // Render the calendar template with the events
        return $this->render('terrain/calendar.html.twig', [
            'events' => $events
        ]);
    }    
    
}

