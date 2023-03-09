<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Equipment;
use App\Form\EquipmentType;
use App\Repository\CategoryRepository;
use App\Repository\SuppliersRepository;
use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Knp\Snappy\Pdf;

#[Route('/equipment')]
class EquipmentController extends AbstractController
{
    #[Route('/', name: 'app_equipment_index', methods: ['GET'])]
    public function index(EquipmentRepository $equipmentRepository): Response
    {
      
        return $this->render('equipment/index.html.twig', [
            'equipment' => $equipmentRepository->findAll(),
        ]);
    }

    #[Route('/pdf', name: 'app_equipment_index_pdf', methods: ['GET'])]
    public function indexPdf(EquipmentRepository $equipmentRepository, Pdf $snappy): Response
    {
        $html = $this->renderView('equipment/pdf.html.twig', [
            'equipment' => $equipmentRepository->findAll(),
        ]);

        $pdf = $snappy->getOutputFromHtml($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="equipment.pdf"',
        ]);
    }
//     #[Route('/listeE', name: 'app_equipment_liste', methods: ['GET'])]
//     public function listeE(EquipmentRepository $equipmentRepository): Response
//    {
//     $pdfOptions = new Options();
//     $pdfOptions->set('defaultFont', 'Arial');
//     $dompdf = new Dompdf($pdfOptions);
//     $equipment = $equipmentRepository->findAll();

