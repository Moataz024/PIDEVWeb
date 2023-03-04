<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security;
use App\Entity\Academy;
use App\Form\AcademyType;
use App\Form\ApplyType;
use App\Repository\AcademyRepository;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/academy')]
class AcademyController extends AbstractController
{
    #[Route('/', name: 'app_academy_index', methods: ['GET'])]
    public function index(AcademyRepository $academyRepository,Security $security): Response
    {
        // if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
        //     return $this->redirectToRoute('app_login');
        // }
        if ($this->isGranted('ROLE_OWNER')){
            return $this->render('academy/index.html.twig', [
                'academies'=> $academyRepository->findBy([
                    'createdBy' => 'front',
                ])
            ]);
        }else{
            return $this->render('academy/client.html.twig', [
                'academies' => $academyRepository->findBy([
                    'createdBy' => 'front',
                ])
            ]);
        }
    }
    
    // #[Route('/client', name: 'app_academy_client', methods: ['GET'])]
    // public function client(AcademyRepository $academyRepository): Response
    // {
    //     return $this->render('academy/client.html.twig', [
    //         'academies' => $academyRepository->findBy([
    //             'createdBy' => 'front',
    //         ])
    //     ]);
    // }

    #[Route('/new', name: 'app_academy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AcademyRepository $academyRepository): Response
    {
        $academy = new Academy();
        $form = $this->createForm(AcademyType::class, $academy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $academy->setCreatedBy('front');
            $academyRepository->save($academy, true);

            return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('academy/new.html.twig', [
            'academy' => $academy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_academy_show', methods: ['GET','POST'])]
    public function show(Academy $academy,CoachRepository $CoachRepository): Response
    {
        $coaches = $CoachRepository->findBy(['academyId' => $academy]);
        if ($this->isGranted('ROLE_OWNER')){
            return $this->render('academy/show.html.twig', [
                'academy' => $academy,
                'coaches' => $coaches,
            ]);
        }else{
            return $this->render('academy/show_client.html.twig', [
                'academy' => $academy,
                'coaches' => $coaches,
            ]);
        }
        
    }
    // #[Route('/{id}/client', name: 'app_academy_show_client', methods: ['GET','POST'])]
    // public function show_client(Academy $academy,CoachRepository $CoachRepository): Response
    // {
    //         $coaches = $CoachRepository->findBy(['academyId' => $academy]);
    //     return $this->render('academy/show_client.html.twig', [
    //         'academy' => $academy,
    //         'coaches' => $coaches,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_academy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Academy $academy, AcademyRepository $academyRepository): Response
    {
        $form = $this->createForm(AcademyType::class, $academy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $academyRepository->save($academy, true);

            return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('academy/edit.html.twig', [
            'academy' => $academy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academy_delete', methods: ['POST'])]
    public function delete(Request $request, Academy $academy, AcademyRepository $academyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$academy->getId(), $request->request->get('_token'))) {
            $academyRepository->remove($academy, true);
        }

        return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/apply', name: 'app_academy_apply', methods: ['GET','POST'])]
    public function Apply(Request $request, Academy $academy, AcademyRepository $academyRepository): Response
    {
        $form = $this->createForm(ApplyType::class, $academy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $academyRepository->save($academy, true);
            
            return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('academy/apply.html.twig', [
            'academy' => $academy,
            'form' => $form,
        ]);
    }   
}
