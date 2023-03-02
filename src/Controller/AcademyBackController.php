<?php

namespace App\Controller;

use App\Entity\Academy;
use App\Form\AcademyType;
use App\Repository\AcademyRepository;
use App\Repository\CoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/academy/back')]
class AcademyBackController extends AbstractController
{
    #[Route('/', name: 'app_academy_back_index', methods: ['GET'])]
    public function index(AcademyRepository $acrepo): Response
    {
        return $this->render('academy_back/index.html.twig', [
            // 'academy_backs' => $academyBackRepository->findBy([
            //     'createdBy' => 'back',
            // ]),    
            // 'academy_fronts' => $acrepo->findAll(),
            'academy_backs' => $acrepo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_academy_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AcademyRepository $academyBackRepository): Response
    {
        $academyBack = new Academy();
        $form = $this->createForm(AcademyType::class, $academyBack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $academyBack->setCreatedBy('back');
            $academyBackRepository->save($academyBack, true);
            


            return $this->redirectToRoute('app_academy_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('academy_back/new.html.twig', [
            'academy_back' => $academyBack,
            'form' => $form,
        ]);
    }
    // #[Route('/new', name: 'app_academy_back_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, AcademyBackRepository $academyBackRepository): Response
    // {
    //     $academyBack = new Academy();
    //     $form = $this->createForm(AcademyType::class, $academyBack);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($academyBack);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_academy_back_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('academy_back/new.html.twig', [
    //         'academy_back' => $academyBack,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/new', name: 'app_academy_back_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, AcademyBackRepository $academyBackRepository): Response
    // {
    //     $academyBack = new Academy();
    //     $form = $this->createForm(AcademyType::class, $academyBack);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getEntityManager();
    //         $entityManager->persist($academyBack);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_academy_back_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('academy_back/new.html.twig', [
    //         'academy_back' => $academyBack,
    //         'form' => $form,
    //     ]);
    // }


    

    #[Route('/{id}/show', name: 'app_academy_back_show', methods: ['GET','POST'])]
    public function show(Academy $academyBack,CoachRepository $CoachRepository): Response
    {
        $coachesBack = $CoachRepository->findBy(['academyId' => $academyBack]);
        return $this->render('academy_back/show.html.twig', [
            'academy_back' => $academyBack,
            'coaches' => $coachesBack,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_academy_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Academy $academyBack, AcademyRepository $academyBackRepository): Response
    {
        $form = $this->createForm(AcademyType::class, $academyBack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $academyBackRepository->save($academyBack, true);

            return $this->redirectToRoute('app_academy_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('academy_back/edit.html.twig', [
            'academy_back' => $academyBack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academy_back_delete', methods: ['POST'])]
    public function delete(Request $request, Academy $academyBack, AcademyRepository $academyBackRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$academyBack->getId(), $request->request->get('_token'))) {
            $academyBackRepository->remove($academyBack, true);
        }

        return $this->redirectToRoute('app_academy_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
