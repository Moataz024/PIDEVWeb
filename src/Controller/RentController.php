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

    #[Route('/new/{id}', name: 'app_rent_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RentRepository $rentRepository,$id,EquipmentRepository $equipRepo): Response
    {

$sid    = "ACfdb76df743ae0e314286200a426727d0";
$token  = "f073db776c28685f3031d898b6ca9c0b";
$twilio = new Client($sid, $token);


        $rent = new Rent();
        $equipment= new Equipment();
        $equipment= $equipRepo->find($id);
        $form = $this->createForm(RentType::class, $rent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rent->setEquipment($equipment);
            $rentRepository->save($rent, true);
 $call = $twilio->calls
            ->create("+21658411086", // to
                     "+12765338087", // from
                     ["url" => "http://127.0.0.1:8000/rent/call"]
            );





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
