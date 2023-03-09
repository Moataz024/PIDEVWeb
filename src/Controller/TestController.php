<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Builder\BuilderInterface;

class TestController extends AbstractController
{
    #[Route('/test/{id}', name: 'app_test')]
    public function index(BuilderInterface $qrbuilder ,Equipment $eq): Response
    {
        $qrCodeContent = $eq->getName() . ' ' . $eq->getAdress()  . ' ' . $eq->getType() . ' ' .  $eq->getPrice() ;
        return $this->render('front/qr_code.html.twig', [
            'qrCodeImage' =>  $qrCodeContent,
        ]);
        // $qrResult = $qrbuilder
        // ->size(400)
        // ->margin(20)
        // ->data('https://youtube.com/c/latteandcode')
        // ->build();
        // $base64 = $qrResult->getDataUri();
        // $html = '<h1> QRCODE </h1><img  src=" ' . $base64 . '">';
        // return new Response(
        //       '<html><body>' .$html . '</body></html>'

        // );
    }
}