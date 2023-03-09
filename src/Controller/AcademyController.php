<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\Academy;
use App\Entity\User;
use App\Form\AcademyType;
use App\Form\ApplyType;
use App\Repository\AcademyRepository;
use App\Repository\ApplicationRepository;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Application;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;


#[Route('/academy')]
class AcademyController extends AbstractController
{
    #[Route('/', name: 'app_academy_index', methods: ['GET'])]
    public function index(Request $request, AcademyRepository $academyRepository, PaginatorInterface $paginator, Security $security): Response
    {
        // Get the search query from the request
    $searchQuery = $request->query->get('q');

    // Create a query builder that fetches the entities you want to display in your table
    $qb = $academyRepository->createQueryBuilder('a')
        ->where('a.createdBy = :createdBy')
        ->setParameter('createdBy', 'front')
        ->orderBy('a.id', 'ASC'); // default order

    // Add sorting based on the query parameters
    $sortField = $request->query->get('sortField', 'id');
    $sortDirection = $request->query->get('sortDirection', 'asc');
    $qb->orderBy("a.$sortField", $sortDirection);

    // Add search filter if search query is present
    if ($searchQuery) {
        $qb->andWhere('a.name LIKE :searchQuery')
            ->setParameter('searchQuery', "%$searchQuery%");
    }

    // Paginate the results
    $pagination = $paginator->paginate(
        $qb, // query builder
        $request->query->getInt('page', 1), // current page number
        10 // maximum number of results per page
    );

    if ($this->isGranted('ROLE_OWNER')){
        return $this->render('academy/index.html.twig', [
            'academies'=> $pagination,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'searchQuery' => $searchQuery, // Pass the search query to the template
        ]);
    }else{
        return $this->render('academy/client.html.twig', [
            'academies' => $pagination,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'searchQuery' => $searchQuery, // Pass the search query to the template
        ]);
    }
}
    
    // #[Route('/', name: 'app_academy_index', methods: ['GET'])]
    // public function index(AcademyRepository $academyRepository,Security $security): Response
    // {
       
    //     if ($this->isGranted('ROLE_OWNER')){
    //         return $this->render('academy/index.html.twig', [
    //             'academies'=> $academyRepository->findBy([
    //                 'createdBy' => 'front',
    //             ])
    //         ]);
    //     }else{
    //         return $this->render('academy/client.html.twig', [
    //             'academies' => $academyRepository->findBy([
    //                 'createdBy' => 'front',
    //             ])
    //         ]);
    //     }
    // }
    
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
        if ($this->isGranted('ROLE_OWNER')){
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
        }else{
            return $this->render('error_pages/meme.html.twig');
        }
    }

    #[Route('/{id}', name: 'app_academy_delete', methods: ['POST'])]
    public function delete(Request $request, Academy $academy, AcademyRepository $academyRepository): Response
    {
        if ($this->isGranted('ROLE_OWNER')){    
            if ($this->isCsrfTokenValid('delete'.$academy->getId(), $request->request->get('_token'))) {
                $academyRepository->remove($academy, true);
            }

            return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
        }else{
            return $this->render('error_pages/meme.html.twig');
        }
    }
    
    // #[Route('/{id}/apply', name: 'app_academy_apply', methods: ['GET','POST'])]
    // public function Apply(Request $request, User $user, Academy $academy, AcademyRepository $academyRepository): Response
    // {
    //     $form = $this->createForm(ApplyType::class, $academy);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $academyRepository->save($academy, true);
            
    //         return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('academy/apply.html.twig', [
    //         'academy' => $academy,
    //         'form' => $form,
    //     ]);
    // }
   
    
    #[Route('/{id}/apply', name: 'app_academy_apply', methods: ['GET','POST'])]
    public function application(Request $request,Academy $academy,ApplicationRepository $apprepo, Security $security)
    {
        
        $app = new Application();
        $app->setAcademy($academy);
        $user = $security->getUser();
        $app->setUser($user);
        $form = $this->createForm(ApplyType::class, $app);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $app->getAppname();
            $filtredMessage = $this->badwords($message);
            $app->setAppname($filtredMessage);            
            $apprepo->save($app, true);
            $applicationId = $app->getId();
            return $this->redirectToRoute('app_academy_receipt', ['id' => $applicationId]);
            // return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
        }
        
        
        return $this->render('academy/apply.html.twig',[
            'application' => $app,
            'form' => $form->createView(),
            'academy' => $academy,
        ]);
          
    }
    #[Route('/{id}/apply/receipt', name: 'app_academy_receipt', methods: ['GET','POST'])]
    public function receipt(Application $application)
    {
        $user = $application->getUser();
        return $this->render('academy/receipt.html.twig', [
            'application' => $application,
            'userId' => $user->getId(),
        ]);
        
    }
    #[Route('/{id}/apply/receipt/pdf', name: 'app_academy_pdf', methods: ['GET','POST'])]
    public function pdf(Application $application, Pdf $snappyPdf)
    {
        $user = $application->getUser();
        $html = $this->renderView('academy/pdftemplate.html.twig', [
            'application' => $application,
            'userId' => $user->getId(),
        ]);
        $filename = sprintf('receipt_%s.pdf', $application->getId());

        return new Response(
            $snappyPdf->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    function badwords($message){
        $badwords = array("stupid","idiot","bad");
        $filter = array("*****","*******","**");
        $message = str_replace($badwords,$filter,$message);
        return $message;

    }
    
    // #[Route('/{id}/apply', name: 'app_academy_apply', methods: ['GET','POST'])]
    // public function apply(Request $request, Academy $academy)
    // {
    //     // get the currently logged-in user
    //     $user = new User();
    //     // $user = $this->getUser();

    //     if (!$user) {
    //         return $this->redirectToRoute('login');
    //     }

    //     // create a new Application entity
    //     $application = new Application();

    //     // set the Academy and User entities
    //     $application->setAcademy($academy);
    //     $application->setUser($user);

    //     // get the User entity data
    //     $name = $user->getNomutilisateur();
    //     $email = $user->getEmail();
    //     $phone = $user->getPhone();

    //     // create the form with the Application entity data
    //     $form = $this->createForm(ApplyType::class, $application);
    //         // ->add('appName')
    //         // ->add('appAge')
    //         // ->getForm();

    //     // handle form submission
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // save the Application entity
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($application);
    //         $entityManager->flush();

    //         // redirect to the homepage or another appropriate page
    //         return $this->render('academy/index.html.twig');
    //     }

    //     return $this->render('academy/apply.html.twig', [
    //         'form' => $form
    //         // 'name' => $name,
    //         // 'email' => $email,
    //         // 'phone' => $phone,
    //         // 'academy' => $academy->getName(),
    //     ]);
    // }

}
