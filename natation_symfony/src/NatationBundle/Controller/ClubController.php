<?php

namespace NatationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\FormError;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use NatationBundle\Entity\Competition;
use NatationBundle\Entity\Jugecompetition;
use NatationBundle\Entity\TypeJuge;
use NatationBundle\Entity\Utilisateur;
use NatationBundle\Entity\Equipe;
use NatationBundle\Entity\Club;
use NatationBundle\Entity\Personne;
use NatationBundle\Entity\Note;


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


        $allNotes = $this->getDoctrine()
        ->getRepository(Note::class)
        ->findAll();

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


    /**
     * @Route("/club/show/{clubId}", name="show_club_members", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function showClubMembersAction(int $clubId)
    {
        
    }

    /**
     * @Route("/club/set/{clubId}/nom", name="set_club_name", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setClubNameAction(Request $request, int $clubId)
    {
        $club = $this->getDoctrine()
        ->getRepository(Club::class)
        ->find($clubId);

        $form = $this->createFormBuilder($club)
            ->add('nom', TextType::class, array('label' => 'New name'))
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $club = $form->getData();
        
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($club);
                $entityManager->flush();

                return $this->redirectToRoute('show_club', array(
                    'clubId' => $club->getId()
                ));
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException  $ex) {
                $form->addError(new FormError('Ce club existe déjà'));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }


        return $this->render('@Natation/Club/update.html.twig', array(
            'club' => $club,
            'form_title' => 'Modification du nom du club ' . $club->getNom(),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/club/set/{clubId}/president", name="set_club_president", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setClubPresidentAction(Request $request, int $clubId)
    {
        $club = $this->getDoctrine()
        ->getRepository(Club::class)
        ->find($clubId);

        $form = $this->createFormBuilder($club)
            ->add('idDirigent', EntityType::class, array('class' => 'NatationBundle:Personne', 'label' => 'New president'))
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $club = $form->getData();
        
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($club);
                $entityManager->flush();

                return $this->redirectToRoute('show_club', array(
                    'clubId' => $club->getId()
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }


        return $this->render('@Natation/Club/update.html.twig', array(
            'club' => $club,
            'form_title' => 'Modification du président du club ' . $club->getNom(),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/club/set/{clubId}/lieu", name="set_club_location", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setClubLieuAction(Request $request, int $clubId)
    {
        $club = $this->getDoctrine()
        ->getRepository(Club::class)
        ->find($clubId);

        $form = $this->createFormBuilder($club)
            ->add('idLieu', EntityType::class, array('class' => 'NatationBundle:Lieu', 'label' => 'New location'))
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $club = $form->getData();
        
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($club);
                $entityManager->flush();

                return $this->redirectToRoute('show_club', array(
                    'clubId' => $club->getId()
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }


        return $this->render('@Natation/Club/update.html.twig', array(
            'club' => $club,
            'form_title' => 'Modification de l\'emplacement du club ' . $club->getNom(),
            'form' => $form->createView(),
        ));
    } 
}