<?php

namespace NatationBundle\Twig;

use NatationBundle\Entity\Competition;
use NatationBundle\Entity\Equipe;
use NatationBundle\Entity\JugeCompetition;
use NatationAuthBundle\Entity\Utilisateur;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Security;

// see: https://stackoverflow.com/questions/39871558/how-to-get-the-current-user-in-twig-extension-service-symfony2-8

class CompetRoleExtension extends \Twig_Extension
{
    protected $container;
    protected $em;
    protected $context;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function has_compet_role(Competition $competition, string $role)
    {
        $_loggedUser = $this->container->get('security.token_storage')->getToken()->getUser();

        $loggedUser = $this->em
            ->getRepository(Utilisateur::class)
            ->find($_loggedUser->getId());
        
        // Obtention du rôle pour la compétition en cours
        $jugeCompet = $this->em
            ->getRepository(JugeCompetition::class)
            ->findOneBy(array(
                'idUtilisateur' => $loggedUser
            ));

        // Si aucun rôle dans la competition en cours, on renvoie false
        if($jugeCompet === null) {
            return false;
        } else {
            // Si le rôle correspond au rôle demandé
            return (
                strtoupper($jugeCompet->getIdTypejuge()->getNom())
            == strtoupper($role)
            );
        }
    }

    public function nNotes_equipe(Equipe $equipe)
    {
        $rawSql = "SELECT
            count(*)
        from equipe_jugeCompetition
        inner join equipe
            on equipe.id = equipe_jugecompetition.id_equipe
        AND id_equipe = " . $equipe->getId() . "
        ";

        $stmt = $this->em->getConnection()->prepare($rawSql);
        $stmt->execute([]);
        
        $nResults = $stmt->fetchColumn(0);

        return $nResults;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_Function('has_compet_role', array($this, 'has_compet_role')),
            new \Twig_Function('nNotes_equipe', array($this, 'nNotes_equipe')),
        );
    }
}