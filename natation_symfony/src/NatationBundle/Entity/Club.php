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
     * @ORM\SequenceGenerator(sequenceName="seq_club_id", allocationSize=1, initialValue=1)
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ClubPersonne", mappedBy="idClub")
     */
    private $idClubPersonne;

    /**
     * @var \Personne
     *
     * @ORM\OneToOne(targetEntity="Personne")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_personne", referencedColumnName="id")
     * })
     */
    private $idDirigent;



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
     * Set idClubPersonne.
     *
     * @param \NatationBundle\Entity\ClubPersonne|null $idClubPersonne
     *
     * @return Club
     */
    public function setIdClubPersonne(\NatationBundle\Entity\ClubPersonne $idClubPersonne = null)
    {
        $this->idPersonne = $idClubPersonne;

        return $this;
    }

    /**
     * Get idClubPersonne.
     *
     * @return \NatationBundle\Entity\ClubPersonne|null
     */
    public function getIdClubPersonne()
    {
        return $this->idClubPersonne;
    }

    /**
     * Set idDirigent.
     *
     * @param \NatationBundle\Entity\Personne|null $idDirigent
     *
     * @return Club
     */
    public function setIdDirigent(\NatationBundle\Entity\Personne $idDirigent = null)
    {
        $this->idDirigent = $idDirigent;

        return $this;
    }

    /**
     * Get idDirigent.
     *
     * @return \NatationBundle\Entity\Personne|null
     */
    public function getIdDirigent()
    {
        return $this->idDirigent;
    }
}
