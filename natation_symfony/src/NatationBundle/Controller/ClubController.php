<?php

namespace NatationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

use NatationBundle\Entity\Competition;
use NatationBundle\Entity\Jugecompetition;
use NatationBundle\Entity\TypeJuge;
use NatationBundle\Entity\Utilisateur;
use NatationBundle\Entity\Equipe;
use NatationBundle\Entity\Club;
use NatationBundle\Entity\Personne;


class ClubController extends Controller
{
    /**
     * @Route("/club/all", name="all_clubs")
     * @Security("has_role('ROLE_USER')")
     */
    public function allClubsAction()
    {
        $allClubs = $this->getDoctrine()
        ->getRepository(Club::class)
        ->findAll();

        $rawSql = "SELECT
            COUNT(*) AS nInscrits,
            club.id AS idClub
        FROM club
        INNER JOIN club_personne
            ON club_personne.id_club = club.id
        WHERE club_personne.dateFinInscription IS NULL
            OR club_personne.dateFinInscription >= current_date
        GROUP BY club.id
        ";

        $stmt = $this->getDoctrine()->getConnection()->prepare($rawSql);
        $stmt->execute([]);
        $_nAdherents = $stmt->fetchAll();

        
        $nAdherents = array();
        foreach($_nAdherents as $row) {
            $nAdherents[$row['idclub']] = $row['ninscrits'];
        }

        return $this->render('@Natation/Club/showAll.html.twig', array(
            'allClubs' => $allClubs,
            'nAdherents' => $nAdherents,
        ));
    }

    /**
     * @Route("/club/new", name="new_club")
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function newClubAction()
    {
        $allClubs = $this->getDoctrine()
        ->getRepository(Club::class)
        ->findAll();

        return $this->render('@Natation/Club/showAll.html.twig', array(
            'allClubs' => $allClubs
        ));
    }

    /**
     * @Route("/club/show/{clubId}", name="show_club", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_USER')")
     */
    public function showClubAction($clubId)
    {
        $club = $this->getDoctrine()
        ->getRepository(Club::class)
        ->find($clubId);

        $rawSql = "SELECT
            COUNT(*) AS nInscrits,
            club.id AS idClub
        FROM club
        INNER JOIN club_personne
            ON club_personne.id_club = club.id
        WHERE (club_personne.dateFinInscription IS NULL
            OR club_personne.dateFinInscription >= current_date)
            AND club.id = " . $clubId . "
        GROUP BY club.id
        ";

        $stmt = $this->getDoctrine()->getConnection()->prepare($rawSql);
        $stmt->execute([]);
        //$_nAdherents = $stmt->fetchOne();

        
        $nAdherents = $stmt->fetchColumn(0);

        return $this->render('@Natation/Club/show.html.twig', array(
            'club' => $club,
            'nAdherents' => $nAdherents,
        ));
    }
}