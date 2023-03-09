<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;


#[Route('/api')]
class JSONAPISecurityController extends AbstractController
{
    #[Route('/j/s/o/n/a/p/i/security', name: 'app_j_s_o_n_a_p_i_security')]
    public function index(): Response
    {
        return $this->render('jsonapi_security/index.html.twig', [
            'controller_name' => 'JSONAPISecurityController',
        ]);
    }

    #[Route('/login', name: 'api_login',methods : ['POST'])]
    public function login(Request $request, TokenStorageInterface $tokenStorage,NormalizerInterface $normalizer,ValidatorInterface $validator): JsonResponse
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if(!$email || !$password){
            throw new BadCredentialsException('Invalid email or password');
        }
        $errors = $validator->validate($email, [
            new Email([
                'message' => 'The email "{{ value }}" is not a valid email address.'
            ])
        ]);

        if (count($errors) > 0) {
            throw new ValidatorException($errors);
        }

      /*$userRoles = $this->getUser()->getRoles();*/
        $user = $userRepository->findOneBy(['email' => $email]);
        $token = new UsernamePasswordToken($user, $password, 'main', ['ROLE_USER']);
        $tokenStorage->setToken($token);
        $currentUser = $this->getUser();
        $userArray = [
            'id' => $currentUser->getId(),
            'email' => $currentUser->getEmail(),
            'roles' => $currentUser->getRoles(),
            'nomutilisateur' => $currentUser->getNomutilisateur(),
            'phone' => $currentUser->getPhone(),
            'status' => $currentUser->isStatus(),
        ];

        if ($this->isGranted('ROLE_USER')) {

            return new JsonResponse(['user' => $normalizer->normalize($userArray)]);
        } else {
            return new JsonResponse(['false' => $normalizer->normalize($userArray)]);
        }
    }
}
