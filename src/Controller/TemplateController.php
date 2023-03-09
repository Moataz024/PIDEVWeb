<?php

namespace App\Controller;

use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\ProfileType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Twig\Environment;
use Vich\UploaderBundle\Handler\UploadHandler;
use function MongoDB\BSON\toJSON;


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
    public function index(Security $security, UserRepository $userRepository): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }
        if(!$this->getUser()->isIsVerified()){
            $this->addFlash('warning', 'A mail has been sent to confirm your email, please reach out to your inbox.');
        }
        return $this->render('template/index.html.twig', [
            'controller_name' => 'TemplateController',
        ]);
    }
    #[Route('/denied', name: 'app_access_denied')]
    public function showErrorPage(): Response
    {
        return $this->render('error_pages/403.html.twig');
    }

    #[Route('/wakanda', name: 'app_dont_do_that_here')]
    public function weDontDoThatHere(): Response
    {
        return $this->render('error_pages/meme.html.twig');
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
    public function profile(Request $request, UserRepository $userRepository, UploadHandler $handler,Security $security): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }
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
    public function profile_back(Request $request, UserRepository $userRepository,UploadHandler $handler, Security $security): Response
    {
        if (!$security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->security->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $handler->upload($user, 'avatarFile');
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_academy_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('profile/profile_back.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/passwordEmail', name: 'app_email_getter')]
    public function getEmailForPassChange(UserRepository $userRepository,Request $request,VerifyEmailHelperInterface $verifyEmailHelper, Environment $twig ): Response
    {
        $user = new User();
        $form = $this->createFormBuilder([])
            ->add('email', EmailType::class, [

            ])
            ->add('submit', SubmitType::class, ['label' => 'Send email'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findByEmail($email);
            if(!$user){
                $this->addFlash('error','There is no user with this email');
            }else {
                $mail = new PHPMailer(true);
                try {
                    $email = $form->get('email')->getData();
                    /*$mail->SMTPDebug = SMTP::DEBUG_SERVER;*/
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'moataz.foudhaili@esprit.tn';
                    $mail->Password = 'sgjlqokbzgztfjrg';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = "Please verify your email";
                    $template = $twig->load('template/anonymous_mail_template.html.twig');
                    $body = $template->render([
                    ]);
                    $mail->msgHTML($body);
                    $mail->send();
                    $this->addFlash('success', 'A mail has been sent to verify that it\'s your email, please reach out to your inbox.');

                } catch (\Exception $ex) {
                    $this->addFlash('error', 'Cannot send email :' . $ex->getMessage());
                }
            }
        }
        return $this->render('security/anonymous_email_getter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/anonym/changePassword', name: 'app_anonymous_password')]
    public function changePasswordAnonymously(Request $request, UserPasswordEncoderInterface $passwordEncoder,UserRepository $userRepository )
    {

        $form = $this->createFormBuilder([])
            ->add('email', EmailType::class, [
            ])
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
            $user = $userRepository->findByEmail($form->get('email')->getData());
            $formData = $form->getData();
                $newEncodedPassword = $passwordEncoder->encodePassword($user, $formData['new_password']);
                $user->setPassword($newEncodedPassword);
                $userRepository->save($user,true);
                $this->addFlash('success', 'Password changed successfully!');
                return $this->redirectToRoute('app_login');
        }

        return $this->render('security/anonymous_change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}

