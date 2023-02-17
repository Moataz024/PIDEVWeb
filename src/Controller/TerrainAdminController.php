<?php

namespace App\Controller;

use App\Entity\Terrain;
use App\Form\TerrainType;
use App\Repository\TerrainRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/terrain')]
class TerrainAdminController extends AbstractController
{
 /// Back-END --------------------------------------------------------------------------||
   #[Route('/admin', name: 'app_terrain_index_admin', methods: ['GET'])]
    public function index_admin(TerrainRepository $terrainRepository): Response
    {
        return $this->render('terrain_admin/index_admin.html.twig', [
            'terrains' => $terrainRepository->findAll(),
        ]);
    }

    #[Route('/admin/new', name: 'app_terrain_new_admin', methods: ['GET', 'POST'])]
    public function new_admin(Request $request, TerrainRepository $terrainRepository): Response
    {
        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $terrainRepository->save($terrain, true);

            return $this->redirectToRoute('app_terrain_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('terrain_admin/new_admin.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_terrain_show_admin', methods: ['GET'])]
    public function show_admin(Terrain $terrain): Response
    {
        return $this->render('terrain_admin/show_admin.html.twig', [
            'terrain' => $terrain,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_terrain_edit_admin', methods: ['GET', 'POST'])]
    public function edit_admin(Request $request, Terrain $terrain, TerrainRepository $terrainRepository): Response
    {
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $terrainRepository->save($terrain, true);

            return $this->redirectToRoute('app_terrain_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('terrain_admin/edit_admin.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_terrain_delete_admin', methods: ['POST'])]
    public function delete_admin(Request $request, Terrain $terrain, TerrainRepository $terrainRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$terrain->getId(), $request->request->get('_token'))) {
            $terrainRepository->remove($terrain, true);
        }

        return $this->redirectToRoute('app_terrain_index_admin', [], Response::HTTP_SEE_OTHER);
    }
}
