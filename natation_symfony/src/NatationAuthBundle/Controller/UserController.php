<?php

namespace NatationAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NatationAuthBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
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
     * @Route("/login/test", name="login_test")
     */
    public function testAction()
    {
        $allUsers = $this->getDoctrine()
            ->getRepository(User::class)
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
