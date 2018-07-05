<?php

namespace NatationAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NatationAuthBundle\Entity\Utilisateur;
use NatationBundle\Entity\Personne;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use NatationAuthBundle\Entity\TypeUtilisateur;

class UtilisateurController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');


        return $this->render(
            '@NatationAuth/login.html.twig',
            array(
                'last_username' => $helper->getLastUsername(),
                'error'         => $helper->getLastAuthenticationError(),
            )
        );
    }

    /**
     * @Route("/login_check", name="security_login_check")
     * @Method({"POST"})
     */
    public function doLoginAction()
    {
        // Nothing to do
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        // Nothing to do
    }


    /**
     * @Route("/user/show", name="show_curr_user")
     * @Security("has_role('ROLE_USER')")
     */
    public function showCurrUserAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render(
            '@NatationAuth/user/show.html.twig',
            array(
                'user' => $user
            )
        );
    }

    /**
     * @Route("/user/allUsers", name="all_users")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAllUserAction()
    {
        $allUsers = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->findAll();

        //return new Response('OK');
        return $this->render(
            '@NatationAuth/all_users.html.twig',
            array(
                'allUsers' => $allUsers
            )
        );
    }

    /**
     * @Route("/user/show/{userId}", name="show_user", requirements={"userId"="\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showUserAction(int $userId)
    {
        $user = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->find($userId);

            return $this->render(
                '@NatationAuth/user/show.html.twig',
                array(
                    'user' => $user
                )
            );
    }

    /**
     * @Route("/user/update/{userId}/userMail", name="update_userMail", requirements={"userId"="\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateUserMail(Request $request, int $userId)
    {
        // On ne peut pas modifier son profil avec ce menu
        if($this->isLoggedUser($userId)) {
            return $this->redirectToRoute('show_user', array(
                'userId' => $userId
            ));
        }

        $user = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->find($userId);

        $form = $this->createFormBuilder($user)
            ->add('mail', EmailType::class, array('label' => 'New mail address'))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
        
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
                return $this->redirectToRoute('show_user', array(
                    'userId' => $user->getId()
                ));
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException  $ex) {
                $form->addError(new FormError('Cette adresse e-mail existe déjà'));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render('@NatationAuth/user/update.html.twig', array(
            'form_title' => 'Modification de l\'adresse mail pour l\'utilisateur ' . $user->getMail(),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/update/userMail", name="update_currUserMail")
     * @Security("has_role('ROLE_USER')")
     */
    public function updateCurrUserMail(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form = $this->createFormBuilder($user)
            ->add('mail', EmailType::class, array('label' => 'New mail address'))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
        
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
                return $this->redirectToRoute('logout');
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException  $ex) {
                $form->addError(new FormError('Cette adresse e-mail existe déjà'));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render('@NatationAuth/user/update.html.twig', array(
            'form_title' => 'Modification de l\'adresse mail pour l\'utilisateur ' . $user->getMail(),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/update/{userId}/userPassword", name="update_userPassword", requirements={"userId"="\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateUserPassword(Request $request, int $userId)
    {
        // On ne peut pas modifier son profil avec ce menu
        if($this->isLoggedUser($userId)) {
            return $this->redirectToRoute('show_user', array(
                'userId' => $userId
            ));
        }

        $user = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->find($userId);

        $form = $this->createFormBuilder($user)
            ->add('password', PasswordType::class, array('label' => 'New password'))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $pwd = $encoder->encodePassword($user, $form["password"]->getData());

            $user->setPassword($pwd);
        
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
                return $this->redirectToRoute('show_user', array(
                    'userId' => $user->getId()
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render('@NatationAuth/user/update.html.twig', array(
            'form_title' => 'Modification du mot de passe pour l\'utilisateur ' . $user->getMail(),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/update/userPassword", name="update_currUserPassword")
     * @Security("has_role('ROLE_USER')")
     */
    public function updateCurrUserPassword(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form = $this->createFormBuilder($user)
            ->add('password', PasswordType::class, array('label' => 'New password'))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $pwd = $encoder->encodePassword($user, $form["password"]->getData());

            $user->setPassword($pwd);
        
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
                return $this->redirectToRoute('logout');
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render('@NatationAuth/user/update.html.twig', array(
            'form_title' => 'Modification du mot de passe pour l\'utilisateur ' . $user->getMail(),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/update/{userId}/userRoles", name="update_userRoles", requirements={"userId"="\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateUserRoles(Request $request, int $userId)
    {
        // On ne peut pas modifier son profil avec ce menu
        if($this->isLoggedUser($userId)) {
            return $this->redirectToRoute('show_user', array(
                'userId' => $userId
            ));
        }

        // https://stackoverflow.com/questions/39945154/symfony-3-update-roles-user-with-form
        $user = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->find($userId);

        $form = $this->createFormBuilder($user)
            ->add('roles', EntityType::class, array(
                'class' => TypeUtilisateur::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'choice_value' => 'nom',
            ))
            ->add('save', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
        
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
    
                return $this->redirectToRoute('show_user', array(
                    'userId' => $user->getId()
                ));
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render('@NatationAuth/user/update_roles.html.twig', array(
            'form_title' => 'Modification des rôles pour l\'utilisateur ' . $user->getMail(),
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    /**
     * @Route("/user/new", name="new_user")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newUserAction(Request $request)
    {
        $user = new Utilisateur();

        $allPersonnes = $this->getDoctrine()
            ->getRepository(Personne::class)
            ->findAll();

        $form = $this->createFormBuilder($user)
            ->add('mail', EmailType::class, array('label' => 'New mail address'))
            ->add('mdp', PasswordType::class, array('label' => 'New password'))
            ->add('personne', EntityType::class, array('label' => 'Person', 'class' => 'NatationBundle:Personne', 'choices' => $allPersonnes))
            ->add('create', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('all_users');
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render(
            '@NatationAuth/user/new.html.twig',
            array(
                'form_title' => 'Nouvel utilisateur',
                'form' => $form->createView(),
                'returnPageUrl' => $this->generateUrl(
                    'all_users'
                ),
            )
        );
    }

    /**
     * @Route("/user/personne/new", name="new_user_personne")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newUserPersonneAction(Request $request)
    {
        $personne = new Personne();

        $form = $this->createFormBuilder($personne)
            ->add('nom', TextType::class, array('label' => 'Last name'))
            ->add('prenom', TextType::class, array('label' => 'First name'))
            ->add('datenaissance', DateType::class, array(
                'label' => 'Born date',
                'years' => range(date('Y')-80, date('Y')),
            ))
            ->add('create', SubmitType::class)
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personne = $form->getData();

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($personne);
                $entityManager->flush();

                return $this->redirectToRoute('new_user');
            } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
                $form->addError(new FormError('Une erreur s\'est produite lors de l\'émission du formulaire'));
            }
        }

        return $this->render(
            '@NatationAuth/user/update.html.twig',
            array(
                'form_title' => 'Création d\'une nouvelle personne',
                'form' => $form->createView(),
                'returnPageUrl' => $this->generateUrl(
                    'new_user'
                ),

            )
        );
    }

    private function isLoggedUser(int $userId)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $userId === $user->getId();
    }
}
