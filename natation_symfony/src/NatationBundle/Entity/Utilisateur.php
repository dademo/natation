<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur", uniqueConstraints={@ORM\UniqueConstraint(name="utilisateur_id_personne_key", columns={"id_personne"}), @ORM\UniqueConstraint(name="utilisateur_mail_key", columns={"mail"})})
 * @ORM\Entity
 */
class Utilisateur
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="utilisateur_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=50, nullable=false)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="mdp", type="string", length=60, nullable=false)
     */
    private $mdp;

    /**
     * @var \Personne
     *
     * @ORM\ManyToOne(targetEntity="Personne")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_personne", referencedColumnName="id")
     * })
     */
    private $idPersonne;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Typeutilisateur", inversedBy="idUtilisateur")
     * @ORM\JoinTable(name="utilisateur_typeutilisateur",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_utilisateur", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_typeutilisateur", referencedColumnName="id")
     *   }
     * )
     */
    private $idTypeutilisateur;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idTypeutilisateur = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set mail.
     *
     * @param string $mail
     *
     * @return Utilisateur
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
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
     * Set mdp.
     *
     * @param string $mdp
     *
     * @return Utilisateur
     */
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;

        return $this;
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
     * Set idPersonne.
     *
     * @param \NatationBundle\Entity\Personne|null $idPersonne
     *
     * @return Utilisateur
     */
    public function setIdPersonne(\NatationBundle\Entity\Personne $idPersonne = null)
    {
        $this->idPersonne = $idPersonne;

        return $this;
    }

    /**
     * Get idPersonne.
     *
     * @return \NatationBundle\Entity\Personne|null
     */
    public function getIdPersonne()
    {
        return $this->idPersonne;
    }

    /**
     * Add idTypeutilisateur.
     *
     * @param \NatationBundle\Entity\Typeutilisateur $idTypeutilisateur
     *
     * @return Utilisateur
     */
    public function addIdTypeutilisateur(\NatationBundle\Entity\Typeutilisateur $idTypeutilisateur)
    {
        $this->idTypeutilisateur[] = $idTypeutilisateur;

        return $this;
    }

    /**
     * Remove idTypeutilisateur.
     *
     * @param \NatationBundle\Entity\Typeutilisateur $idTypeutilisateur
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIdTypeutilisateur(\NatationBundle\Entity\Typeutilisateur $idTypeutilisateur)
    {
        return $this->idTypeutilisateur->removeElement($idTypeutilisateur);
    }

    /**
     * Get idTypeutilisateur.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdTypeutilisateur()
    {
        return $this->idTypeutilisateur;
    }
}
