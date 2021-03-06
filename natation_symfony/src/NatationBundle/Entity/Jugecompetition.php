<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Jugecompetition
 *
 * @ORM\Table(name="jugecompetition", uniqueConstraints={@ORM\UniqueConstraint(name="jugecompetition_id_competition_rang_key", columns={"id_competition", "rang"}), @ORM\UniqueConstraint(name="jugecompetition_id_utilisateur_rang_key", columns={"id_utilisateur", "rang"}), @ORM\UniqueConstraint(name="jugecompetition_id_competition_id_utilisateur_key", columns={"id_competition", "id_utilisateur"})}, indexes={@ORM\Index(name="IDX_81D2BC792F2619F9", columns={"id_typejuge"}), @ORM\Index(name="IDX_81D2BC79AD18E146", columns={"id_competition"}), @ORM\Index(name="IDX_81D2BC7950EAE44", columns={"id_utilisateur"})})
 * @ORM\Entity
 */
class Jugecompetition
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="seq_jugeCompetition_id", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="rang", type="integer", nullable=false)
     */
    private $rang;

    /**
     * @var \Typejuge
     *
     * @ORM\ManyToOne(targetEntity="Typejuge")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_typejuge", referencedColumnName="id")
     * })
     */
    private $idTypejuge;

    /**
     * @var \Competition
     *
     * @ORM\ManyToOne(targetEntity="Competition", inversedBy="idJugecompetition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_competition", referencedColumnName="id")
     * })
     */
    private $idCompetition;

    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="\NatationAuthBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_utilisateur", referencedColumnName="id")
     * })
     */
    private $idUtilisateur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Note", mappedBy="idJugecompetition")
     */
    private $idNote;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set rang.
     *
     * @param int $rang
     *
     * @return Jugecompetition
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang.
     *
     * @return int
     */
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * Set idTypejuge.
     *
     * @param \NatationBundle\Entity\Typejuge|null $idTypejuge
     *
     * @return Jugecompetition
     */
    public function setIdTypejuge(\NatationBundle\Entity\Typejuge $idTypejuge = null)
    {
        $this->idTypejuge = $idTypejuge;

        return $this;
    }

    /**
     * Get idTypejuge.
     *
     * @return \NatationBundle\Entity\Typejuge|null
     */
    public function getIdTypejuge()
    {
        return $this->idTypejuge;
    }

    /**
     * Set idCompetition.
     *
     * @param \NatationBundle\Entity\Competition|null $idCompetition
     *
     * @return Jugecompetition
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
     * Set idUtilisateur.
     *
     * @param \NatationAuthBundle\Entity\Utilisateur|null $idUtilisateur
     *
     * @return Jugecompetition
     */
    public function setIdUtilisateur(\NatationAuthBundle\Entity\Utilisateur $idUtilisateur = null)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get idUtilisateur.
     *
     * @return \NatationAuthBundle\Entity\Utilisateur|null
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    /**
     * Add idNote.
     *
     * @param \NatationBundle\Entity\Note $idNote
     *
     * @return Jugecompetition
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
}
