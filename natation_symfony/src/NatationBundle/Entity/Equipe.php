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
     * @ORM\SequenceGenerator(sequenceName="seq_equipe_id", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="visionnable", type="boolean", nullable=false, options={"default":false})
     */
    private $visionnable;

    /**
     * @var bool
     *
     * @ORM\Column(name="notable", type="boolean", nullable=false, options={"default":false})
     */
    private $notable;

    /**
     * @var int|null
     *
     * @ORM\Column(name="penalite", type="integer", nullable=true)
     */
    private $penalite;

    /**
     * @var \Competition
     *
     * @ORM\ManyToOne(targetEntity="Competition", inversedBy="idEquipe", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_competition", referencedColumnName="id")
     * })
     */
    private $idCompetition;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Note", mappedBy="idEquipe")
     */
    private $idNote;

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
        $this->idPersonne = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idNote = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get notable.
     *
     * @return bool
     */
    public function getNotable()
    {
        return $this->notable;
    }

    /**
     * Set notable.
     *
     * @param bool $notable
     *
     * @return Equipe
     */
    public function setNotable($notable)
    {
        $this->notable = $notable;

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
        $this->penalite = $penalite * 2;

        return $this;
    }

    /**
     * Get penalite.
     *
     * @return int|null
     */
    public function getPenalite()
    {
        return $this->penalite * 0.5;
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
     * Add idNote.
     *
     * @param \NatationBundle\Entity\Note $idNote
     *
     * @return Equipe
     */
    public function addIdNote(\NatationBundle\Entity\Note $idNote)
    {
        $this->idNote[] = $idNote;

        return $this;
    }

    /**
     * Remove idNote.
     *
     * @param \NatationBundle\Entity\Note $idNote
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIdNote(\NatationBundle\Entity\Note $idNote)
    {
        return $this->idNote->removeElement($idNote);
    }

    /**
     * Get idNote.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdNote()
    {
        return $this->idNote;
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
        // On vérifie que la personne n'est pas déjà dans l'entité

        foreach($this->idPersonne as $personne) {
            if($personne->getId() == $idPersonne->getid()) {
                return $this;
            }
        }

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
     * Clean idPersonne.
     *
     * @return Equipe
     */
    public function cleanIdPersonne()
    {
        $this->idPersonne = new \Doctrine\Common\Collections\ArrayCollection();

        return $this;
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

    /**
     * Get note.
     *
     * @return integer
     */
    public function getNote()
    {
        $note = 0;
        $nArbitres = 0;

        foreach($this->idNote as $_note) {
            $note += $_note->getNote();
            $nArbitres ++;
        }

        $note /= $nArbitres;

        $note -= $this->penalite * 0.5;

        return $note;
    }
}
