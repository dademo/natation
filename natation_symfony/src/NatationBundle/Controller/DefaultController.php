<?php

namespace NatationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        return $this->render('@Natation/index.html.twig');
    }

    /**
     * @Route("/test/{testVal}", name="test", requirements={"testVal"="\d+"})
     * @Method({"GET","POST"})
     */
    public function testAction($testVal = 10)
    {
        return new Response('Hello test _' . $testVal . '_ !');
    }
}
