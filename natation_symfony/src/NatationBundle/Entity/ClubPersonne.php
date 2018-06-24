<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClubPersonne
 *
 * @ORM\Table(name="club_personne", indexes={@ORM\Index(name="IDX_C75634633CE2470", columns={"id_club"}), @ORM\Index(name="IDX_C7563465F15257A", columns={"id_personne"})})
 * @ORM\Entity
 */
class ClubPersonne
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="seq_club_personne_id", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateinscription", type="date", nullable=false)
     */
    private $dateinscription;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datefininscription", type="date", nullable=true)
     */
    private $datefininscription;

    /**
     * @var \Club
     *
     * @ORM\ManyToOne(targetEntity="Club")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_club", referencedColumnName="id")
     * })
     */
    private $idClub;

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
     * Set dateinscription.
     *
     * @param \DateTime $dateinscription
     *
     * @return ClubPersonne
     */
    public function setDateinscription($dateinscription)
    {
        $this->dateinscription = $dateinscription;

        return $this;
    }

    /**
     * Get dateinscription.
     *
     * @return \DateTime
     */
    public function getDateinscription()
    {
        return $this->dateinscription;
    }

    /**
     * Set datefininscription.
     *
     * @param \DateTime|null $datefininscription
     *
     * @return ClubPersonne
     */
    public function setDatefininscription($datefininscription = null)
    {
        $this->datefininscription = $datefininscription;

        return $this;
    }

    /**
     * Get datefininscription.
     *
     * @return \DateTime|null
     */
    public function getDatefininscription()
    {
        return $this->datefininscription;
    }

    /**
     * Set idClub.
     *
     * @param \NatationBundle\Entity\Club|null $idClub
     *
     * @return ClubPersonne
     */
    public function setIdClub(\NatationBundle\Entity\Club $idClub = null)
    {
        $this->idClub = $idClub;

        return $this;
    }

    /**
     * Get idClub.
     *
     * @return \NatationBundle\Entity\Club|null
     */
    public function getIdClub()
    {
        return $this->idClub;
    }

    /**
     * Set idPersonne.
     *
     * @param \NatationBundle\Entity\Personne|null $idPersonne
     *
     * @return ClubPersonne
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
