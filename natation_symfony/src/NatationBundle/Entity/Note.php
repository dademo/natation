<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Note
 *
 * @ORM\Table(name="equipe_jugecompetition", uniqueConstraints={@ORM\UniqueConstraint(name="equipe_jugecompetition_id_equipe_id_jugecompetition_key", columns={"id_competition", "rang"})})
 * @ORM\Entity
 */
class Note
{
    /**
     * @var int
     *
     * @ORM\Column(name="note", type="integer")
     */
    private $note;

    /**
     * @var \Jugecompetition
     *
     * @ORM\ManyToOne(targetEntity="Jugecompetition", inversedBy="idNote")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_jugeCompetition", referencedColumnName="id")
     * })
     * @ORM\OrderBy({"rang" = "ASC"})
     */
    private $idJugecompetition;

    /**
     * @var \Equipe
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Equipe", inversedBy="idNote")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_equipe", referencedColumnName="id")
     * })
     * @ORM\OrderBy({"ordrepassage" = "ASC"})
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
     * @return Note
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note.
     *
     * @return int
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set idJugecompetition.
     *
     * @param \NatationBundle\Entity\JugeCompetition|null $idTypejuge
     *
     * @return Note
     */
    public function setIdJugeCompetition(\NatationBundle\Entity\JugeCompetion $idJugecompetition = null)
    {
        $this->idJugecompetition = $idJugecompetition;

        return $this;
    }

    /**
     * Get idJugeCompetion.
     *
     * @return \NatationBundle\Entity\JugeCompetition|null
     */
    public function getIdJugeCompetition()
    {
        return $this->idJugeCidJugecompetitionompetion;
    }

    /**
     * Add idEquipe.
     *
     * @param \NatationBundle\Entity\Equipe $idEquipe
     *
     * @return Note
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