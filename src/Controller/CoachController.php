<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Academy;
use App\Entity\Coach;
use App\Form\CoachType;
use App\Repository\CoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/coach')]
class CoachController extends AbstractController
{
    #[Route('/', name: 'app_coach_index', methods: ['GET'])]
    public function index(Request $request,CoachRepository $coachRepository, PaginatorInterface $paginator): Response
    {   
       // Create a query builder that fetches the entities you want to display in your table
       $qb = $coachRepository->createQueryBuilder('c')
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

        if ($this->isGranted('ROLE_OWNER')){
            return $this->render('coach/index.html.twig', [
                'coaches'=> $pagination,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
                'searchQuery' => $q,
            ]);
        }else{
            return $this->render('error_pages/meme.html.twig');
        }
    }

    #[Route('/new', name: 'app_coach_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CoachRepository $coachRepository): Response
    {
        if ($this->isGranted('ROLE_OWNER')){
            $coach = new Coach();
            $form = $this->createForm(CoachType::class, $coach);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $coach->setCreatedBy('front');
                $coachRepository->save($coach, true);

                return $this->redirectToRoute('app_coach_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('coach/new.html.twig', [
                'coach' => $coach,
                'form' => $form,
            ]);
        }else{
            return $this->render('error_pages/meme.html.twig');
        }
    }

    #[Route('/{id}/show', name: 'app_coach_show', methods: ['GET','POST'])]
    public function show(Coach $coach): Response
    {
        if ($this->isGranted('ROLE_OWNER')){
            return $this->render('coach/show.html.twig', [
                'coach' => $coach,
            ]);
        }else{
            return $this->render('error_pages/meme.html.twig');
        }
    }

    #[Route('/{id}/edit', name: 'app_coach_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Coach $coach, CoachRepository $coachRepository): Response
    {
        if ($this->isGranted('ROLE_OWNER')){
            $form = $this->createForm(CoachType::class, $coach);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $coachRepository->save($coach, true);

                return $this->redirectToRoute('app_coach_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('coach/edit.html.twig', [
                'coach' => $coach,
                'form' => $form,
            ]);
        }else{
            return $this->render('error_pages/meme.html.twig');
        }
    }

    #[Route('/{id}', name: 'app_coach_delete', methods: ['POST'])]
    public function delete(Request $request, Coach $coach, CoachRepository $coachRepository): Response
    {   
        if ($this->isGranted('ROLE_OWNER')){
            if ($this->isCsrfTokenValid('delete'.$coach->getId(), $request->request->get('_token'))) {
                $coachRepository->remove($coach, true);
            }

            return $this->redirectToRoute('app_coach_index', [], Response::HTTP_SEE_OTHER);
        }else{
            return $this->render('error_pages/meme.html.twig');
        }
    }
}
