<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jugecompetition
 *
 * @ORM\Table(name="jugecompetition", uniqueConstraints={@ORM\UniqueConstraint(name="jugecompetition_id_competition_rang_key", columns={"id_competition", "rang"})}, indexes={@ORM\Index(name="IDX_81D2BC792F2619F9", columns={"id_typejuge"}), @ORM\Index(name="IDX_81D2BC79AD18E146", columns={"id_competition"}), @ORM\Index(name="IDX_81D2BC7950EAE44", columns={"id_utilisateur"})})
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
     * @ORM\SequenceGenerator(sequenceName="jugecompetition_id_seq", allocationSize=1, initialValue=1)
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
     * @ORM\ManyToOne(targetEntity="Competition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_competition", referencedColumnName="id")
     * })
     */
    private $idCompetition;

    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_utilisateur", referencedColumnName="id")
     * })
     */
    private $idUtilisateur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Equipe", mappedBy="idJugecompetition")
     */
    private $idEquipe;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idEquipe = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \NatationBundle\Entity\Utilisateur|null $idUtilisateur
     *
     * @return Jugecompetition
     */
    public function setIdUtilisateur(\NatationBundle\Entity\Utilisateur $idUtilisateur = null)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get idUtilisateur.
     *
     * @return \NatationBundle\Entity\Utilisateur|null
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    /**
     * Add idEquipe.
     *
     * @param \NatationBundle\Entity\Equipe $idEquipe
     *
     * @return Jugecompetition
     */
    public function addIdEquipe(\NatationBundle\Entity\Equipe $idEquipe)
    {
        $this->idEquipe[] = $idEquipe;

        return $this;
    }

    /**
     * Remove idEquipe.
     *
     * @param \NatationBundle\Entity\Equipe $idEquipe
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIdEquipe(\NatationBundle\Entity\Equipe $idEquipe)
    {
        return $this->idEquipe->removeElement($idEquipe);
    }

    /**
     * Get idEquipe.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdEquipe()
    {
        return $this->idEquipe;
    }
}
