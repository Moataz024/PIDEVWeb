<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/event_json')]
class EventJsonController extends AbstractController
{
    #[Route("/allEvents", name: "event")]
    public function get_AllEvents(EventRepository $repo ,SerializerInterface $serializer) : JsonResponse
    {
        $event=$repo->findAll();
        $json=$serializer->serialize($event,'json', ['groups'=>['events','users'] ]);
        return new JsonResponse($json,200,[],true);
    }

    #[Route("/Event/{id}")]
    public function eventId(EventRepository $repo ,NormalizerInterface $normalizer, $id)
    {
        $event=$repo->find($id);
        $eventNormalises = $normalizer->normalize($event,'json',['groups'=>['events','users'] ]);
        return new Response(json_encode($eventNormalises));
    }


    #[Route("/addEvent/{id_user}")]
    public function addEvent(Request $req ,NormalizerInterface $normalizer,$id_user,UserRepository $repository,EventRepository $eventRepo)
    {
        $user=$repository->find($id_user);

        $event = new Event();
        //$event->setImage($req->get('image'));
        $event->setNom($req->get('nom'));
        $event->setCategory($req->get('category'));
        $event->setLieu($req->get('lieu'));
        $event->setDescription($req->get('description'));
        $event->setOrganisateur($user);
        $eventRepo->save($event,true);
        $jsonContent =$normalizer->normalize($event, 'json',['groups'=>['events','users'] ]);
        return new Response(json_encode($jsonContent));
    }


    #[Route("/deleteEvent/{id}")]
    public function deleteEvent(Request $req ,$id,NormalizerInterface $normalizer,EventRepository $eventRepository)
    {
        $event =$eventRepository->find($id);
        $eventRepository->remove($event,true);
        $jsonContent =$normalizer->normalize($event, 'json',['groups'=>['events','users'] ]);
        return new Response("Event deleted successsfully" . json_encode($jsonContent));
    }


    #[Route("/updateEvent/{id}")]
    public function updateEvent(Request $req ,$id,NormalizerInterface $normalizer,EventRepository $eventRepository)
    {
        $event = $eventRepository->find($id);
        //$event->setImage($req->get('image'));
        $event->setNom($req->get('nom'));
        $event->setCategory($req->get('category'));
        $event->setLieu($req->get('lieu'));
        $event->setDescription($req->get('description'));
        //$event->setOrganisateur($req->get('organisateur'));
        $eventRepository->save($event,true);
        $jsonContent =$normalizer->normalize($event, 'json', ['groups' => 'events']);
        return new Response("Event updated successsfully" . json_encode($jsonContent));
    }




}
