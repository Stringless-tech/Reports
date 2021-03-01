<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $userRepo;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        $users = $this->userRepo->findBy(['company' => $this->getUser()->getCompany()]);

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/create", name="user-create")
     */
    public function create(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        return $this->form($user,$request,$passwordEncoder);
    }

    /**
     * @Route("/user/update/{id}", name="user-update")
     */
    public function update($id,Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->userRepo->find($id);
        return $this->form($user,$request,$passwordEncoder);
    }

    public function form($user,$request,$passwordEncoder)
    {
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $loggedUserCompany = $this->getUser()->getCompany();
            $em = $this->getDoctrine()->getManager();
            $user->setCompany($loggedUserCompany);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('user'));
        }

        return $this->render('user/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/remove/{id}", name="user-remove")
     */
    public function remove($id)
    {
        $user = $this->userRepo->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * @Route("/user/show/{id}", name="user-show")
     */
    public function show($id)
    {
        $user = $this->userRepo->find($id);
        return $this->render('user/show.html.twig',[
            'user' => $user
        ]);
    }
}

