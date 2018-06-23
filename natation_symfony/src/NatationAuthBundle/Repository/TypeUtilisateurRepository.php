<?php

namespace NatationAuthBundle\Repository;

use NatationAuthBundle\Entity\TypeUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
//use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

class TypeUtilisateurRepository extends EntityRepository
{
    public function __construct(EntityManager $registry)
    {
        parent::__construct($registry, new ClassMetadata(TypeUtilisateur::class));
    }
}