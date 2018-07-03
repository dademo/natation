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
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
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
     * @ORM\ManyToOne(targetEntity="Equipe", inversedBy="idNote")
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
     * @param \NatationBundle\Entity\Jugecompetition|null $idTypejuge
     *
     * @return Note
     */
    public function setIdJugeCompetition(\NatationBundle\Entity\Jugecompetition $idJugecompetition = null)
    {
        $this->idJugecompetition = $idJugecompetition;

        return $this;
    }

    /**
     * Get idJugecompetion.
     *
     * @return \NatationBundle\Entity\Jugecompetition|null
     */
    public function getIdJugeCompetition()
    {
        return $this->idJugecompetition;
    }

    /**
     * Set idEquipe.
     *
     * @param \NatationBundle\Entity\Equipe $idEquipe
     *
     * @return Note
     */
    public function setIdEquipe(\NatationBundle\Entity\Equipe $idEquipe)
    {
        $this->idEquipe = $idEquipe;

        return $this;
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

    public function __toString()
    {
        return ($this->note === null)? "-" : strval($this->note);
    }
}
