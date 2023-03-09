<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Repository\CardRepository;
use App\Entity\User;
use Symfony\Component\Serializer\SerializerInterface;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/cardjson')]
class CardjsonController extends AbstractController
{
    #[Route('/cart/user/{userId}', name: 'cart_by_user')]
#[ParamConverter('user', class: 'App\Entity\User', options: ['id' => 'userId'])]
public function showByUser(User $user, CardRepository $cartRepository,SerializerInterface $serializer ): Response
{
    $cart = $cartRepository->findCartByUser($user->getId());
        
    if (!$cart) {
        throw $this->createNotFoundException('Cart not found.');
    }

    $jsonData = $serializer->serialize($cart, 'json', ['groups' => 'cart']);
    return new JsonResponse($jsonData, 200, [], true);
}
}
