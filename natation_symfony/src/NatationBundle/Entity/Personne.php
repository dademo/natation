<?php

namespace NatationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Personne
 *
 * @ORM\Table(name="personne")
 * @ORM\Entity
 */
class Personne
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="seq_personne_id", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=50, nullable=false)
     */
    private $prenom;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datenaissance", type="date", nullable=true)
     */
    private $datenaissance;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Equipe", mappedBy="idPersonne")
     */
    private $idEquipe;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ClubPersonne", mappedBy="idPersonne", fetch="EAGER")
     */
    private $idClubPersonne;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idEquipe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idClubPersonne = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Personne
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
     * Set prenom.
     *
     * @param string $prenom
     *
     * @return Personne
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom.
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set datenaissance.
     *
     * @param \DateTime|null $datenaissance
     *
     * @return Personne
     */
    public function setDatenaissance($datenaissance = null)
    {
        $this->datenaissance = $datenaissance;

        return $this;
    }

    /**
     * Get datenaissance.
     *
     * @return \DateTime|null
     */
    public function getDatenaissance()
    {
        return $this->datenaissance;
    }

    /**
     * Add idEquipe.
     *
     * @param \NatationBundle\Entity\Equipe $idEquipe
     *
     * @return Personne
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

    /**
     * __toString function
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->nom . ' ' . $this->prenom;
    }

    /**
     * Get idClubPersonne.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdClubPersonne()
    {
        return $this->idClubPersonne;
    }

    /**
     * Add idClubPersonne.
     *
     * @param \NatationBundle\Entity\ClubPersonne $idClubPersonne
     *
     * @return Personne
     */
    public function addIdClubPersonne(\NatationBundle\Entity\ClubPersonne $idEquipe)
    {
        $this->idClubPersonne[] = $idClubPersonne;

        return $this;
    }

    /**
     * Remove idClubPersonne.
     *
     * @param \NatationBundle\Entity\ClubPersonne $idClubPersonne
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIdClubPersonne(\NatationBundle\Entity\ClubPersonne $idClubPersonne)
    {
        return $this->idClubPersonne->removeElement($idClubPersonne);
    }


    /**
     * Get current idClubPersonne.
     *
     * @return \NatationBundle\Entity\ClubPersonne The current club_personne relation
     */
    public function getCurrIdClubPersonne()
    {
        foreach ($this->idClubPersonne as $clubPersonne) {
            $dateFinInscription = $clubPersonne->getDatefininscription();
            if($dateFinInscription === null || $this->_moreOrSameDay($dateFinInscription, new \DateTime('now'))) {
                return $clubPersonne;
            }
        }

        return null;
    }

    /**
     * Get current equipe.
     *
     * @return \NatationBundle\Entity\Equipe 
     */
    public function getCurrIdEquipe()
    {
        foreach ($this->idEquipe as $equipe) {
            if ($this->_sameDay($equipe->getIdCompetition()->getDatecompetition(), new \DateTime("now"))) {
                return $equipe;
            }
        }

        return null;
    }

    /**
     * Check if the two dates are in the same day
     * 
     * @return boolean
     */
    private function _moreOrSameDay(\DateTime $date1, \DateTime $date2)
    {
        if($date1->format('y') < $date2->format('y')) {
            return false;
        }

        if($date1->format('m') < $date2->format('m')) {
            return true;
        }

        if($date1->format('d') >= $date2->format('d')) {
            return true;
        } else {
            return false;
        }
    }

    private function _sameDay(\DateTime $date1, \DateTime $date2)
    {
        if($date1->format('y') != $date2->format('y')) {
            return false;
        }

        if($date1->format('m') != $date2->format('m')) {
            return true;
        }

        if($date1->format('d') == $date2->format('d')) {
            return true;
        } else {
            return false;
        }
    }
}
