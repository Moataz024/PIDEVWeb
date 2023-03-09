<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
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
    public function index(Request $request,AcademyRepository $acrepo, PaginatorInterface $paginator): Response
    {
        $qb = $acrepo->createQueryBuilder('c')
        ->orderBy('c.id', 'ASC'); // default order
        // Add sorting based on the query parameters
        $sortField = $request->query->get('sortField', 'id');
        $sortDirection = $request->query->get('sortDirection', 'asc');
        $qb->orderBy("c.$sortField", $sortDirection);

        // Add a search filter based on the query parameter 'q'
        $q = $request->query->get('q');
        if ($q) {
            $qb->andWhere('c.name LIKE :search')
            ->setParameter('search', '%' . $q . '%');
        }

        // Paginate the results
        $pagination = $paginator->paginate(
            $qb, // query builder
            $request->query->getInt('page', 1), // current page number
            10 // maximum number of results per page
        );

        // if ($this->isGranted('ROLE_ADMIN')){
            return $this->render('academy_back/index.html.twig', [
                'academy_backs' => $pagination,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
                'searchQuery' => $q,
            ]);
        // }else{
        //     return $this->render('error_pages/meme.html.twig');
        // }
    }

    #[Route('/new', name: 'app_academy_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AcademyRepository $academyBackRepository): Response
    {
        // if ($this->isGranted('ROLE_ADMIN')){
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
        // }else{
        //     return $this->render('error_pages/meme.html.twig');
        // }
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
        // if ($this->isGranted('ROLE_ADMIN')){
            $coachesBack = $CoachRepository->findBy(['academyId' => $academyBack]);
            return $this->render('academy_back/show.html.twig', [
                'academy_back' => $academyBack,
                'coaches' => $coachesBack,
            ]);
        // }else{
        //     return $this->render('error_pages/meme.html.twig');
        // }
    }

    #[Route('/{id}/edit', name: 'app_academy_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Academy $academyBack, AcademyRepository $academyBackRepository): Response
    {
        // if ($this->isGranted('ROLE_ADMIN')){
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
        // }else{
        //     return $this->render('error_pages/meme.html.twig');
        // }
    }

    #[Route('/{id}', name: 'app_academy_back_delete', methods: ['POST'])]
    public function delete(Request $request, Academy $academyBack, AcademyRepository $academyBackRepository): Response
    {
        // if ($this->isGranted('ROLE_ADMIN')){
            if ($this->isCsrfTokenValid('delete'.$academyBack->getId(), $request->request->get('_token'))) {
                $academyBackRepository->remove($academyBack, true);
            }

            return $this->redirectToRoute('app_academy_back_index', [], Response::HTTP_SEE_OTHER);
        // }else{
        //     return $this->render('error_pages/meme.html.twig');
        // }
    }
}
