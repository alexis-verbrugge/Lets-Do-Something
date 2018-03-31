<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Evenement
 *
 * @ORM\Table(name="evenement")
 * @ORM\Entity(repositoryClass="LDS\PlatformBundle\Repository\EvenementRepository")
 */
class Evenement
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=30, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=false)
     * @Assert\Length(min=100, minMessage="Veuillez un peu plus détailler votre évenement.")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date")
     */
    private $dateFin;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_debut", type="time")
     */
    private $heureDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fin", type="time")
     */
    private $heureFin;

    /**
     * @var \boolean
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    private $valide;

       /**
   * @ORM\ManyToOne(targetEntity="LDS\PlatformBundle\Entity\Etablissement")
   * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
   */
  private $etablissement;

  /**
     * @var string
     *
     * @ORM\Column(name="photo1", type="string", length=255, nullable=false)
      * @Assert\Image(
     *     maxSize = "8000000",
     *     mimeTypesMessage = "Votre fichier n'est pas une image",
     *     maxSizeMessage = "Votre fichier est trop gros ({{ size }}). La taille maximum autorisée est : {{ limit }}"
     * )
     */
  private $photo1;

      /**
     * @var string
     *
     * @ORM\Column(name="photo2", type="string", length=255, nullable=true)
      * @Assert\Image(
     *     maxSize = "8000000",
     *     mimeTypesMessage = "Votre fichier n'est pas une image",
     *     maxSizeMessage = "Votre fichier est trop gros ({{ size }}). La taille maximum autorisée est : {{ limit }}"
     * )
     */
  private $photo2;

      /**
     * @var string
     *
     * @ORM\Column(name="photo3", type="string", length=255, nullable=true)
      * @Assert\Image(
     *     maxSize = "8000000",
     *     mimeTypesMessage = "Votre fichier n'est pas une image",
     *     maxSizeMessage = "Votre fichier est trop gros ({{ size }}). La taille maximum autorisée est : {{ limit }}"
     * )
     */
  private $photo3;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_demande", type="datetime", nullable=true)
     */
    private $dateDemande;


    public function __construct()
  {
    $this->valide = false;
  }   


    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Evenement
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Evenement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return Evenement
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return Evenement
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set heureDebut
     *
     * @param \DateTime $heureDebut
     *
     * @return Evenement
     */
    public function setHeureDebut($heureDebut)
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    /**
     * Get heureDebut
     *
     * @return \DateTime
     */
    public function getHeureDebut()
    {
        return $this->heureDebut;
    }

    /**
     * Set heureFin
     *
     * @param \DateTime $heureFin
     *
     * @return Evenement
     */
    public function setHeureFin($heureFin)
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    /**
     * Get heureFin
     *
     * @return \DateTime
     */
    public function getHeureFin()
    {
        return $this->heureFin;
    }

    /**
     * Set etablissement
     *
     * @param \LDS\PlatformBundle\Entity\Etablissement $etablissement
     *
     * @return Evenement
     */
    public function setEtablissement(\LDS\PlatformBundle\Entity\Etablissement $etablissement)
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    /**
     * Get etablissement
     *
     * @return \LDS\PlatformBundle\Entity\Etablissement
     */
    public function getEtablissement()
    {
        return $this->etablissement;
    }

    /**
     * Set photo1
     *
     * @param string $photo1
     *
     * @return Evenement
     */
    public function setPhoto1($photo1)
    {
        $this->photo1 = $photo1;

        return $this;
    }

    /**
     * Get photo1
     *
     * @return string
     */
    public function getPhoto1()
    {
        return $this->photo1;
    }

    /**
     * Set photo2
     *
     * @param string $photo2
     *
     * @return Evenement
     */
    public function setPhoto2($photo2)
    {
        $this->photo2 = $photo2;

        return $this;
    }

    /**
     * Get photo2
     *
     * @return string
     */
    public function getPhoto2()
    {
        return $this->photo2;
    }

    /**
     * Set photo3
     *
     * @param string $photo3
     *
     * @return Evenement
     */
    public function setPhoto3($photo3)
    {
        $this->photo3 = $photo3;

        return $this;
    }

    /**
     * Get photo3
     *
     * @return string
     */
    public function getPhoto3()
    {
        return $this->photo3;
    }

    /**
     * Set valide
     *
     * @param boolean $valide
     *
     * @return Evenement
     */
    public function setValide($valide)
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * Get valide
     *
     * @return boolean
     */
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * Set dateDemande
     *
     * @param \DateTime $dateDemande
     *
     * @return Evenement
     */
    public function setDateDemande($dateDemande)
    {
        $this->dateDemande = $dateDemande;

        return $this;
    }

    /**
     * Get dateDemande
     *
     * @return \DateTime
     */
    public function getDateDemande()
    {
        return $this->dateDemande;
    }
}
