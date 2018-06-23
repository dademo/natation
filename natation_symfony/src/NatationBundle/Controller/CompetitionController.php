<?php

namespace NatationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use NatationBundle\Entity\Competition;
use NatationBundle\Entity\Jugecompetition;
use NatationBundle\Entity\TypeJuge;


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
     * @Route("/compet/show/{competId}/judgeList", name="show_competition_juges", requirements={"competId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function showJudgeCompetitionAction(int $competId)
    {
        $competition = $this->getDoctrine()
            ->getRepository(Competition::class)
            ->find($competId);

        // https://www.doctrine-project.org/api/orm/latest/Doctrine/ORM/EntityRepository.html#method_findBy
        $allJugecompetition = $this->getDoctrine()
            ->getRepository(Jugecompetition::class)
            ->findBy(array(
                'idCompetition' => $competId,
            ),
            array(
                'rang' => 'ASC',
                'idTypejuge' => 'ASC'
            ));

        // Vérifications -> émission d'alertes
        $nJugeArbitre = 0;
        $nJuge = 0;
        $alerts = array();
        $oldRang = null;

        foreach($allJugecompetition as $juge) {
            switch($juge->getIdTypejuge()->getNom()) {
            case 'Juge-arbitre':
                ++$nJugeArbitre;
                break;
            case 'Juge':
                ++$nJuge;
                break;
                // Default: Arbitre inconnu
            }
            if($oldRang === $juge->getRang()) {
                $alerts[] = 'Il y a au moins 2 juges de rang ' . $oldRang;
            }
            $oldRang = $juge->getRang();
        }

        // Tests
        //++$nJuge;
        //++$nJugeArbitre;

        if($nJugeArbitre == 0) {
            $alerts[] = 'Il n\'y a pas de juge-arbitre pour cette compétition (' . $nJugeArbitre . ').  Elle ne pourra pas démarrer';
        }
        if($nJugeArbitre > 1) {
            $alerts[] = 'Il y a trop de juge-arbitres pour cette compétition (' . $nJugeArbitre . ').  Elle ne pourra pas démarrer';
        }

        if($nJuge < 5) {
            $alerts[] = 'Il n\'y a pas assez de juges pour cette compétition (' . $nJuge . '). Elle ne pourra pas démarrer';
        }
        if($nJuge > 5) {
            $alerts[] = 'Il y a trop de juges pour cette compétition (' . $nJuge . '). Elle ne pourra pas démarrer';
        }



        return $this->render('@Natation/Competition/showJudge.html.twig', array(
            'competition' => $competition,
            'allJugeCompetition' => $allJugecompetition,
            'alerts' => $alerts
        ));
    }

    /**
     * @Route("/compet/set/{competId}/judgeList", name="set_competition_juges", requirements={"competId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setJugecompetitionAction(int $competId)
    {
        $competition = $this->getDoctrine()
            ->getRepository(Competition::class)
            ->find($competId);

        // https://www.doctrine-project.org/api/orm/latest/Doctrine/ORM/EntityRepository.html#method_findBy
        /*$allJugecompetition = $this->getDoctrine()
            ->getRepository(Jugecompetition::class)
            ->findBy(
                ''
            );*/

        $rawSql = "SELECT
            jugeCompetition.id
            FROM jugeCompetition
            INNER JOIN utilisateur
                ON utilisateur.id = jugeCompetition.id_utilisateur
            INNER JOIN utilisateur_typeUtilisateur
                ON utilisateur_typeUtilisateur.id_utilisateur = utilisateur.id
            INNER JOIN typeUtilisateur
                ON typeUtilisateur.id = utilisateur_typeUtilisateur.id_typeUtilisateur
            WHERE typeUtilisateur.nom = 'ROLE_JUGE'
            ";

        $stmt = $this->getDoctrine()->getConnection()->prepare($rawSql);
        $stmt->execute([]);
        $_allJugecompetition = $stmt->fetchAll();

        $tmpAllJugecompetition = array();
        foreach($_allJugecompetition as $val) {
            $tmpAllJugecompetition[] = $val['id'];
        }

        $allJugecompetition = $this->getDoctrine()
            ->getRepository(JugeCompetition::class)
            ->findBy(array(
                'id' => $tmpAllJugecompetition
            ));
/*
        $q = $this->getDoctrine()
            ->getRepository(Jugecompetition::class)
            ->createQueryBuilder('JugeCompetition')
            ->innerJoin('NatationBundle\Entity\JugeCompetition', 'utilisateur', 'ON', 'utilisateur.id = jugeCompetition.id_utilisateur')
            ->innerJoin('NatationBundle\Entity\Utilisateur', 'utilisateur_typeUtilisateur', 'utilisateur_typeUtilisateur', 'utilisateur_typeUtilisateur.id_utilisateur = utilisateur.id')
            ->andWhere('typeUtilisateur = \'ROLE_JUGE\'');

            $allJugecompetition = $q->getQuery()->getResult();
*/

        $_allTypeJuge = $this->getDoctrine()
            ->getRepository(TypeJuge::class)
            ->findAll();

        // Tous les types de juge
        $allTypeJuge = array();
        foreach($_allTypeJuge as $typeJuge) {
            $nom = $typeJuge->getNom();
            $allTypeJuge[$nom] = $typeJuge->getId();
        }

        // Tous les juges de la compétition
        $allCompetJuges = array();
        $allCompetJugeArbitres = array();
        foreach($competition->getIdJugecompetition() as $juge) {
            if($juge->getIdTypeJuge()->getNom() == 'Juge') {
                $allCompetJuges[$juge->getId()] = $juge->getRang();
            } else {
                $allCompetJugeArbitres[$juge->getId()] = $juge->getRang();
            }
        }

            //$form["username"]->getData();

        return $this->render('@Natation/Competition/setJugeForm.html.twig', array(
            'competition' => $competition,
            'allJugeCompetition' => $allJugecompetition,
            'allTypeJuge' => $allTypeJuge,
            'allCompetJuge' => $allCompetJuges,
            'allCompetJugeArbitres' => $allCompetJugeArbitres,
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
