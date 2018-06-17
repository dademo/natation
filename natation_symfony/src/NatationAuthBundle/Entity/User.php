<?php

namespace NatationAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
//use Symfony\Brodge\Doctrine\Security\User\UserLoaderInterface;

/**
 * Utilisateur
 * 
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="NatationAuthBundle\Repository\UserRepository")
 */
class User implements UserInterface, AdvancedUserInterface, \Serializable
{
    /**
     * @var int
     * 
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="utilisateur_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, unique=true, nullable=false)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, nullable=false)
     */
    private $mdp;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="TypeUtilisateur")
     * @ORM\JoinTable(name="utilisateur_typeutilisateur",
     *                  joinColumns={@ORM\JoinColumn(name="id_utilisateur", referencedColumnName="id")},
     *                  inverseJoinColumns={@ORM\JoinColumn(name="id_typeUtilisateur", referencedColumnName="id")}
     *      )
     */
    private $roles;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get Username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getMail();
    }

    /**
     * Get mail.
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set mail.
     *
     * @param string $mail
     *
     * @return User
     */
    public function setMail(string $mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get salt.
     *
     * @return string|null
     */
    public function getSalt()
    {
        // blowfish encoder, return NULL
        return null;
    }

    /**
     * Get mdp.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->getMdp();
    }

    /**
     * Get mdp.
     *
     * @return string
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * Set mdp.
     *
     * @param string $mdp
     *
     * @return User
     */
    public function setPassword(string $mdp)
    {
        return $this->setMdp($mdp);
    }

    /**
     * Set mdp.
     *
     * @param string $mdp
     *
     * @return User
     */
    public function setMdp(string $mdp)
    {
        // TODO: Encode the password and then set //
        //$encoder = $this->get('security.password_encoder');
        //$this->pwd = $encoder->encodePassword($this, $mdp);
        $this->mdp = $mdp;

        return $this;
    }

    /**
     * Get roles.
     * 
     * @return String[]
     */
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

    /**
     * Get role objects.
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRolesObj()
    {
        return $this->roles;
    }


    /**
     * Add role.
     *
     * @param \NatationAuthBundle\Entity\TypeUtilisateur $role
     *
     * @return User
     */
    public function addRole(TypeUtilisateur $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Delete role.
     *
     * @param \NatationAuthBundle\Entity\TypeUtilisateur $role
     *
     * @return User
     */
    public function delRole(TypeUtilisateur $role)
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return true;
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
