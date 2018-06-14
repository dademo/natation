<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Club
 *
 * @ORM\Table(name="club", uniqueConstraints={@ORM\UniqueConstraint(name="club_nom_key", columns={"nom"})}, indexes={@ORM\Index(name="IDX_B8EE3872A477615B", columns={"id_lieu"}), @ORM\Index(name="IDX_B8EE38725F15257A", columns={"id_personne"})})
 * @ORM\Entity
 */
class Club
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="club_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * @var \Lieu
     *
     * @ORM\ManyToOne(targetEntity="Lieu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_lieu", referencedColumnName="id")
     * })
     */
    private $idLieu;

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
     * @return Club
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
     * Set idLieu.
     *
     * @param \NatationBundle\Entity\Lieu|null $idLieu
     *
     * @return Club
     */
    public function setIdLieu(\NatationBundle\Entity\Lieu $idLieu = null)
    {
        $this->idLieu = $idLieu;

        return $this;
    }

    /**
     * Get idLieu.
     *
     * @return \NatationBundle\Entity\Lieu|null
     */
    public function getIdLieu()
    {
        return $this->idLieu;
    }

    /**
     * Set idPersonne.
     *
     * @param \NatationBundle\Entity\Personne|null $idPersonne
     *
     * @return Club
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
}
