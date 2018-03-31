<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FicheSoiree
 *
 * @ORM\Table(name="fiche_soiree")
 * @ORM\Entity
 */
class FicheSoiree
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=30, nullable=false)
     */
    private $nom;

   
    /**
   * @ORM\ManyToOne(targetEntity="LDS\PlatformBundle\Entity\Particulier")
   * @ORM\JoinColumn(nullable=false)
   */
    private $admin;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=300, nullable=false)
     */
    private $description;

    
       /**
   * @ORM\ManyToOne(targetEntity="LDS\PlatformBundle\Entity\Etablissement")
   * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
   */
    private $etablissement;

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
     * @ORM\Column(name="heure_debut", type="time", nullable=false)
     */

    private $heureDebut;

    /**
     *@var \DateTime
     *
     * @ORM\Column(name="heure_fin", type="time", nullable=false)
     */

    private $heureFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;





    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return FicheSoiree
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
     * @return FicheSoiree
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
     * Set heureDebut
     *
     * @param \DateTime $heureDebut
     *
     * @return FicheSoiree
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
     * @return FicheSoiree
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
     * Set admin
     *
     * @param \LDS\PlatformBundle\Entity\Particulier $admin
     *
     * @return FicheSoiree
     */
    public function setAdmin(\LDS\PlatformBundle\Entity\Particulier $admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \LDS\PlatformBundle\Entity\Particulier
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set etablissement
     *
     * @param \LDS\PlatformBundle\Entity\Etablissement $etablissement
     *
     * @return FicheSoiree
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return FicheSoiree
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
