<?php

namespace NatationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('@Natation/index.html.twig');
    }

    /**
     * @Route("/toto", name="testpage")
     */
    public function totoAction()
    {
        return new Response('TOTO');
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
