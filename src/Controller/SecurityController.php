<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Repository\CardRepository;

class SecurityController extends AbstractController
{

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             if ($this->isGranted('ROLE_ADMIN')){
                 return $this->redirectToRoute('app_academy_index');
             }else{
                 return $this->redirectToRoute('app_template');
             }
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route('/users', name: 'app_users', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user_management/display.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/change-password', name: 'change_password')]
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder([])
            ->add('current_password', PasswordType::class)
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'New Password'],
                'second_options' => ['label' => 'Confirm New Password'],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Change Password'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            if ($passwordEncoder->isPasswordValid($user, $formData['current_password'])) {
                $newEncodedPassword = $passwordEncoder->encodePassword($user, $formData['new_password']);
                $user->setPassword($newEncodedPassword);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Password changed successfully!');

                return $this->redirectToRoute('app_test');
            } else {
                $form->addError(new FormError('Current password is invalid'));
            }
        }

        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/change-password-front', name: 'change_password_front')]
    public function changePasswordFront(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder([])
            ->add('current_password', PasswordType::class)
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'New Password'],
                'second_options' => ['label' => 'Confirm New Password'],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Change Password'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            if ($passwordEncoder->isPasswordValid($user, $formData['current_password'])) {
                $newEncodedPassword = $passwordEncoder->encodePassword($user, $formData['new_password']);
                $user->setPassword($newEncodedPassword);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Password changed successfully!');

                return $this->redirectToRoute('app_template');
            } else {
                $form->addError(new FormError('Current password is invalid'));
            }
        }

        return $this->render('security/change_password_front.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/toggle-block/{id}', name: 'app_block_unblock',methods: ['POST'])]
    public function toggleBlock(Request $request, User $user, UserRepository $userRepository): Response
    {
        if($this->isGranted('ROLE_ADMIN')){
                if($user->isStatus()){
                    $user->setStatus(false);
                    $userRepository->save($user,true);
                    return $this->redirectToRoute('app_users', [], Response::HTTP_SEE_OTHER);
                }else{
                    $user->setStatus(true);
                    $userRepository->save($user,true);
                    return $this->redirectToRoute('app_users', [], Response::HTTP_SEE_OTHER);
                }

        }else{
            return $this->redirectToRoute('app_access_denied', [], Response::HTTP_SEE_OTHER);
        }
    }
    #[Route('/cart/user/{userId}', name: 'security_cart_by_user')]
    public function showUserCart($userId, CardRepository $cartRepository)
    {
        $cart = $cartRepository->findCartByUser($userId);

        if (!$cart) {
            throw $this->createNotFoundException('Cart not found.');
        }

        return $this->render('card/showcard.html.twig', [
            'cart' => $cart,
        ]);
    }
}
