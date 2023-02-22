<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\ProfileType;
use Symfony\Component\Security\Core\Security;

class TemplateController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }
    #[Route('/template', name: 'app_template')]
    public function index(): Response
    {
        return $this->render('template/index.html.twig', [
            'controller_name' => 'TemplateController',
        ]);
    }

    #[Route('/{id}/profile', name: 'app_profile', methods : ['GET','POST'])]
    public function profile(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->security->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_template', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('profile/profile_front.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/admin/{id}/profile', name: 'app_profile_back', methods : ['GET','POST'])]
    public function profile_back(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->security->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('profile/profile_back.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
