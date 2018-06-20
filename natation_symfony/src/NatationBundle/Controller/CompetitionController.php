<?php

namespace NatationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class CompetitionController extends Controller
{
    /**
     * @Route("/compet/all", name="all_competitions")
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function allCompetitionAction()
    {
        return $this->render('@Natation/index.html.twig');
    }
}
