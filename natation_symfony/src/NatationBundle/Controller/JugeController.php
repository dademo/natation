<?php

namespace NatationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class JugeController extends Controller
{
    /**
     * @Route("/juge/compet", name="juge_compet")
     * @Security("has_role('ROLE_JUGE')")
     */
    public function jugeCompetAction()
    {
        return $this->render('@Natation/index.html.twig');
    }
}
