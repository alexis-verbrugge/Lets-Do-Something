<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Etablissement
 *
 * @ORM\Table(name="etablissement")
 * @ORM\Entity(repositoryClass="LDS\PlatformBundle\Repository\EtablissementRepository")
 */
class Etablissement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="siret", type="integer", unique=true)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="nomEtablissement", type="string", length=255)
     */
    private $nomEtablissement;

    /**
     * @var string
     *
     * @ORM\Column(name="proprietaire", type="string", length=255)
     */
    private $proprietaire;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255)
     */
    private $ville;

    /**
     * @var int
     *
     * @ORM\Column(name="code_postal", type="integer")
     * @Assert\Length(min=5,max=5, minMessage="Votre code postal doit faire 5 caractères.")
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255)
     * @Assert\Length(min=10,max=10, minMessage="Votre numéro de téléphone doit faire 10 chiffres.")
     */
    private $telephone;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $lattitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\Length(min=150, minMessage="Veuillez un peu plus détailler votre établissement.")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_ouverture", type="time", nullable=true)
     */
    private $heureOuverture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fermeture", type="time", nullable=true)
     */
    private $heureFermeture;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_ouverture_lundi", type="time", nullable=true)
     */
    private $heureOuvertureLundi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fermeture_lundi", type="time", nullable=true)
     */
    private $heureFermetureLundi;

        /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_ouverture_mardi", type="time", nullable=true)
     */
    private $heureOuvertureMardi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fermeture_mardi", type="time", nullable=true)
     */
    private $heureFermetureMardi;

        /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_ouverture_mercredi", type="time", nullable=true)
     */
    private $heureOuvertureMercredi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fermeture_mercredi", type="time", nullable=true)
     */
    private $heureFermetureMercredi;

        /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_ouverture_jeudi", type="time", nullable=true)
     */
    private $heureOuvertureJeudi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fermeture_jeudi", type="time", nullable=true)
     */
    private $heureFermetureJeudi;

        /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_ouverture_vendredi", type="time", nullable=true)
     */
    private $heureOuvertureVendredi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fermeture_vendredi", type="time", nullable=true)
     */
    private $heureFermetureVendredi;

        /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_ouverture_samedi", type="time", nullable=true)
     */
    private $heureOuvertureSamedi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fermeture_samedi", type="time", nullable=true)
     */
    private $heureFermetureSamedi;

        /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_ouverture_dimanche", type="time", nullable=true)
     */
    private $heureOuvertureDimanche;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fermeture_dimanche", type="time", nullable=true)
     */
    private $heureFermetureDimanche;


    /**
     * @var float
     *
     * @ORM\Column(name="moyenne", type="float")
     */
    private $moyenne;

    /**
     * @var int
     *
     * @ORM\Column(name="nombre_note", type="integer")
     */
    private $nombreNote;

    /**
     * @var float
     *
     * @ORM\Column(name="coefficient", type="float")
     */
    private $coefficient;

    /**
     * @var boolean
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    private $valide;

      /**
       * @ORM\ManyToMany(targetEntity="LDS\PlatformBundle\Entity\Tag", cascade={"persist"})
       */
  private $tags;

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
     * @var string
     *
     * @ORM\Column(name="photo4", type="string", length=255, nullable=true)
      * @Assert\Image(
     *     maxSize = "8000000",
     *     mimeTypesMessage = "Votre fichier n'est pas une image",
     *     maxSizeMessage = "Votre fichier est trop gros ({{ size }}). La taille maximum autorisée est : {{ limit }}"
     * )
     */
  private $photo4;

   /**
     * @var string
     *
     * @ORM\Column(name="photo5", type="string", length=255, nullable=true)
      * @Assert\Image(
     *     maxSize = "8000000",
     *     mimeTypesMessage = "Votre fichier n'est pas une image",
     *     maxSizeMessage = "Votre fichier est trop gros ({{ size }}). La taille maximum autorisée est : {{ limit }}"
     * )
     */
  private $photo5;

   /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_demande", type="datetime", nullable=true)
     */
    private $dateDemande;

    /**
     * @var int
     *
     * @ORM\Column(name="distance", type="integer", nullable=true)
     */
    private $distance;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;





