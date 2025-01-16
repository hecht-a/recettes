<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Http\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct($em);
    }

    #[Route(path: '/register', name: 'auth_register')]
    public function register(Request $request): Response
    {
        $loggedInUser = $this->getUser();
        if ($loggedInUser) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('password')) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $form->get('password')->getData()));
            }

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash(
                'success',
                [
                    'title' => 'Compte',
                    'description' => 'Votre compte a été créé avec succès',
                ]
            );

            return $this->redirectToRoute('auth_login');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
