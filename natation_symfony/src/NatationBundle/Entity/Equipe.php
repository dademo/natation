<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Equipe
 *
 * @ORM\Table(name="equipe", uniqueConstraints={@ORM\UniqueConstraint(name="equipe_id_competition_ordrepassage_key", columns={"id_competition", "ordrepassage"})}, indexes={@ORM\Index(name="IDX_2449BA15AD18E146", columns={"id_competition"})})
 * @ORM\Entity
 */
class Equipe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="equipe_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=25, nullable=false)
     */
    private $nom;

    /**
     * @var int
     *
     * @ORM\Column(name="ordrepassage", type="integer", nullable=false)
     */
    private $ordrepassage;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="debut", type="datetime", nullable=true)
     */
    private $debut;

    /**
     * @var bool
     *
     * @ORM\Column(name="visionnable", type="boolean", nullable=false)
     */
    private $visionnable;

    /**
     * @var int|null
     *
     * @ORM\Column(name="penalite", type="integer", nullable=true)
     */
    private $penalite;

    /**
     * @var \Competition
     *
     * @ORM\ManyToOne(targetEntity="Competition", inversedBy="idEquipe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_competition", referencedColumnName="id")
     * })
     */
    private $idCompetition;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Jugecompetition", inversedBy="idEquipe")
     * @ORM\JoinTable(name="equipe_jugecompetition",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_equipe", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_jugecompetition", referencedColumnName="id")
     *   }
     * )
     */
    private $idJugecompetition;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Personne", inversedBy="idEquipe")
     * @ORM\JoinTable(name="equipe_personne",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_equipe", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_personne", referencedColumnName="id")
     *   }
     * )
     */
    private $idPersonne;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idJugecompetition = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idPersonne = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Equipe
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
     * Set ordrepassage.
     *
     * @param int $ordrepassage
     *
     * @return Equipe
     */
    public function setOrdrepassage($ordrepassage)
    {
        $this->ordrepassage = $ordrepassage;

        return $this;
    }

    /**
     * Get ordrepassage.
     *
     * @return int
     */
    public function getOrdrepassage()
    {
        return $this->ordrepassage;
    }

    /**
     * Set debut.
     *
     * @param \DateTime|null $debut
     *
     * @return Equipe
     */
    public function setDebut($debut = null)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * Get debut.
     *
     * @return \DateTime|null
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set visionnable.
     *
     * @param bool $visionnable
     *
     * @return Equipe
     */
    public function setVisionnable($visionnable)
    {
        $this->visionnable = $visionnable;

        return $this;
    }

    /**
     * Get visionnable.
     *
     * @return bool
     */
    public function getVisionnable()
    {
        return $this->visionnable;
    }

    /**
     * Set penalite.
     *
     * @param int|null $penalite
     *
     * @return Equipe
     */
    public function setPenalite($penalite = null)
    {
        $this->penalite = $penalite;

        return $this;
    }

    /**
     * Get penalite.
     *
     * @return int|null
     */
    public function getPenalite()
    {
        return $this->penalite;
    }

    /**
     * Set idCompetition.
     *
     * @param \NatationBundle\Entity\Competition|null $idCompetition
     *
     * @return Equipe
     */
    public function setIdCompetition(\NatationBundle\Entity\Competition $idCompetition = null)
    {
        $this->idCompetition = $idCompetition;

        return $this;
    }

    /**
     * Get idCompetition.
     *
     * @return \NatationBundle\Entity\Competition|null
     */
    public function getIdCompetition()
    {
        return $this->idCompetition;
    }

    /**
     * Add idJugecompetition.
     *
     * @param \NatationBundle\Entity\Jugecompetition $idJugecompetition
     *
     * @return Equipe
     */
    public function addIdJugecompetition(\NatationBundle\Entity\Jugecompetition $idJugecompetition)
    {
        $this->idJugecompetition[] = $idJugecompetition;

        return $this;
    }

    /**
     * Remove idJugecompetition.
     *
     * @param \NatationBundle\Entity\Jugecompetition $idJugecompetition
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIdJugecompetition(\NatationBundle\Entity\Jugecompetition $idJugecompetition)
    {
        return $this->idJugecompetition->removeElement($idJugecompetition);
    }

    /**
     * Get idJugecompetition.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdJugecompetition()
    {
        return $this->idJugecompetition;
    }

    /**
     * Add idPersonne.
     *
     * @param \NatationBundle\Entity\Personne $idPersonne
     *
     * @return Equipe
     */
    public function addIdPersonne(\NatationBundle\Entity\Personne $idPersonne)
    {
        $this->idPersonne[] = $idPersonne;

        return $this;
    }

    /**
     * Remove idPersonne.
     *
     * @param \NatationBundle\Entity\Personne $idPersonne
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIdPersonne(\NatationBundle\Entity\Personne $idPersonne)
    {
        return $this->idPersonne->removeElement($idPersonne);
    }

    /**
     * Get idPersonne.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdPersonne()
    {
        return $this->idPersonne;
    }
}
