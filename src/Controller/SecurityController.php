<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\SignupType;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * Login page
     *
     * @Route("/login", name="app_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Signup page
     *
     * @Route("/signup", name="app_signup")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function signup(AuthenticationUtils $authenticationUtils, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new User();
        $form = $this->createForm(SignupType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            //Adding to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Merci pour votre inscription. À présent, vous pouvez vous connecter à votre compte.');

            return $this->redirectToRoute('app_photos');
        }

        return $this->render('security/signup.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView()
        ]);
    }

    /**
     * Logout page
     *
     * @Route("/logout", name="app_logout")
     *
     * @return Response
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
