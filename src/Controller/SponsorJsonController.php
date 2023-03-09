<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\SponsorE;
use App\Repository\EventRepository;
use App\Repository\SponsorERepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/sponsor_json')]
class SponsorJsonController extends AbstractController
{

    #[Route("/allSponsors")]
    public function get_AllSponsors(SponsorERepository $repo ,SerializerInterface $serializer) : JsonResponse
    {
        $sponsor=$repo->findAll();
        $json=$serializer->serialize($sponsor,'json', ['groups'=>['sponsors'] ]);
        return new JsonResponse($json,200,[],true);
    }


    #[Route("/Sponsor/{id}")]
    public function sponsorId(SponsorERepository $repo ,NormalizerInterface $normalizer, $id)
    {
        $sponsor=$repo->find($id);
        $eventNormalises = $normalizer->normalize($sponsor,'json',['groups'=>['sponsors'] ]);
        return new Response(json_encode($eventNormalises));
    }

    #[Route("/addSponsor/{id_user}")]
    public function addSponsor(Request $req ,NormalizerInterface $normalizer,$id_user,UserRepository $repository,SponsorERepository $eventRepo)
    {
        $user=$repository->find($id_user);

        $sponsor = new SponsorE();
        //$event->setImage($req->get('image'));
        $sponsor->setNomSponsor($req->get('nomSponsor'));
        $sponsor->setEmailSponsor($req->get('emailSponsor'));
        $sponsor->setTelSponsor($req->get('telSponsor'));
        $eventRepo->save($sponsor,true);
        $jsonContent =$normalizer->normalize($sponsor, 'json',['groups'=>['sponsors','users'] ]);
        return new Response(json_encode($jsonContent));
    }



    #[Route("/deleteSponsor/{id}")]
    public function deleteSponsor(Request $req ,$id,NormalizerInterface $normalizer,SponsorERepository $eventRepository)
    {
        $event =$eventRepository->find($id);
        $eventRepository->remove($event,true);
        $jsonContent =$normalizer->normalize($event, 'json',['groups'=>['sponsors'] ]);
        return new Response("Sponsor delete successsfully" . json_encode($jsonContent));
    }


    #[Route("/updateSponsor/{id}")]
    public function updateSponsor(Request $req ,$id,NormalizerInterface $normalizer,SponsorERepository $eventRepository)
    {


        $event = $eventRepository->find($id);
        //$event->setImage($req->get('image'));
        $event->setNomSponsor($req->get('nomSponsor'));
        $event->setEmailSponsor($req->get('emailSponsor'));
        $event->setTelSponsor($req->get('telSponsor'));
        //$event->setOrganisateur($req->get('organisateur'));
        $eventRepository->save($event,true);
        $jsonContent =$normalizer->normalize($event, 'json', ['groups' => 'sponsors']);
        return new Response("Sponsor update successsfully" . json_encode($jsonContent));
    }



}