//     $html = $this->renderView('equipment/listeE.html.twig', [
//         'equipment' => $equipmentRepository->findAll(),
//     ]);
//     $dompdf->loadHtml($html);
//     $dompdf->setPaper('A4', 'portrait');
//     $dompdf->render();
//     $dompdf->stream("mypdf.pdf", [
//         "Attachment" => false
//     ]);
// }
    #[Route('/equipments_mobile', name: 'app_equipments_mobile', methods: ['GET'])]
    public function equipments_mobile_all(EquipmentRepository $equipmentRepository , NormalizerInterface $Normalizer): Response
    {
        $equipments = $equipmentRepository->findAll();
        
        $jsonContent = $Normalizer->normalize($equipments , 'json', ['groups' => ['category', 'equipments','suppliers']]);

        return new Response(json_encode($jsonContent));
  
    }
    #[Route('/delete_equipments_mobile', name: 'app_delete_equipments_mobile', methods: ['GET'])]
    public function delete_equipments_mobile(Request $request,EquipmentRepository $equipmentRepository , NormalizerInterface $Normalizer): Response
    {
        $equipment = $equipmentRepository->find((int)$request->get("id"));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($equipment);
        $entityManager->flush();
        $equipments = $equipmentRepository->findAll();
        
        $jsonContent = $Normalizer->normalize($equipments , 'json', ['groups' => ['category', 'equipments','suppliers']]);

        return new Response(json_encode($jsonContent));
  
    }
    
    #[Route('/update_equipments_mobile', name: 'app_update_equipments_mobile', methods: ['GET'])]
    public function update_equipments_mobile(Request $request,EquipmentRepository $equipmentRepository,SuppliersRepository $suppliersRepository , CategoryRepository $categoryRepository ,NormalizerInterface $Normalizer ): Response
    {
        $supplier = $suppliersRepository->find((int)$request->get("id_s"));
        $category = $categoryRepository->find((int)$request->get("id_c"));
        $equipment = $equipmentRepository->find((int)$request->get("id"));
        $equipment->setName($request->get("name"));
        $equipment->setAdress($request->get("adress"));
        $equipment->setType($request->get("type"));
        $equipment->setQuantity($request->get("quantite"));
        $equipment->setPrice($request->get("price"));
        $equipment->setSuppliers($supplier);
        $equipment->setCategory($category);
        $equipment->setImage("https://previews.123rf.com/images/lightwise/lightwise1301/lightwise130100068/17472622-sports-equipment.jpg");
        $entityManager = $this->getDoctrine()->getManager();
        $equipmentRepository->save($equipment, true);
        $equipments = $equipmentRepository->findAll();
        $jsonContent = $Normalizer->normalize($equipments , 'json', ['groups' => ['category', 'equipments','suppliers']]);
        return new Response(json_encode($jsonContent));   
    }
    #[Route('/add_equipments_mobile', name: 'app_add_equipments_mobile', methods: ['GET'])]
    public function add_equipments_mobile(Request $request,EquipmentRepository $equipmentRepository,SuppliersRepository $suppliersRepository , CategoryRepository $categoryRepository ,NormalizerInterface $Normalizer ): Response
    {
        $supplier = $suppliersRepository->find((int)$request->get("id_s"));
        $category = $categoryRepository->find((int)$request->get("id_c"));
        $equipment = new equipment();


        $equipment->setName($request->get("name"));
        $equipment->setAdress($request->get("adress"));
        $equipment->setType($request->get("type"));
        $equipment->setQuantity($request->get("quantite"));
        $equipment->setPrice($request->get("price"));
        $equipment->setSuppliers($supplier);
        $equipment->setCategory($category);
        $equipment->setImage("https://previews.123rf.com/images/lightwise/lightwise1301/lightwise130100068/17472622-sports-equipment.jpg");
        $entityManager = $this->getDoctrine()->getManager();
        $equipmentRepository->save($equipment, true);
        $entityManager->flush();

        $equipments = $equipmentRepository->findAll();
        $jsonContent = $Normalizer->normalize($equipments , 'json', ['groups' => ['category', 'equipments','suppliers']]);
        return new Response(json_encode($jsonContent));
      
    }
    #[Route('/category/{id}', name: 'app_equipment_index_category_front', methods: ['GET'])]
    public function index_ParCategory_Front(EquipmentRepository $equipmentRepository,$id): Response
    {
        return $this->render('front/equipement.html.twig', [
            'equipment' => $equipmentRepository->findEquipmentByIdCategory($id),
        ]);
    }

    #[Route('/new', name: 'app_equipment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EquipmentRepository $equipmentRepository): Response
    {
        $equipment = new Equipment();
        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $equipmentRepository->save($equipment, true);

            return $this->redirectToRoute('app_equipment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipment/new.html.twig', [
            'equipment' => $equipment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipment_show', methods: ['GET'])]
    public function show(Equipment $equipment): Response
    {
        return $this->render('equipment/show.html.twig', [
            'equipment' => $equipment,
        ]);
    }
    #[Route('/{id}/front', name: 'app_equipment_show_front', methods: ['GET'])]
    public function showF(Equipment $equipment): Response
    {
        return $this->render('equipment/detail.html.twig', [
            'equipment' => $equipment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipment $equipment, EquipmentRepository $equipmentRepository): Response
    {
        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $equipmentRepository->save($equipment, true);

            return $this->redirectToRoute('app_equipment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipment/edit.html.twig', [
            'equipment' => $equipment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipment_delete', methods: ['POST'])]
    public function delete(Request $request, Equipment $equipment, EquipmentRepository $equipmentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipment->getId(), $request->request->get('_token'))) {
            $equipmentRepository->remove($equipment, true);
        }

        return $this->redirectToRoute('app_equipment_index', [], Response::HTTP_SEE_OTHER);
    }
    // #[Route('/pdf/{id}', name: 'app_equipment_pdf')]
    // public function PDF()
    // {
    //     // Configure Dompdf according to your needs
    //     $pdfOptions = new Options();
    //     $pdfOptions->set('defaultFont', 'Arial');
        
    //     // Instantiate Dompdf with our options
    //     $dompdf = new Dompdf($pdfOptions);
        
    //     // Retrieve the HTML generated in our twig file
    //     $html = $this->renderView('equipment/mypdf.html.twig', [
    //         'title' => "Welcome to our PDF Test"
    //     ]);
        
    //     // Load HTML to Dompdf
    //     $dompdf->loadHtml($html);
        
    //     // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    //     $dompdf->setPaper('A4', 'portrait');

    //     // Render the HTML as PDF
    //     $dompdf->render();

    //     // Store PDF Binary Data
    //     $output = $dompdf->output();
        
    //     // In this case, we want to write the file in the public directory
    //     $publicDirectory = $this->get('kernel')->getProjectDir() . '/public';
    //     // e.g /var/www/project/public/mypdf.pdf
    //     $pdfFilepath =  $publicDirectory . '/mypdf.pdf';
        
    //     // Write file to the desired path
    //     file_put_contents($pdfFilepath, $output);
        
    //     // Send some text response
    //     return new Response("The PDF file has been succesfully generated !");
    // }
#[Route('/equipment/{id}/like', name: 'equipment_like')]
    #[Route('/equipment/{id}/dislike', name: 'equipment_dislike')]
    public function likeOrDislike(Equipment $equipment, Request $request): Response
    {
        if ($request->get('_route') === 'equipment_like') {
            $equipment->setLikes($equipment->getLikes() + 1);
        } else {
            $equipment->setDislikes($equipment->getDislikes() + 1);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($equipment);
        $entityManager->flush();

        return $this->redirectToRoute('app_equipment_index_category_front', ['id' => $equipment->getId()]);
    }

}
