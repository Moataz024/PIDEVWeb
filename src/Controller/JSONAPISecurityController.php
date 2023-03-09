<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Vich\UploaderBundle\Handler\UploadHandler;


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
    public function login(Request $request, TokenStorageInterface $tokenStorage,NormalizerInterface $normalizer,ValidatorInterface $validator, UserRepository $userRepository): JsonResponse
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

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
        $currentUser = $user;
        $userArray = [
            "id" => $currentUser->getId(),
            'email' => $currentUser->getEmail(),
            'roles' => $currentUser->getRoles(),
            'nomutilisateur' => $currentUser->getNomutilisateur(),
            'phone' => $currentUser->getPhone(),
            'status' => $currentUser->isStatus(),
            'firstname' => $currentUser->getFirstname(),
            'lastname' => $currentUser->getLastname(),
        ];
            return new JsonResponse(['user' => $normalizer->normalize($userArray)]);
    }



    #[Route('/editUser',name: 'api_edit',methods : ['POST'])]
    public function editUser(Request $request,UserRepository $userRepository, UploadHandler $handler, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        $id = $request->get("id");
        $firstname = $request->get("firstname");
        $password = $request->get("password");
        $email = $request->get("email");
        $lastname = $request->get("lastname");
        $nomutilisateur = $request->get("nomutilisateur");
        $phone = $request->get("phone");

        $user = $userRepository->find($id);

        if($request->files->get("avatar")){
            $file = $request->files->get("avatar");
            $fileName = $file->getClientOriginalName();
            $file->move(
                $fileName
            );
            $user->setAvatarName($fileName);
            $user->setAvatarFile($file);
            $handler->upload($user, 'avatarFile');
        }
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setNomutilisateur($nomutilisateur);
        $user->setPhone($phone);
        if($password != null){
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
        }

        try {
            $userRepository->save($user,true);
            return new JsonResponse("success",200);
        }catch(\Exception $ex){
            return new JsonResponse("fail",$ex->getMessage());
        }

    }


    #[Route('/blockUnblock',name: 'api_admin_block',methods : ['POST'])]
    public function blockUnblock(Request $request,UserRepository $userRepository, UploadHandler $handler, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        $id = $request->get("id");

        $user = $userRepository->find($id);

        if ($user->isStatus())
            $user->setStatus(false);
        else
            $user->setStatus(true);
        try {
            $userRepository->save($user, true);
            return new JsonResponse("success", 200);
        } catch (\Exception $ex) {
            return new JsonResponse("fail", $ex->getMessage());
        }
    }

    #[Route('/users', name: 'api_get_users', methods: ['GET'])]
    public function getUsers(UserRepository $userRepository, SerializerInterface $serializer, NormalizerInterface $normalizer)
    {
        $users = $userRepository->findAll();

        $jsonContent = $normalizer->normalize($users,'json',['groups' => 'users']);
        return new Response(json_encode($jsonContent));

    }


}
