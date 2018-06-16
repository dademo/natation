<?php

namespace NatationAuthBundle\Repository;

use NatationAuthBundle\Entity\User;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

class UserRepository extends EntityRepository // implements UserLoaderInterface
{
    public function __construct(EntityManager $registry)
    {
        parent::__construct($registry, new ClassMetadata(User::class));
    }

    public function loadUserByUsername($username)
    {
        /*
        return $this->getEntityManager()
            ->createQuery(
                'SELECT utilisateur.mail, utilisateur.mdp, ARRAY_AGG(\'typeUtilisateur.nom\')
                FROM utilisateur
                INNER JOIN utilisateur_typeUtilisateur
                    ON utilisateur_typeUtilisateur.id_utilisateur = utilisateur.id
                INNER JOIN typeUtilisateur
                typeUtilisateur.id = ON utilisateur_typeUtilisateur.id_typeUtilisateur
                WHERE utilisateur.mail = :email'
            )
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
        */
        
        return $this->createQueryBuilder('user')
            ->from('utilisateur')
            ->where('utilisateur.mail = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
        
    }
}