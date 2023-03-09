<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Terrain;
use App\Form\TerrainType;
use App\Repository\TerrainRepository;
use App\Repository\UserRepository;

class TerrainApiController extends AbstractController
{
    #[Route('/terrainApiAfficher', name: 'terrain_show_all', methods: ['GET', 'POST'])]
    public function showAll_terrain(TerrainRepository $terrainRepository, SerializerInterface $serializer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->createQuery(
            'SELECT t, u FROM App\Entity\Terrain t
            JOIN t.owner u'
        );

        $terrains = $query->getResult();
        $data = $serializer->serialize($terrains, 'json', ['groups' => ['Terrains','Reservations']]);

        // Loop through the terrains and add the image URL and owner to the serialized data
        $imageUrlPrefix = $this->getParameter('app.path.terrain_images');
        $dataArray = json_decode($data, true);
        foreach ($dataArray as &$terrainData) {
            $imageUrl = $imageUrlPrefix . '/' . $terrainData['imageName'];
            $terrainData['image_url'] = $imageUrl;

            foreach ($terrains as $terrain) {
                if ($terrain->getId() == $terrainData['id']) {
                    $ownerData = $serializer->normalize($terrain->getOwner(), null, ['groups' => 'users']);
                    $terrainData['owner'] = $ownerData;
                    break;
                }
            }
        }
        $data = json_encode($dataArray);

        return new Response($data, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
    #[Route('/terrainApiAfficher/{id_terrain}', name: 'terrain_show_id', methods: ['GET', 'POST'])]
    public function showId_terrain($id_terrain,TerrainRepository $terrainRepository, SerializerInterface $serializer,UserRepository $UserRepository): Response
    { 
        $terrain=$terrainRepository->find($id_terrain);
        $ownerData = $serializer->normalize($terrain->getOwner(), null, ['groups' => 'users']);

        $data = $serializer->serialize($terrain, 'json',  ['groups' => ['Terrains','Reservations']]);
        $imageUrlPrefix = $this->getParameter('app.path.terrain_images');
        $dataArray = json_decode($data, true);
        $imageUrl = $imageUrlPrefix . '/' . $dataArray['imageName'];
        $dataArray['image_url'] = $imageUrl;
        $dataArray['owner'] = $ownerData;

        $data = json_encode($dataArray);
        return new Response($data, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    #[Route('/terrainApiAfficher/owner/{id_user}', name: 'terrain_show_user', methods: ['GET', 'POST'])]
    public function showOwner_terrain($id_user, SerializerInterface $serializer,UserRepository $UserRepository): Response
    { 
        $user = $UserRepository->find($id_user);
        $terrains = $user->getTerrains();
        

        $data = $serializer->serialize($terrains, 'json',  ['groups' => ['Terrains']]);
        $imageUrlPrefix = $this->getParameter('app.path.terrain_images');
        $dataArray = json_decode($data, true);
        foreach ($dataArray as &$terrainData) {
            $imageUrl = $imageUrlPrefix . '/' . $terrainData['imageName'];
            $terrainData['image_url'] = $imageUrl;

            foreach ($terrains as $terrain) {
                if ($terrain->getId() == $terrainData['id']) {
                    $ownerData = $serializer->normalize($terrain->getOwner(), null, ['groups' => 'users']);
                    $terrainData['owner'] = $ownerData;
                    break;
                }
            }
        }
        $data = json_encode($dataArray);
        return new Response($data, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
    #[Route('/terrainApiAjouter/{id_user}', name: 'terrain_new', methods: ['GET', 'POST'])]
    public function ajouter_terrain(Request $req,SerializerInterface $serializer,$id_user,UserRepository $userRepository,terrainRepository $terrainRepository): Response
    {
        $user = $userRepository->find($id_user);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }
        $terrain = new Terrain();
        $terrain->setOwner($user);
        $terrain->setName($req->get('name'));
        $terrain->setCapacity(intval($req->get('capacity')));
        $terrain->setSportType($req->get('sportType'));
        $terrain->setRentPrice(floatval($req->get('rentPrice')));
        $terrain->setDisponibility($req->get('disponibility') === 'true' ? true : false);
        $terrain->setPostalCode(intval($req->get('postalCode')));
        $terrain->setRoadName($req->get('roadName'));
        $terrain->setRoadNumber(intval($req->get('roadNumber')));
        $terrain->setCity($req->get('city'));
        $terrain->setCountry($req->get('country'));
        $terrain->setImageName($req->get('imageName'));
        $terrainRepository->save($terrain, true);
        $ownerData = $serializer->normalize($terrain->getOwner(), null, ['groups' => 'users']);
        $data = $serializer->serialize($terrain, 'json', ['groups' => ['Terrains','Reservations']]);
        $dataArray = json_decode($data, true);
        $dataArray['owner'] = $ownerData;
        $data = json_encode($dataArray);
        return new Response($data, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    #[Route('/terrainApiModifier/{id_terrain}', name: 'terrain_update', methods: ['GET', 'POST'])]
    public function modifier_terrain(Request $req,$id_terrain,SerializerInterface $serializer,terrainRepository $terrainRepository): Response
    {
        $terrain=$terrainRepository->find($id_terrain);
        $terrain->setName($req->get('name'));
        $terrain->setCapacity(intval($req->get('capacity')));
        $terrain->setSportType($req->get('sportType'));
        $terrain->setRentPrice(floatval($req->get('rentPrice')));
        $terrain->setDisponibility($req->get('disponibility') === 'true' ? true : false);
        $terrain->setPostalCode(intval($req->get('postalCode')));
        $terrain->setRoadName($req->get('roadName'));
        $terrain->setRoadNumber(intval($req->get('roadNumber')));
        $terrain->setCity($req->get('city'));
        $terrain->setCountry($req->get('country'));
        $terrain->setImageName($req->get('imageName'));
        $terrainRepository->save($terrain, true);
        $ownerData = $serializer->normalize($terrain->getOwner(), null, ['groups' => 'users']);
        $data = $serializer->serialize($terrain, 'json', ['groups' => ['Terrains','Reservations']]);
        $dataArray = json_decode($data, true);
        $dataArray['owner'] = $ownerData;
        $data = json_encode($dataArray);
        return new Response($data, 200, [
            'Content-Type' => 'application/json',
        ]); 
    }
    #[Route('/terrainApiSupprimer/{id_terrain}', name: 'terrain_delete', methods: ['GET','POST'])]
    public function supprimer_terrain($id_terrain,SerializerInterface $serializer,terrainRepository $terrainRepository): Response
    {
        $terrain=$terrainRepository->find($id_terrain);
        $ownerData = $serializer->normalize($terrain->getOwner(), null, ['groups' => 'users']);
        $data = $serializer->serialize($terrain, 'json', ['groups' => ['Terrains','Reservations']]);
        $dataArray = json_decode($data, true);
        $dataArray['owner'] = $ownerData;
        $data = json_encode($dataArray);

        $terrainRepository->remove($terrain, true);
        
        return new Response($data, 200, [
            'Content-Type' => 'application/json',
        ]); 
    }
}