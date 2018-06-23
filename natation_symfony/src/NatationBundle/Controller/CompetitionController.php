<?php

namespace NatationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use NatationBundle\Entity\Competition;


class CompetitionController extends Controller
{
    /**
     * @Route("/compet/all", name="all_competitions")
     * @Security("has_role('ROLE_USER')")
     */
    public function allCompetitionAction()
    {
        $allCompet = $this->getDoctrine()
            ->getRepository(Competition::class)
            ->findAll();

        return $this->render('@Natation/Competition/showAll.html.twig', array(
            'competitions' => $allCompet
        ));
    }

    /**
     * @Route("/compet/show/{competId}", name="show_competition", requirements={"competId"="\d+"})
     * @Security("has_role('ROLE_USER')")
     */
    public function showCompetitionAction(int $competId)
    {
        $competition = $this->getDoctrine()
            ->getRepository(Competition::class)
            ->find($competId);

        return $this->render('@Natation/Competition/show.html.twig', array(
            'competition' => $competition,
        ));
    }



    /**
     * @Route("/compet/new", name="new_competition")
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function newCompetitionAction()
    {
        $allCompet = $this->getDoctrine()
            ->getRepository(Competition::class)
            ->findAll();

        return $this->render('@Natation/Competition/new.html.twig');
    }
}
