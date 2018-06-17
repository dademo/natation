<?php

namespace NatationAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
//use Symfony\Brodge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="NatationAuthBundle\Repository\UserRepository")
 */
class User implements UserInterface, AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $mdp;

    /**
     * @ORM\ManyToMany(targetEntity="TypeUtilisateur")
     * @ORM\JoinTable(name="utilisateur_typeutilisateur",
     *                  joinColumns={@ORM\JoinColumn(name="id_utilisateur", referencedColumnName="id")},
     *                  inverseJoinColumns={@ORM\JoinColumn(name="id_typeUtilisateur", referencedColumnName="id")}
     *      )
     */
    private $roles;



    public function __construct()
    {
        
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->getMail();
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function setMail(string $mail)
    {
        $this->mail = $mail;
    }

    public function getSalt()
    {
        // blowfish encoder, return NULL
        return null;
    }

    public function getPassword()
    {
        return $this->mdp;
    }

    public function setPassword(string $mdp)
    {
        // TODO: Encode the password and then set //
    }

    public function getRoles()
    {
        // Generating array of string
        $toReturn = array();

        foreach($this->roles as $role) {
            $toReturn[] = $role->getNom();
        }

        //return array('ROLE_USER');
        //var_dump($this->roles);
        return $toReturn;
    }

    public function addRole(TypeUtilisateur $role) {
        if(!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function delRole(TypeUtilisateur $role) {
        if(($pos = array_search($role, $this->roles) !== false)) {
            unset($this->roles[$pos]);
        }
    }

    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return true;
    }

    public function loadUserByMail(string $mail) {

    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->mail,
            $this->mdp,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->mail,
            $this->mdp,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }
}