public function __construct()
  {
    $this->lattitude = 0.0;
    $this->longitude = 0.0;
    $this->moyenne = 0.0;
    $this->nombreNote = 0;
    $this->coefficient=0.0;
    $this->valide = false;
  }   

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set siret
     *
     * @param integer $siret
     *
     * @return Etablissement
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return int
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Etablissement
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
     * Set proprietaire
     *
     * @param string $proprietaire
     *
     * @return Etablissement
     */
    public function setProprietaire($proprietaire)
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    /**
     * Get proprietaire
     *
     * @return string
     */
    public function getProprietaire()
    {
        return $this->proprietaire;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Etablissement
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set codePostal
     *
     * @param integer $codePostal
     *
     * @return Etablissement
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return int
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Etablissement
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Etablissement
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set lattitude
     *
     * @param float $lattitude
     *
     * @return Etablissement
     */
    public function setLattitude($lattitude)
    {
        $this->lattitude = $lattitude;

        return $this;
    }

    /**
     * Get lattitude
     *
     * @return float
     */
    public function getLattitude()
    {
        return $this->lattitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return Etablissement
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Etablissement
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
     * Set heureOuverture
     *
     * @param \DateTime $heureOuverture
     *
     * @return Etablissement
     */
    public function setHeureOuverture($heureOuverture)
    {
        $this->heureOuverture = $heureOuverture;

        return $this;
    }

    /**
     * Get heureOuverture
     *
     * @return \DateTime
     */
    public function getHeureOuverture()
    {
        return $this->heureOuverture;
    }

    /**
     * Set heureFermeture
     *
     * @param \DateTime $heureFermeture
     *
     * @return Etablissement
     */
    public function setHeureFermeture($heureFermeture)
    {
        $this->heureFermeture = $heureFermeture;

        return $this;
    }

    /**
     * Get heureFermeture
     *
     * @return \DateTime
     */
    public function getHeureFermeture()
    {
        return $this->heureFermeture;
    }

    /**
     * Set moyenne
     *
     * @param float $moyenne
     *
     * @return Etablissement
     */
    public function setMoyenne($moyenne)
    {
        $this->moyenne = $moyenne;

        return $this;
    }

    /**
     * Get moyenne
     *
     * @return float
     */
    public function getMoyenne()
    {
        return $this->moyenne;
    }

    /**
     * Set nombreNote
     *
     * @param integer $nombreNote
     *
     * @return Etablissement
     */
    public function setNombreNote($nombreNote)
    {
        $this->nombreNote = $nombreNote;

        return $this;
    }

    /**
     * Get nombreNote
     *
     * @return int
     */
    public function getNombreNote()
    {
        return $this->nombreNote;
    }

    /**
     * Set nomEtablissement
     *
     * @param string $nomEtablissement
     *
     * @return Etablissement
     */
    public function setNomEtablissement($nomEtablissement)
    {
        $this->nomEtablissement = $nomEtablissement;

        return $this;
    }

    /**
     * Get nomEtablissement
     *
     * @return string
     */
    public function getNomEtablissement()
    {
        return $this->nomEtablissement;
    }

    /**
     * Set valide
     *
     * @param boolean $valide
     *
     * @return Etablissement
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
     * Add tag
     *
     * @param \LDS\PlatformBundle\Entity\Tag $tag
     *
     * @return Etablissement
     */
    public function addTag(\LDS\PlatformBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \LDS\PlatformBundle\Entity\Tag $tag
     */
    public function removeTag(\LDS\PlatformBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set photo1
     *
     * @param string $photo1
     *
     * @return Etablissement
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
     * @return Etablissement
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
     * @return Etablissement
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
     * Set photo4
     *
     * @param string $photo4
     *
     * @return Etablissement
     */
    public function setPhoto4($photo4)
    {
        $this->photo4 = $photo4;

        return $this;
    }

    /**
     * Get photo4
     *
     * @return string
     */
    public function getPhoto4()
    {
        return $this->photo4;
    }

    /**
     * Set photo5
     *
     * @param string $photo5
     *
     * @return Etablissement
     */
    public function setPhoto5($photo5)
    {
        $this->photo5 = $photo5;

        return $this;
    }

    /**
     * Get photo5
     *
     * @return string
     */
    public function getPhoto5()
    {
        return $this->photo5;
    }

    /**
     * Set coefficient
     *
     * @param float $coefficient
     *
     * @return Etablissement
     */
    public function setCoefficient($coefficient)
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * Get coefficient
     *
     * @return float
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }

    /**
     * Set dateDemande
     *
     * @param \DateTime $dateDemande
     *
     * @return Etablissement
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

    /**
     * Set distance
     *
     * @param integer $distance
     *
     * @return Etablissement
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Etablissement
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set heureOuvertureLundi
     *
     * @param \DateTime $heureOuvertureLundi
     *
     * @return Etablissement
     */
    public function setHeureOuvertureLundi($heureOuvertureLundi)
    {
        $this->heureOuvertureLundi = $heureOuvertureLundi;

        return $this;
    }

    /**
     * Get heureOuvertureLundi
     *
     * @return \DateTime
     */
    public function getHeureOuvertureLundi()
    {
        return $this->heureOuvertureLundi;
    }

    /**
     * Set heureFermetureLundi
     *
     * @param \DateTime $heureFermetureLundi
     *
     * @return Etablissement
     */
    public function setHeureFermetureLundi($heureFermetureLundi)
    {
        $this->heureFermetureLundi = $heureFermetureLundi;

        return $this;
    }

    /**
     * Get heureFermetureLundi
     *
     * @return \DateTime
     */
    public function getHeureFermetureLundi()
    {
        return $this->heureFermetureLundi;
    }

    /**
     * Set heureOuvertureMardi
     *
     * @param \DateTime $heureOuvertureMardi
     *
     * @return Etablissement
     */
    public function setHeureOuvertureMardi($heureOuvertureMardi)
    {
        $this->heureOuvertureMardi = $heureOuvertureMardi;

        return $this;
    }

    /**
     * Get heureOuvertureMardi
     *
     * @return \DateTime
     */
    public function getHeureOuvertureMardi()
    {
        return $this->heureOuvertureMardi;
    }

    /**
     * Set heureFermetureMardi
     *
     * @param \DateTime $heureFermetureMardi
     *
     * @return Etablissement
     */
    public function setHeureFermetureMardi($heureFermetureMardi)
    {
        $this->heureFermetureMardi = $heureFermetureMardi;

        return $this;
    }

    /**
     * Get heureFermetureMardi
     *
     * @return \DateTime
     */
    public function getHeureFermetureMardi()
    {
        return $this->heureFermetureMardi;
    }

    /**
     * Set heureOuvertureMercredi
     *
     * @param \DateTime $heureOuvertureMercredi
     *
     * @return Etablissement
     */
    public function setHeureOuvertureMercredi($heureOuvertureMercredi)
    {
        $this->heureOuvertureMercredi = $heureOuvertureMercredi;

        return $this;
    }

    /**
     * Get heureOuvertureMercredi
     *
     * @return \DateTime
     */
    public function getHeureOuvertureMercredi()
    {
        return $this->heureOuvertureMercredi;
    }

    /**
     * Set heureFermetureMercredi
     *
     * @param \DateTime $heureFermetureMercredi
     *
     * @return Etablissement
     */
    public function setHeureFermetureMercredi($heureFermetureMercredi)
    {
        $this->heureFermetureMercredi = $heureFermetureMercredi;

        return $this;
    }

    /**
     * Get heureFermetureMercredi
     *
     * @return \DateTime
     */
    public function getHeureFermetureMercredi()
    {
        return $this->heureFermetureMercredi;
    }

    /**
     * Set heureOuvertureJeudi
     *
     * @param \DateTime $heureOuvertureJeudi
     *
     * @return Etablissement
     */
    public function setHeureOuvertureJeudi($heureOuvertureJeudi)
    {
        $this->heureOuvertureJeudi = $heureOuvertureJeudi;

        return $this;
    }

    /**
     * Get heureOuvertureJeudi
     *
     * @return \DateTime
     */
    public function getHeureOuvertureJeudi()
    {
        return $this->heureOuvertureJeudi;
    }

    /**
     * Set heureFermetureJeudi
     *
     * @param \DateTime $heureFermetureJeudi
     *
     * @return Etablissement
     */
    public function setHeureFermetureJeudi($heureFermetureJeudi)
    {
        $this->heureFermetureJeudi = $heureFermetureJeudi;

        return $this;
    }

    /**
     * Get heureFermetureJeudi
     *
     * @return \DateTime
     */
    public function getHeureFermetureJeudi()
    {
        return $this->heureFermetureJeudi;
    }

    /**
     * Set heureOuvertureVendredi
     *
     * @param \DateTime $heureOuvertureVendredi
     *
     * @return Etablissement
     */
    public function setHeureOuvertureVendredi($heureOuvertureVendredi)
    {
        $this->heureOuvertureVendredi = $heureOuvertureVendredi;

        return $this;
    }

    /**
     * Get heureOuvertureVendredi
     *
     * @return \DateTime
     */
    public function getHeureOuvertureVendredi()
    {
        return $this->heureOuvertureVendredi;
    }

    /**
     * Set heureFermetureVendredi
     *
     * @param \DateTime $heureFermetureVendredi
     *
     * @return Etablissement
     */
    public function setHeureFermetureVendredi($heureFermetureVendredi)
    {
        $this->heureFermetureVendredi = $heureFermetureVendredi;

        return $this;
    }

    /**
     * Get heureFermetureVendredi
     *
     * @return \DateTime
     */
    public function getHeureFermetureVendredi()
    {
        return $this->heureFermetureVendredi;
    }

    /**
     * Set heureOuvertureSamedi
     *
     * @param \DateTime $heureOuvertureSamedi
     *
     * @return Etablissement
     */
    public function setHeureOuvertureSamedi($heureOuvertureSamedi)
    {
        $this->heureOuvertureSamedi = $heureOuvertureSamedi;

        return $this;
    }

    /**
     * Get heureOuvertureSamedi
     *
     * @return \DateTime
     */
    public function getHeureOuvertureSamedi()
    {
        return $this->heureOuvertureSamedi;
    }

    /**
     * Set heureFermetureSamedi
     *
     * @param \DateTime $heureFermetureSamedi
     *
     * @return Etablissement
     */
    public function setHeureFermetureSamedi($heureFermetureSamedi)
    {
        $this->heureFermetureSamedi = $heureFermetureSamedi;

        return $this;
    }

    /**
     * Get heureFermetureSamedi
     *
     * @return \DateTime
     */
    public function getHeureFermetureSamedi()
    {
        return $this->heureFermetureSamedi;
    }

    /**
     * Set heureOuvertureDimanche
     *
     * @param \DateTime $heureOuvertureDimanche
     *
     * @return Etablissement
     */
    public function setHeureOuvertureDimanche($heureOuvertureDimanche)
    {
        $this->heureOuvertureDimanche = $heureOuvertureDimanche;

        return $this;
    }

    /**
     * Get heureOuvertureDimanche
     *
     * @return \DateTime
     */
    public function getHeureOuvertureDimanche()
    {
        return $this->heureOuvertureDimanche;
    }

    /**
     * Set heureFermetureDimanche
     *
     * @param \DateTime $heureFermetureDimanche
     *
     * @return Etablissement
     */
    public function setHeureFermetureDimanche($heureFermetureDimanche)
    {
        $this->heureFermetureDimanche = $heureFermetureDimanche;

        return $this;
    }

    /**
     * Get heureFermetureDimanche
     *
     * @return \DateTime
     */
    public function getHeureFermetureDimanche()
    {
        return $this->heureFermetureDimanche;
    }
}
