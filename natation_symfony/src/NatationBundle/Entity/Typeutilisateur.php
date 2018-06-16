<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Typeutilisateur
 *
 * @ORM\Table(name="typeutilisateur", uniqueConstraints={@ORM\UniqueConstraint(name="typeutilisateur_nom_key", columns={"nom"})})
 * @ORM\Entity
 */
class Typeutilisateur
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="typeutilisateur_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=25, nullable=false)
     */
    private $nom;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="idTypeutilisateur")
     */
    private $idUtilisateur;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idUtilisateur = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nom.
     *
     * @param string $nom
     *
     * @return Typeutilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
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
     * Add idUtilisateur.
     *
     * @param \NatationBundle\Entity\Utilisateur $idUtilisateur
     *
     * @return Typeutilisateur
     */
    public function addIdUtilisateur(\NatationBundle\Entity\Utilisateur $idUtilisateur)
    {
        $this->idUtilisateur[] = $idUtilisateur;

        return $this;
    }

    /**
     * Remove idUtilisateur.
     *
     * @param \NatationBundle\Entity\Utilisateur $idUtilisateur
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIdUtilisateur(\NatationBundle\Entity\Utilisateur $idUtilisateur)
    {
        return $this->idUtilisateur->removeElement($idUtilisateur);
    }

    /**
     * Get idUtilisateur.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }
}
