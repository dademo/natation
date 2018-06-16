<?php

namespace NatationAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
//use Symfony\Brodge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @ORM\Table(name="typeUtilisateur")
 * @ORM\Entity(repositoryClass="NatationAuthBundle\Repository\TypeUtilisateurRepository")
 */
class TypeUtilisateur
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $nom;


    public function getId()
    {
        return $this->id;
    }


    public function getNom()
    {
        return $this->nom;
    }

    public function setNom(string $nom)
    {
        $this->nom = $nom;
    }
}
