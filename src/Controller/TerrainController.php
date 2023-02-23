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

#[Route('/terrain')]
class TerrainController extends AbstractController
{
    //FRONT-END
    #[Route('/recent', name: 'app_terrain_index', methods: ['GET'])]
    public function index(TerrainRepository $terrainRepository): Response
    {
        return $this->render('terrain/book_terrain.html.twig', [
            'terrains' => $terrainRepository->findRecentTerrains(),
        ]);
    }
    #[Route('/all', name: 'app_terrain_all', methods: ['GET'])]
    public function explore_all(TerrainRepository $terrainRepository): Response
    {
        return $this->render('terrain/all_terrain.html.twig', [
            'terrains' => $terrainRepository->findAll(),
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
    #[Route('/{id_user}', name: 'app_terrain_list', methods: ['GET'])]
    public function owner_terrain(UserRepository $userRepository ,$id_user): Response
    {
       $user = $userRepository->find($id_user);
       if(!$user)
        {
        throw $this->createNotFoundException('The user does not exist');
        }
        $terrains = $user->getTerrains();
        return $this->render('terrain/index.html.twig', [
            'terrains' => $terrains,
        ]);
    }
   

    #[Route('/{id_user}/new', name: 'app_terrain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TerrainRepository $terrainRepository,UserRepository $userRepository,int $id_user): Response
    {
        $user = $userRepository->find($id_user);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }
        $terrain = new Terrain();
        $terrain->setOwner($user);
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $terrainRepository->save($terrain, true);

            return $this->redirectToRoute('app_terrain_list', ['id_user' => $id_user]);
        }

        return $this->renderForm('terrain/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/{id_user}', name: 'app_terrain_show', methods: ['GET'])]
    public function show(Terrain $terrain,$id_user,UserRepository $userRepository): Response
    {
        $currentUser = $userRepository->find($id_user);
    
        // Check if the authenticated user is the owner of the terrain
        if ($terrain->getOwner() !== $currentUser) {
            throw $this->createNotFoundException('The terrain does not exist');
        }
        return $this->render('terrain/show.html.twig', [
            'terrain' => $terrain,
        ]);
    }

    #[Route('/{id}/{id_user}/edit', name: 'app_terrain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Terrain $terrain, TerrainRepository $terrainRepository,$id_user, UserRepository $UserRepository): Response
    {
        $currentUser = $UserRepository->find($id_user);
        if ($terrain->getOwner() !== $currentUser) {
            throw $this->createNotFoundException();
        }
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $terrainRepository->save($terrain, true);

            return $this->redirectToRoute('app_terrain_list', ['id_user' => $id_user]);
        }

        return $this->renderForm('terrain/edit.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/{id_user}', name: 'app_terrain_delete', methods: ['POST'])]
    public function delete(Request $request, Terrain $terrain, TerrainRepository $terrainRepository,$id_user,UserRepository $UserRepository): Response
    {
        $currentUser = $UserRepository->find($id_user);
        if ($terrain->getOwner() !== $currentUser) {
            throw $this->createNotFoundException();
        }
        if ($this->isCsrfTokenValid('delete'.$terrain->getId(), $request->request->get('_token'))) {
            $terrainRepository->remove($terrain, true);
        }

        return $this->redirectToRoute('app_terrain_list', ['id_user' => $id_user]);
    }


}
