<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Competition
 *
 * @ORM\Table(name="competition", indexes={@ORM\Index(name="IDX_B50A2CB1A477615B", columns={"id_lieu"})})
 * @ORM\Entity
 */
class Competition
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="competition_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=50, nullable=false)
     */
    private $titre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datecompetition", type="date", nullable=false)
     */
    private $datecompetition;

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
     * @var Jugecompetition
     *
     * @ORM\OneToMany(targetEntity="Jugecompetition", mappedBy="idCompetition")
     * @Assert\Count(
     *      max = 5,
     *      maxMessage = "You cannot specify more than {{ limit }} judges"
     * )
     */
    private $idJugecompetition;

    /**
     * @var Equipe
     *
     * @ORM\OneToMany(targetEntity="Equipe", mappedBy="idCompetition")
     */
    private $idEquipe;



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
     * Set titre.
     *
     * @param string $titre
     *
     * @return Competition
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre.
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set datecompetition.
     *
     * @param \DateTime $datecompetition
     *
     * @return Competition
     */
    public function setDatecompetition($datecompetition)
    {
        $this->datecompetition = $datecompetition;

        return $this;
    }

    /**
     * Get datecompetition.
     *
     * @return \DateTime
     */
    public function getDatecompetition()
    {
        return $this->datecompetition;
    }

    /**
     * Set idLieu.
     *
     * @param \NatationBundle\Entity\Lieu|null $idLieu
     *
     * @return Competition
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
     * Set idJugecompetition.
     *
     * @param \NatationBundle\Entity\Jugecompetition[]|null $idLieu
     *
     * @return \NatationBundle\Entity\Jugecompetition|null
     */
    public function setIdJugecompetition(\NatationBundle\Entity\Jugecompetition $idJugecompetition = null)
    {
        $this->idJugeCompetition = $idJugecompetition;

        return $this;
    }

    /**
     * Get idJugecompetition.
     *
     * @return \NatationBundle\Entity\Jugecompetition|null
     */
    public function getIdJugecompetition()
    {
        return $this->idJugecompetition;
    }

    /**
     * Set idEquipe.
     *
     * @param \NatationBundle\Entity\Equipe[]|null $idLieu
     *
     * @return \NatationBundle\Entity\Equipe|null
     */
    public function setIdEquipe(\NatationBundle\Entity\Equipe $idEquipe = null)
    {
        $this->idEquipe = $idEquipe;

        return $this;
    }

    /**
     * Get idEquipe.
     *
     * @return \NatationBundle\Entity\Equipe|null
     */
    public function getIdEquipe()
    {
        return $this->idEquipe;
    }
}
