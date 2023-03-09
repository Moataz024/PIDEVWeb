<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\SponsorERepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/event')]
class EventController extends AbstractController
{
    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository,SponsorERepository $sponsorERepository): Response
    {
        /*test*/
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/showParticipant/{id}', name: 'app_event_showPArticipant', methods: ['GET'])]
    public function showParticipant(Event $event): Response
    {
        $participants = $event->getParticipants();
        $sponsors = $event->getSponsors();
        return $this->render('event/showParticipant.html.twig', [
            'event' => $event,
            'participants' => $participants,
            'sponsors' => $sponsors,
        ]);
    }


    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventRepository $eventRepository): Response
    {
        $user = $this->security->getUser();
        $event = new Event();
        $event->setOrganisateur($user);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setOrganisateur($user);
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $user = $this->security->getUser();
        $event->setOrganisateur($user);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setOrganisateur($user);
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }






    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $eventRepository->remove($event, true);
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/add_participant/{id}', name: 'add_participant', methods: ['GET','POST'])]
    public function addParticipantToEvent(Event $event,EventRepository $eventRepository, UserRepository $userRepository): Response
    {
        $participant = $this->security->getUser();
        $event->addParticipant($participant);
        $participant->addInscription($event);
        $eventRepository->save($event,true);
        $userRepository->save($participant,true);

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/historique/{id}', name: 'show_historique', methods: ['GET','POST'])]
    public function showHistorique(User $user)
    {
        $inscriptions = $user->getInscriptions();

        return $this->render('event/historique.html.twig', [
            'user' => $user,
            'inscriptions' => $inscriptions,
        ]);
    }







}
