<?php

namespace NatationAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NatationAuthBundle\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UtilisateurController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');


        return $this->render(
            '@NatationAuth/login.html.twig',
            array(
                'last_username' => $helper->getLastUsername(),
                'error'         => $helper->getLastAuthenticationError(),
            )
        );
    }

    /**
     * @Route("/login_check", name="security_login_check")
     * @Method({"POST"})
     */
    public function doLoginAction()
    {
        // Nothing to do
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        // Nothing to do
    }

    /**
     * @Route("/login/testpwd", name="testpwd")
     */
    public function testPwdAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($user == 'anon.') {
            return new Response('Nobody is logged');
        } else {
            $encoder = $this->get('security.password_encoder');
            $pwd = $encoder->encodePassword($user, 'azerty2');

            $user->setPassword($pwd);

            // Saving the user
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
                
            return new Response('Ok');
        }
    }

    /**
     * @Route("/login/test", name="login_test")
     */
    public function testAction()
    {
        $allUsers = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->findAll();

        //return new Response('OK');
        return $this->render(
            '@NatationAuth/all_users.html.twig',
            array(
                'allUsers' => $allUsers
            )
        );
    }

    /**
     * @Route("/user/show", name="show_user")
     * @Security("has_role('ROLE_USER')")
     */
    public function showUserAction()
    {
        /*$allUsers = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->findAll();

        return $this->render(
            '@NatationAuth/all_users.html.twig',
            array(
                'allUsers' => $allUsers
            )
        );*/

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render(
            '@NatationAuth/user/show.html.twig',
            array(
                'user' => $user
            )
        );
    }

    /**
     * @Route("/user/allUsers", name="all_users")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAllUserAction()
    {
        $allUsers = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->findAll();

        //return new Response('OK');
        return $this->render(
            '@NatationAuth/all_users.html.twig',
            array(
                'allUsers' => $allUsers
            )
        );
    }
}
