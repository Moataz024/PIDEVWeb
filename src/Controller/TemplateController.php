<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\ProfileType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Handler\UploadHandler;

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
    #[Route('/denied', name: 'app_access_denied')]
    public function showErrorPage(): Response
    {
        return $this->render('error_pages/403.html.twig');
    }

    #[Route('/suspended', name: 'app_suspended')]
    public function showSuspendedPage(TokenStorageInterface $tokenStorage): Response
    {
        $tokenStorage->setToken(null);

        // Invalidate the session
        $this->get('session')->invalidate();
        return $this->render('error_pages/account_blocked.html.twig');
    }

    #[Route('/notfound', name : 'not_found')]
    public function notFound(): Response
    {
        return $this->render('error_pages/not_found.html.twig');
    }

    #[Route('/{id}/profile', name: 'app_profile', methods : ['GET','POST'])]
    public function profile(Request $request, UserRepository $userRepository, UploadHandler $handler): Response
    {
        $user = $this->security->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $handler->upload($user, 'avatarFile');
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
