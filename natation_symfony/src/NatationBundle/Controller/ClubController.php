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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use NatationBundle\Entity\Competition;
use NatationBundle\Entity\Jugecompetition;
use NatationBundle\Entity\TypeJuge;
use NatationAuthBundle\Entity\Utilisateur;
use NatationBundle\Entity\Equipe;
use NatationBundle\Entity\Club;
use NatationBundle\Entity\Personne;
use NatationBundle\Entity\Note;
use NatationBundle\Entity\ClubPersonne;


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

        $rawSql = 'SELECT
            SUM(
				CASE
					WHEN club_personne.id_personne IS NOT NULL THEN 1
					ELSE 0
				END
			) AS nInscrits,
            club.id AS idClub
        FROM club
        LEFT JOIN club_personne
            ON club_personne.id_club = club.id
        WHERE club_personne.dateFinInscription IS NULL
            OR club_personne.dateFinInscription >= current_date
        GROUP BY club.id
		ORDER BY club.id ASC
        ';

        $stmt = $this->getDoctrine()->getConnection()->prepare($rawSql);
        $stmt->execute([]);
        $_nAdherents = $stmt->fetchAll();


        $nAdherents = array();
        foreach ($_nAdherents as $row) {
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
    public function newClubAction(Request $request)
    {
        $club = new Club();

        $form = $this->createFormBuilder($club)
            ->add('nom', TextType::class, array('label' => 'Name'))
            ->add('idDirigent', EntityType::class, array('label' => 'Leader', 'class' => 'NatationBundle:Personne'))
            ->add('idLieu', EntityType::class, array('label' => 'Location', 'class' => 'NatationBundle:Lieu'))
            ->add('create', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $club = $form->getData();

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($club);
                $entityManager->flush();

                return $this->redirectToRoute('all_clubs');
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }


        return $this->render('@Natation/Club/update.html.twig', array(
            'form_title' => 'Création d\'un nouveau club',
            'form' => $form->createView(),
            'returnPageUrl' => $this->generateUrl('all_clubs'),
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
        /*
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
                ";*/

        $rawSql = 'SELECT
            SUM(
				CASE
					WHEN club_personne.id_personne IS NOT NULL THEN 1
					ELSE 0
				END
			) AS nInscrits,
            club.id AS idClub
        FROM club
        LEFT JOIN club_personne
            ON club_personne.id_club = club.id
        WHERE club_personne.dateFinInscription IS NULL
            OR club_personne.dateFinInscription >= current_date
            AND club.id = ' . $clubId . '
        GROUP BY club.id
        ';

        $stmt = $this->getDoctrine()->getConnection()->prepare($rawSql);
        $stmt->execute([]);
        //$_nAdherents = $stmt->fetchOne();


        $nAdherents = $stmt->fetchColumn(0);

        return $this->render('@Natation/Club/show.html.twig', array(
            'club' => $club,
            'nAdherents' => $nAdherents,
            'returnPageUrl' => $this->generateUrl(
                'all_clubs'
            ),
        ));
    }

    /**
     * @Route("/club/show/{clubId}/membres", name="show_club_membres", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function showClubMembresAction($clubId)
    {
        /*$allPersonnes = $this->getDoctrine()
        ->getRepository(Personne::class)
        ->findBy(array(
            'idClubPersonne.dateFinInscription' => array(
                '>= currentDate'
            )
        ));*/


        $rawSql = "SELECT
            club_personne.id_personne as idPersonne
        FROM club
        INNER JOIN club_personne
            ON club_personne.id_club = club.id
        WHERE (club_personne.dateFinInscription IS NULL
            OR club_personne.dateFinInscription >= current_date)
            AND club.id = " . $clubId . "
        ";

        $stmt = $this->getDoctrine()->getConnection()->prepare($rawSql);
        $stmt->execute();
        $_allPersonnes = $stmt->fetchAll();

        $tmpAllPersonnes = [];

        foreach ($_allPersonnes as $row) {
            $tmpAllPersonnes[] = $row['idpersonne'];
        }

        $allPersonnes = $this->getDoctrine()
            ->getRepository(Personne::class)
            ->findBy(array(
                'id' => $tmpAllPersonnes
            ));

        return $this->render('@Natation/Club/show_adherents.html.twig', array(
            'allPersonnes' => $allPersonnes,
            'clubId' => $clubId,
            'returnPageUrl' => $this->generateUrl(
                'show_club', array(
                    'clubId' => $clubId
                )
            ),
        ));
    }

    /**
     * @Route("/club/set/{clubId}/nom", name="set_club_nom", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setClubNomAction(Request $request, $clubId)
    {
        $club = $this->getDoctrine()
            ->getRepository(Club::class)
            ->find($clubId);

        $form = $this->createFormBuilder($club)
            ->add('nom', TextType::class, array('label' => 'New club name'))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
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
            'form_title' => 'Modification du nom du club ' . $club->getNom(),
            'form' => $form->createView(),
            'returnPageUrl' => $this->generateUrl(
                'show_club_membres', array(
                    'clubId' => $clubId
                )
            ),
        ));
    }


    /**
     * @Route("/club/set/{clubId}/dirigent", name="set_club_dirigent", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setClubDirigentAction(Request $request, $clubId)
    {
        $club = $this->getDoctrine()
            ->getRepository(Club::class)
            ->find($clubId);

        $form = $this->createFormBuilder($club)
            ->add('idDirigent', EntityType::class, array('label' => 'New president', 'class' => 'NatationBundle:Personne'))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('show_club', array(
                    'clubId' => $club->getId()
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render('@Natation/Club/update.html.twig', array(
            'form_title' => 'Modification du dirigent du club ' . $club->getNom(),
            'form' => $form->createView(),
            'returnPageUrl' => $this->generateUrl(
                'show_club_membres', array(
                    'clubId' => $clubId
                )
            ),
        ));
    }

    /**
     * @Route("/club/set/{clubId}/lieu", name="set_club_lieu", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setClubLieuAction(Request $request, $clubId)
    {
        $club = $this->getDoctrine()
            ->getRepository(Club::class)
            ->find($clubId);

        $form = $this->createFormBuilder($club)
            ->add('idLieu', EntityType::class, array('label' => 'New club location', 'class' => 'NatationBundle:Lieu'))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('show_club', array(
                    'clubId' => $club->getId()
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render('@Natation/Club/update.html.twig', array(
            'form_title' => 'Modification de l\'emplacement du club' . $club->getNom(),
            'form' => $form->createView(),
            'returnPageUrl' => $this->generateUrl(
                'show_club_membres', array(
                    'clubId' => $clubId
                )
            ),
        ));
    }


    /**
     * @Route("/personne/set/{personneId}/dateFinInscription", name="set_personne_dateFin", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setPersonneDatefininscription(Request $request, $personneId)
    {
        $personne = $this->getDoctrine()
            ->getRepository(Personne::class)
            ->find($personneId);

        $clubPersonne = $personne->getCurrIdClubPersonne();

        if (isset($clubPersonne) && $clubPersonne->getDatefininscription() === null) {

            $form = $this->createFormBuilder($clubPersonne)
                ->add('datefininscription', DateType::class, array('label' => 'Fin d\'inscription de la personne'))
                ->add('save', SubmitType::class)
                ->getForm();


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $clubPersonne = $form->getData();

                try {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($clubPersonne);
                    $entityManager->flush();

                    return $this->redirectToRoute('show_club_membres', array(
                        'clubId' => $clubPersonne->getIdClub()->getId()
                    ));
                } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                    $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
                }
            }


            return $this->render('@Natation/Club/update.html.twig', array(
                'form_title' => 'Modification de la date de fin d\'inscription pour ' . $personne,
                'form' => $form->createView(),
                'returnPageUrl' => $this->generateUrl(
                    'show_club_membres', array(
                        'clubId' => $clubPersonne->getIdClub()->getId()
                    )
                ),
            ));
        } else {
            return $this->redirectToRoute('all_clubs');
        }

    }


    /**
     * @Route("/club/add/{clubId}/membre", name="add_club_membre", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function addClubMembreAction(Request $request, $clubId)
    {
        $club = $this->getDoctrine()
            ->getRepository(Club::class)
            ->find($clubId);

        $clubPersonne = new ClubPersonne();

        $clubPersonne->setIdClub($club);

        $form = $this->createFormBuilder($clubPersonne)
            ->add('idPersonne', EntityType::class, array('label' => 'Person', 'class' => 'NatationBundle:Personne'))
            ->add('dateinscription', DateType::class, array(
                'label' => 'Begin of registration',
                'years' => range(date('Y') - 1, date('Y') + 10),
            ))
            //->add('datefininscription', DateType::class, array('label' => 'End of registration'))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personne = $form->getData();

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($personne);
                $entityManager->flush();

                return $this->redirectToRoute('show_club_membres', array(
                    'clubId' => $clubId
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                var_dump($ex->getMessage());
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }


        return $this->render('@Natation/Club/update_membre_club.html.twig', array(
            'form_title' => 'Ajout d\'une personne au club',
            'form' => $form->createView(),
            'returnPageUrl' => $this->generateUrl(
                'show_club_membres', array(
                    'clubId' => $clubId
                )
            ),
            'clubId' => $clubId,
        ));
    }


    /**
     * @Route("/personne/add/{clubId}", name="add_personne", requirements={"clubId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function addPersonneAction(Request $request, $clubId)
    {
        $club = $this->getDoctrine()
            ->getRepository(Personne::class)
            ->find($clubId);

        $personne = new Personne();

        $form = $this->createFormBuilder($personne)
            ->add('nom', TextType::class, array('label' => 'Last name'))
            ->add('prenom', TextType::class, array('label' => 'First name'))
            ->add('datenaissance', DateType::class, array(
                'label' => 'Born date',
                'years' => range(date('Y') - 80, date('Y')),
            ))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personne = $form->getData();

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($personne);
                $entityManager->flush();

                return $this->redirectToRoute('add_club_membre', array(
                    'clubId' => $clubId
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }


        return $this->render('@Natation/Club/update.html.twig', array(
            'form_title' => 'Création d\'une nouvelle personne',
            'form' => $form->createView(),
            'returnPageUrl' => $this->generateUrl(
                'add_club_membre', array(
                    'clubId' => $clubId
                )
            ),
        ));
    }
}