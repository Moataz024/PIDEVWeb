<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/eventBack')]
class EventBackController extends AbstractController
{
    #[Route('/', name: 'app_event_back', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        /*test*/
        return $this->render('event_back/indexBack.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }


    #[Route('/newBack', name: 'app_event_newBack', methods: ['GET', 'POST'])]
    public function newBack(Request $request, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event_back/newBack.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }


    #[Route('/showBack/{id}', name: 'app_event_showBack', methods: ['GET'])]
    public function showBAck(Event $event): Response
    {
        return $this->render('event_back/showBack.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{event_id}/organisateur', name: 'show_organisateur2', methods: ['GET','POST'])]
    public function showOrganisateur(EventRepository $eventrepo , $event_id)
    {
        $event=$eventrepo->find($event_id);
        $organisateur = $event->getOrganisateur();

        return $this->render('event_Back/showBackOrganisateur.html.twig', [
            'event' => $event,
            'organisateur' => $organisateur,
        ]);
    }




    #[Route('/editBack/{id}/edit', name: 'app_event_editBack', methods: ['GET', 'POST'])]
    public function editBack(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event_back/editBack.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }


    #[Route('/showParticipantBack/{id}', name: 'app_event_showPArticipantBack', methods: ['GET'])]
    public function showParticipantBack(Event $event): Response
    {
        $participant = $event->getParticipants();
        return $this->render('event_back/showParticipantBack.html.twig', [
            'participants' => $participant,
        ]);
    }




}
