<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

#[Route('/api')]
class JSONAPIRegisterController extends AbstractController
{
    protected array $roles = [];
    #[Route('/j/s/o/n/a/p/i/register', name: 'app_j_s_o_n_a_p_i_register')]
    public function index(): Response
    {
        return $this->render('jsonapi_register/index.html.twig', [
            'controller_name' => 'JSONAPIRegisterController',
        ]);
    }

    #[Route('/register',methods : ['POST'])]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $phone = $request->request->get('phone');
        $nomutilisateur = $request->request->get('nomutilisateur');
        $this->roles [] = $request->request->get('roles');
        $lastname = $request->request->get('firstname');
        $firstname = $request->request->get('lastname');

        
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse(['success' => false, 'message' => 'Email exists']);
        }


        $user = new User();
        $user->setEmail($email);


        $encodedPassword = $passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encodedPassword);
        $user->setNomutilisateur($nomutilisateur);
        $user->setPhone($phone);
        $user->setPassword($encodedPassword);
        $user->setRoles($this->roles);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setStatus(false);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $this->roles = [];

        return new JsonResponse(['success' => true, 'message' => 'User successfully registered']);
    }
}
