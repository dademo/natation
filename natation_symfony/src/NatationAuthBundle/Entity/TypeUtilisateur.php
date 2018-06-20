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
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $nom;

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return Typeutilisateur
     */
    public function setNom(string $nom)
    {
        $this->nom = $nom;

        return $this;
    }
}
