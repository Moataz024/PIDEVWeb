<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class RegistrationController extends AbstractController
{


    #[Route('/register', name: 'app_register')]
    public function register(Environment $twig, Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            $mail = new PHPMailer(true);
            try {
                $email = $form->get('email')->getData();
            //Server settings
/*            $mail->SMTPDebug = SMTP::DEBUG_SERVER;*/
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'prexzcod@gmail.com';
            $mail->Password   = 'agxdusiorgzjsptx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->addAddress($email);
            $signatureComponents = $verifyEmailHelper->generateSignature('app_verify_email',$user->getId(),$user->getEmail(), ['id' => $user->getId()]);
            $signedUrl = $signatureComponents->getSignedUrl();
            $mail->isHTML(true);                       // Set email format to HTML
            $mail->Subject = "Please verify your email";
                $template = $twig->load('template/mail_template.html.twig');
                $body = $template->render([
                    'signedUrl' => $signedUrl,
                ]);
            $mail->msgHTML($body);
            $mail->send();
                $this->addFlash('success', 'A mail has been sent to confirm your email, please reach out to your inbox.');
/*                $authenticator->authenticate($request);*/
                $userAuthenticator->authenticateUser($user,$authenticator,$request);
                return $this->redirectToRoute('app_template');
            }catch(\Exception $ex){
                $this->addFlash('error', 'Cannot send email :'.$ex->getMessage());
                return $this->redirectToRoute('app_register');
        }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator,UserRepository $userRepository, Security $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $security->getUser();
        $user->setIsVerified(true);
        $userRepository->save($user,true);
        $this->addFlash('success', 'Your email address has been verified.');
        sleep(5);
        return $this->redirectToRoute('app_login');
    }
}
