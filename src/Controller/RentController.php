<?php

namespace App\Controller;

use App\Entity\Rent;
use App\Form\RentType;
use App\Entity\Equipment;
use App\Repository\RentRepository;
use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Constraints\DateTime;
use Monolog\DateTimeImmutable;
use Symfony\Component\Serializer\Serializer;
#[Route('/rent')]
class RentController extends AbstractController
{
    #[Route('/', name: 'app_rent_index', methods: ['GET'])]
    public function index(RentRepository $rentRepository): Response
    {
        return $this->render('rent/index.html.twig', [
            'rents' => $rentRepository->findAll(),
        ]);
    }
    #[Route('/rents_mobile', name: 'app_rents_mobile', methods: ['GET'])]
    public function rents_mobile_all(RentRepository $rentRepository , NormalizerInterface $Normalizer): Response
    {
        $rents = $rentRepository->findAll();
        
        $jsonContent = $Normalizer->normalize($rents , 'json', ['groups' => ['rents']]);

        return new Response(json_encode($jsonContent));
  
    }
    #[Route('/delete_rents_mobile', name: 'app_delete_rents_mobile', methods: ['GET'])]
    public function delete_rents_mobile(Request $request,RentRepository $rentRepository , NormalizerInterface $Normalizer): Response
    {
        $rent = $rentRepository->find((int)$request->get("id"));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($rent);
        $entityManager->flush();
        $rents = $rentRepository->findAll();
        $jsonContent = $Normalizer->normalize($rents , 'json', ['groups' => ['rents']]);
        return new Response(json_encode($jsonContent));
  
    }
    #[Route('/add_rents_mobile', name: 'app_add_rents_mobile', methods: ['GET'])]
    public function add_rents_mobile(Request $request, RentRepository $rentRepository,EquipmentRepository $equipRepo , NormalizerInterface $Normalizer): Response
    {
          $rent = new Rent();
           
$DateTimeImmutable = new DateTimeImmutable($request->get("date"), null);

    $equipment= $equipRepo->find((int)$request->get("id_e"));
        $rent->setEquipment($equipment);
        $rent->setRentAt($DateTimeImmutable);
        $entityManager = $this->getDoctrine()->getManager();
        $rentRepository->save($rent, true);
        $entityManager->flush();
        $sid    = "ACfdb76df743ae0e314286200a426727d0";
$token  = "a1f004934c47f30b3aeb35bb583b1feb";
$twilio = new Client($sid, $token);
$call = $twilio->calls
->create("+21658411086", // to
         "+12765338087", // from
         ["url" => "http://127.0.0.1:8000/rent/call"]
);
        $rents = $rentRepository->findAll();
        $jsonContent = $Normalizer->normalize($rents , 'json', ['groups' => ['rents']]);
        return new Response(json_encode($jsonContent));
  
    }
    #[Route('/new/{id}', name: 'app_rent_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RentRepository $rentRepository,$id,EquipmentRepository $equipRepo): Response
    {

        $sid    = "ACfdb76df743ae0e314286200a426727d0";
        $token  = "a1f004934c47f30b3aeb35bb583b1feb";
        $twilio = new Client($sid, $token);


        $rent = new Rent();
        $equipment= new Equipment();
        $equipment= $equipRepo->find($id);
        $form = $this->createForm(RentType::class, $rent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rent->setEquipment($equipment);
            $rentRepository->save($rent, true);
//            $call = $twilio->calls
//            ->create("+21658411086", // to
//                     "+12765338087", // from
//                     ["url" => "http://127.0.0.1:8000/rent/call"]
//            );





            return $this->redirectToRoute('app_category_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rent/new.html.twig', [
            'rent' => $rent,
            'form' => $form,
        ]);
    }

    #[Route('/call', name: 'app_call', methods: ['GET'])]
    public function call(): Response
    {
        return $this->render('front/call.xml', [
           
        ]);
    }

    #[Route('/{id}', name: 'app_rent_show', methods: ['GET'])]
    public function show(Rent $rent): Response
    {
        return $this->render('rent/show.html.twig', [
            'rent' => $rent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rent_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rent $rent, RentRepository $rentRepository): Response
    {
        $form = $this->createForm(RentType::class, $rent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rentRepository->save($rent, true);

            return $this->redirectToRoute('app_rent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rent/edit.html.twig', [
            'rent' => $rent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rent_delete', methods: ['POST'])]
    public function delete(Request $request, Rent $rent, RentRepository $rentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rent->getId(), $request->request->get('_token'))) {
            $rentRepository->remove($rent, true);
        }

        return $this->redirectToRoute('app_rent_index', [], Response::HTTP_SEE_OTHER);
    }
}
