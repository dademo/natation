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
use NatationBundle\Entity\Personne;
use NatationAuthBundle\Entity\Utilisateur;
use NatationBundle\Entity\Equipe;
use NatationBundle\Entity\Note;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormError;


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
            'returnPageUrl' => $this->generateUrl(
                'all_competitions'
            ),
        ));
    }

    /**
     * @Route("/compet/show/{competId}/list_judges", name="show_competition_juges", requirements={"competId"="\d+"})
     * @Security("has_role('ROLE_USER')")
     */
    public function showJugecompetitionAction(int $competId)
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
            'alerts' => $alerts,
            'returnPageUrl' => $this->generateUrl(
                'show_competition',
                array(
                    'competId' => $competId,
                )
            ),
        ));
    }


    /**
     * @Route("/compet/show/{competId}/list_teams", name="show_competition_teams", requirements={"competId"="\d+"})
     * @Security("has_role('ROLE_USER')")
     */
    public function showEquipeCompetitionAction(int $competId)
    {
        $competition = $this->getDoctrine()
            ->getRepository(Competition::class)
            ->find($competId);

        
        return $this->render('@Natation/Competition/showTeam.html.twig', array(
            'competition' => $competition,
            'returnPageUrl' => $this->generateUrl(
                'show_competition',
                array(
                    'competId' => $competId,
                )
            ),
        ));
    }

    /**
     * @Route("/compet/new/{competId}/team", name="new_equipe", requirements={"competId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function newEquipeCompetitionAction(Request $request, int $competId)
    {
        $competition = $this->getDoctrine()
            ->getRepository(Competition::class)
            ->find($competId);

        $equipe = new Equipe();

        $equipe->setIdCompetition($competition);

        $rawSql = 'select count(*) FROM equipe WHERE id_competition = ' . $competId;

        $stmt = $this->getDoctrine()->getConnection()->prepare($rawSql);
        $stmt->execute([]);
        
        $ordrePassage = $stmt->fetchColumn(0) + 1;
        
        $equipe->setOrdrePassage($ordrePassage);

        $equipe->setVisionnable(false);
        $equipe->setNotable(false);


        $form = $this->createFormBuilder($equipe)
        ->add('nom', TextType::class, array('label' => 'Name'))
        ->add('create', SubmitType::class)
        ->getForm();
    
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $equipe = $form->getData();
                
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($equipe);
                $entityManager->flush();
           
                return $this->redirectToRoute('show_competition_teams', array('competId' => $competId));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }
    
        return $this->render('@Natation/Competition/new.html.twig', array(
            'form_title' => 'Création d\'une nouvelle compétition',
            'form' => $form->createView(),
            'returnPageUrl' => $this->generateUrl('all_competitions'),
        ));
    }

    /**
     * @Route("/compet/set/{equipeId}/begin", name="set_equipe_debut", requirements={"equipeId"="\d+"})
     * @Security("has_role('ROLE_JUGE')")
     */
    public function setDebutEquipeCompetitionAction(Request $request, int $equipeId)
    {
        $currUser = $this->get('security.token_storage')->getToken()->getUser();
        
        $jugeCompetition = $this->getDoctrine()
            ->getRepository(JugeCompetition::class)
            ->findOneBy(array(
                'idUtilisateur' => $currUser
            ));


        // Si c'est un juge-arbitre
        if ($jugeCompetition->getIdTypejuge()->getNom() == 'Juge-arbitre') {
            $equipe = $this->getDoctrine()
                ->getRepository(Equipe::class)
                ->find($equipeId);

            $equipe->setDebut(new \DateTime('now'));

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($equipe);
                $entityManager->flush();
            
                return $this->redirectToRoute('show_competition_teams', array(
                    'competId' => $equipe->getIdCompetition()->getId(),
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                echo 'Exception inconnue : ' . $ex->getMessage();
            }
        } else {
            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/compet/set/{equipeId}/notable", name="set_equipe_notable", requirements={"equipeId"="\d+"})
     * @Security("has_role('ROLE_JUGE')")
     */
    public function setNotableEquipeCompetitionAction(Request $request, int $equipeId)
    {
        $currUser = $this->get('security.token_storage')->getToken()->getUser();
        
        $jugeCompetition = $this->getDoctrine()
            ->getRepository(JugeCompetition::class)
            ->findOneBy(array(
                'idUtilisateur' => $currUser
            ));


        // Si c'est un juge-arbitre
        if ($jugeCompetition->getIdTypejuge()->getNom() == 'Juge-arbitre') {
            $equipe = $this->getDoctrine()
                ->getRepository(Equipe::class)
                ->find($equipeId);

            $equipe->setNotable(true);

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($equipe);
                $entityManager->flush();
            
                return $this->redirectToRoute('show_competition_teams', array(
                    'competId' => $equipe->getIdCompetition()->getId(),
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                echo 'Exception inconnue : ' . $ex->getMessage();
            }
        } else {
            return $this->redirect($request->headers->get('referer'));
        }
    }


    /**
     * @Route("/compet/set/{equipeId}/visionnable", name="set_equipe_visionnable", requirements={"equipeId"="\d+"})
     * @Security("has_role('ROLE_JUGE')")
     */
    public function setVisionnableEquipeCompetitionAction(Request $request, int $equipeId)
    {
        $currUser = $this->get('security.token_storage')->getToken()->getUser();
        
        $jugeCompetition = $this->getDoctrine()
            ->getRepository(JugeCompetition::class)
            ->findOneBy(array(
                'idUtilisateur' => $currUser
            ));


        // Si c'est un juge-arbitre
        if ($jugeCompetition->getIdTypejuge()->getNom() == 'Juge-arbitre') {
            $equipe = $this->getDoctrine()
                ->getRepository(Equipe::class)
                ->find($equipeId);

            $equipe->setVisionnable(true);

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($equipe);
                $entityManager->flush();
            
                return $this->redirectToRoute('show_competition_teams', array(
                    'competId' => $equipe->getIdCompetition()->getId(),
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                echo 'Exception inconnue : ' . $ex->getMessage();
            }
        } else {
            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/compet/set/{equipeId}/note", name="set_equipe_note", requirements={"equipeId"="\d+"})
     * @Security("has_role('ROLE_JUGE')")
     */
    public function noteEquipeCompetitionAction(Request $request, int $equipeId)
    {
        $currUser = $this->get('security.token_storage')->getToken()->getUser();
        
        $jugeCompetition = $this->getDoctrine()
            ->getRepository(JugeCompetition::class)
            ->findOneBy(array(
                'idUtilisateur' => $currUser
            ));


        // Si c'est un juge-arbitre
        if ($jugeCompetition->getIdTypejuge()->getNom() == 'Juge') {
            $equipe = $this->getDoctrine()
                ->getRepository(Equipe::class)
                ->find($equipeId);
                
            $note = $this->getDoctrine()
                ->getRepository(Note::class)
                ->findOneBy(array(
                    'idEquipe' => $equipeId,
                    'idJugecompetition' => $jugeCompetition
                    )
                );

            if ($note == null) {
                $note = new Note();
                $note->setidEquipe($equipe);
                $note->setIdJugecompetition($jugeCompetition);
            }

            $form = $this->createFormBuilder($note)
                ->add('note', NumberType::class, array(
                    'label' => 'Note',
                    'attr' => array(
                        'min' => 0,'returnPageUrl' => $this->generateUrl('all_competitions'),
                        'max' => 100,
                    ),
                    ))
                ->add('Apply', SubmitType::class)
                ->getForm();
        
        
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $note = $form->getData();
                    
                try {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($note);
                    $entityManager->flush();
            
                    return $this->redirectToRoute('show_competition_teams', array(
                        'competId' => $equipe->getIdCompetition()->getId(),
                    ));
                } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                    $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
                }
            }

            return $this->render('@Natation/Club/update.html.twig', array(
                'form_title' => 'Ajout/Modification de la note pour l\'équipe ' . $equipe->getNom(),
                'form' => $form->createView(),
                'returnPageUrl' => $this->generateUrl('show_competition_teams', array(
                    'competId' => $equipe->getIdCompetition()->getId(),
                )),'returnPageUrl' => $this->generateUrl('all_competitions'),
            ));


        } else {
            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/compet/set/{equipeId}/members", name="set_equipe_membres", requirements={"equipeId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setMembresEquipeCompetitionAction(Request $request, int $equipeId)
    {
        $equipe = $this->getDoctrine()
            ->getRepository(Equipe::class)
            ->find($equipeId);

        $alerts = array();

        if ($request->isMethod('POST')) {
            $all_id_membresEquipe = json_decode($_POST["all_membres_equipe"], true);

            if($all_id_membresEquipe !== null && is_array($all_id_membresEquipe)) {
                $all_membres_equipe = $this->getDoctrine()
                    ->getRepository(Personne::class)
                    ->findBy(array(
                        'id' => $all_id_membresEquipe
                    ));

                    // S'il y a autant de personnes trouvées que de personnes demandées
                    if(count($all_membres_equipe) == count($all_id_membresEquipe)) {
                        $equipe->cleanIdPersonne();
                        // On vérifie qu'ils fassent partie du même club et on ajoute
                        $lastClubId = null;
                        foreach($all_membres_equipe as $personne) {
                            $curr_club = $personne->getClubAt($equipe->getIdCompetition()->getDatecompetition());
                            if($lastClubId !== null) {
                                if($lastClubId != $curr_club->getId()) {
                                    $alerts[] = 'Plusieurs clubs ont été sélectionnés';
                                }
                            }
                            $lastClubId = $curr_club->getId();
                            $equipe->addIdPersonne($personne);
                        }

                        // S'il n'y a pas d'erreur
                        if(count($alerts) == 0) {
                            try {
                                $entityManager = $this->getDoctrine()->getManager();
                                $entityManager->persist($equipe);
                                $entityManager->flush();
                        
                                return $this->redirectToRoute('show_competition_teams', array(
                                    'competId' => $equipe->getIdCompetition()->getId(),
                                ));
                            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                                $alerts[] = 'Une erreur s\'est produite lors de l\'émission du formulaire';
                            }
                        }
                    } else {
                        $alerts[] = 'Erreur lors de la lecture du formulaire: Une des personne n\'existe pas';
                    }
            } else {
                $alerts[] = 'Erreur lors de la lecture du formulaire';
            }
/*
            if($juges !== null) {
                $allJuges = [];

                foreach($juges as $juge) {
                    // Obtention du juge s'il existe
                    $newJuge = $this->getDoctrine()
                        ->getRepository(Jugecompetition::class)
                        ->findOneBy(array(
                            'idCompetition' => $competition,
                                'rang' => $juge['rangJuge']
                            ));
                        if($newJuge === NULL) {
                            $newJuge = new JugeCompetition();
                            $newJuge->setRang($juge['rangJuge']);
                            $newJuge->setIdCompetition($competition);
                        }
                        $newJuge->setIdTypejuge(
                            $this->getDoctrine()
                            ->getRepository(Typejuge::class)
                            ->findOneBy(array(
                                'nom' => $juge['typejuge']
                            ))
                        );
                        $newJuge->setIdUtilisateur(
                            $this->getDoctrine()
                            ->getRepository(Utilisateur::class)
                            ->find($juge['idUtilisateur'])
                        );
                        $allJuges[] = $newJuge;
                    }
    
                    $competition->setIdJugecompetition($allJuges);

                    try {
                            
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->getConnection()->beginTransaction();
                        $entityManager->getConnection()->setAutoCommit(false);
                        // Ajout des nouveaux juges
                        $entityManager->persist($competition);
                        $entityManager->flush();
                        $entityManager->getConnection()->commit();
                    
                        return $this->redirectToRoute('show_competition_juges', array(
                            'competId' => $competId
                        ));
                    } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
                        $alerts[] = 'Une valeur est dupliquée ('  .$ex->getMessage() . ')';
                        $this->getDoctrine()->getManager()->getConnection()->rollBack();
                    } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                        $alerts[] = 'Exception inconnue : ' . $ex->getMessage();
                        $this->getDoctrine()->getManager()->getConnection()->rollBack();
                    } catch (\PDOException $ex) {
                        $alerts[] = 'Exception inconnue : ' . $ex->getMessage();
                        $this->getDoctrine()->getManager()->getConnection()->rollBack();
                    }
                } else {
                    $alerts[] = 'Erreur lors de la lecture du formulaire';
                }*/
        }
            //https://stackoverflow.com/questions/12862389/symfony2-doctrine-create-custom-sql-query
        $rawSql = "SELECT personne.id
        FROM personne
        -- Club
        INNER JOIN club_personne
            ON club_personne.id_personne = personne.id
        WHERE personne.id NOT IN (
            SELECT personne.id
            FROM personne
            -- Competition; les personnes ne sont pas déjà inscrites
            INNER JOIN equipe_personne
                ON equipe_personne.id_personne = personne.id
            INNER JOIN equipe
                ON equipe_personne.id_equipe = equipe.id
            INNER JOIN competition
                ON competition.id = equipe.id_competition
            -- Competition
            WHERE equipe.id_competition = :idCompet
                AND equipe.id != :idEquipe
            )
            -- Club
            AND (club_personne.dateFinInscription IS NULL
                OR club_personne.dateFinInscription >= :dateCompet
                )
            ";

        $stmt = $this->getDoctrine()->getConnection()->prepare($rawSql);
        //$stmt->bindValue(':idCompet', $equipe->getIdCompetition()->getId());
        //$stmt->bindValue(':dateCompet', date_format($equipe->getIdCompetition()->getDatecompetition(), 'Y-m-d'), \PDO::PARAM_STR);
        $stmt->execute(array(
            ':idCompet' => $equipe->getIdCompetition()->getId(),
            ':idEquipe' => $equipeId,
            ':dateCompet' => date_format($equipe->getIdCompetition()->getDatecompetition(), 'Y-m-d'),
        ));


        $_all_id_personnes = $stmt->fetchAll();

        $all_id_personnes = array();
        foreach($_all_id_personnes as $val) {
            $all_id_personnes[] = $val['id'];
        }


        $allPersonnes = $this->getDoctrine()
            ->getRepository(Personne::class)
            ->findBy(array(
                'id' => $all_id_personnes
            ));

        $_allEquipeMembres = $equipe->getIdPersonne();
        $allEquipeMembres = array();

        foreach($_allEquipeMembres as $personne) {
            $allEquipeMembres[] = $personne->getId();
        }

        /*
                $allPersonnes = array();

                foreach($_allPersonnes as $personne) {
                    if($personne->getCurrIdClubPersonne() !== null && $personne->getCurrIdEquipe() === null) {
                        $allPersonnes[] = $personne;
                    }
                }*/

        return $this->render('@Natation/Competition/setEquipeMembresForm.html.twig', array(
            'equipe' => $equipe,
            'alerts' => $alerts,
            'allPersonnes' => $allPersonnes,
            'allEquipeMembres' => $allEquipeMembres,
            'returnPageUrl' => $this->generateUrl('show_competition_teams', array(
                'competId' => $equipe->getIdCompetition()->getId(),
            )),
        ));

        

        return new Response('OK');
    }

    /**
     * @Route("/compet/set/{equipeId}/penalite", name="set_equipe_penalite", requirements={"equipeId"="\d+"})
     * @Security("has_role('ROLE_JUGE')")
     */
    public function setPenaliteEquipeCompetitionAction(Request $request, int $equipeId)
    {
        $currUser = $this->get('security.token_storage')->getToken()->getUser();
        
        $jugeCompetition = $this->getDoctrine()
            ->getRepository(JugeCompetition::class)
            ->findOneBy(array(
                'idUtilisateur' => $currUser
            ));


        // Si c'est un juge-arbitre
        if ($jugeCompetition->getIdTypejuge()->getNom() == 'Juge-arbitre') {
            $equipe = $this->getDoctrine()
                ->getRepository(Equipe::class)
                ->find($equipeId);


                $form = $this->createFormBuilder($equipe)
                ->add('penalite', ChoiceType::class, array(
                    'label' => 'Penality',
                    'choices' => array(
                        '0' => 0,
                        '0.5' => 0.5,
                        '1' => 1,
                        '1.5' => 1.5,
                        '2' => 2,
                    ),
                    ))
                ->add('Apply', SubmitType::class)
                ->getForm();
        
        
                $form->handleRequest($request);
        
                if ($form->isSubmitted() && $form->isValid()) {
                    $club = $form->getData();
                    
                    try {
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($club);
                        $entityManager->flush();
            
                        return $this->redirectToRoute('show_competition_teams', array(
                            'competId' => $equipe->getIdCompetition()->getId(),
                        ));
                    } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                        $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
                    }
                }
        
        
                return $this->render('@Natation/Club/update.html.twig', array(
                    'form_title' => 'Création d\'un nouveau club',
                    'form' => $form->createView(),
                    'returnPageUrl' => $this->generateUrl('show_competition_teams', array(
                        'competId' => $equipe->getIdCompetition()->getId(),
                    )),
                ));
        } else {
            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/compet/set/{competId}/list_judges", name="set_competition_juges", requirements={"competId"="\d+"})
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function setJugecompetitionAction(Request $request, int $competId)
    {
        $alerts = array();
        $competition = $this->getDoctrine()
            ->getRepository(Competition::class)
            ->find($competId);

        if ($request->isMethod('POST')) {
            try {
                $juges = \json_decode($_POST["all_arbitres"], true);
                if($juges !== null) {
                    $allJuges = [];

                    foreach($juges as $juge) {
                        // Obtention du juge s'il existe
                        $newJuge = $this->getDoctrine()
                            ->getRepository(Jugecompetition::class)
                            ->findOneBy(array(
                                'idCompetition' => $competition,
                                'rang' => $juge['rangJuge']
                            ));
                        if($newJuge === NULL) {
                            $newJuge = new JugeCompetition();
                            $newJuge->setRang($juge['rangJuge']);
                            $newJuge->setIdCompetition($competition);
                        }
                        $newJuge->setIdTypejuge(
                            $this->getDoctrine()
                            ->getRepository(Typejuge::class)
                            ->findOneBy(array(
                                'nom' => $juge['typejuge']
                            ))
                        );
                        $newJuge->setIdUtilisateur(
                            $this->getDoctrine()
                            ->getRepository(Utilisateur::class)
                            ->find($juge['idUtilisateur'])
                        );
                        $allJuges[] = $newJuge;
                    }

                    $competition->setIdJugecompetition($allJuges);

                    try {
                        
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->getConnection()->beginTransaction();
                        $entityManager->getConnection()->setAutoCommit(false);
                        // Ajout des nouveaux juges
                        $entityManager->persist($competition);
                        $entityManager->flush();
                        $entityManager->getConnection()->commit();
                
                        return $this->redirectToRoute('show_competition_juges', array(
                            'competId' => $competId
                        ));
                    } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
                        $alerts[] = 'Une valeur est dupliquée ('  .$ex->getMessage() . ')';
                        $this->getDoctrine()->getManager()->getConnection()->rollBack();
                    } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                        $alerts[] = 'Exception inconnue : ' . $ex->getMessage();
                        $this->getDoctrine()->getManager()->getConnection()->rollBack();
                    } catch (\PDOException $ex) {
                        $alerts[] = 'Exception inconnue : ' . $ex->getMessage();
                        $this->getDoctrine()->getManager()->getConnection()->rollBack();
                    }
                } else {
                    $alerts[] = 'Erreur lors de la lecture du formulaire';
                }
            } catch (\Exception $ex) {
                $alerts[] = 'Erreur lors de la lecture du formulaire';
            } catch (\Error $ex) {
                $alerts[] = 'Erreur lors de la lecture du formulaire';
            }
        }

        // https://www.doctrine-project.org/api/orm/latest/Doctrine/ORM/EntityRepository.html#method_findBy
        $rawSql = "SELECT
            utilisateur.id
        FROM utilisateur
        INNER JOIN utilisateur_typeUtilisateur
            ON utilisateur_typeUtilisateur.id_utilisateur = utilisateur.id
        INNER JOIN typeUtilisateur
            ON typeUtilisateur.id = utilisateur_typeUtilisateur.id_typeUtilisateur
        WHERE typeUtilisateur.nom = 'ROLE_JUGE'
        ";

        $stmt = $this->getDoctrine()->getConnection()->prepare($rawSql);
        $stmt->execute([]);
        $_allJuges = $stmt->fetchAll();

        $tmpAllJuges = array();
        foreach($_allJuges as $val) {
            $tmpAllJuges[] = $val['id'];
        }

        $allJuges = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->findBy(array(
                'id' => $tmpAllJuges
            ));

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
                $allCompetJuges[$juge->getIdUtilisateur()->getId()] = $juge->getRang();
            } else {
                $allCompetJugeArbitres[$juge->getIdUtilisateur()->getId()] = $juge->getRang();
            }
        }

        return $this->render('@Natation/Competition/setJugeForm.html.twig', array(
            'competition' => $competition,
            'allJuges' => $allJuges,
            'allTypeJuge' => $allTypeJuge,
            'allCompetJuge' => $allCompetJuges,
            'allCompetJugeArbitres' => $allCompetJugeArbitres,
            'alerts' => $alerts,
            'returnPageUrl' => $this->generateUrl(
                'show_competition_juges',
                array(
                    'competId' => $competId,
                )
            ),
        ));
    }


    /**
     * @Route("/compet/new", name="new_competition")
     * @Security("has_role('ROLE_CREATE_COMPET')")
     */
    public function newCompetitionAction(Request $request)
    {
        $competition = new Competition();

        $form = $this->createFormBuilder($competition)
        ->add('titre', TextType::class, array('label' => 'Title'))
        ->add('datecompetition', DateType::class, array('label' => 'Date'))
        ->add('idLieu', EntityType::class, array('label' => 'Location', 'class' => 'NatationBundle:Lieu'))
        ->add('create', SubmitType::class)
        ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competition = $form->getData();
            
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($competition);
                $entityManager->flush();
        
                return $this->redirectToRoute('all_competitions');
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render('@Natation/Competition/new.html.twig', array(
            'form_title' => 'Création d\'une nouvelle compétition',
            'form' => $form->createView(),
            'returnPageUrl' => $this->generateUrl('all_competitions'),
        ));
    }
}
