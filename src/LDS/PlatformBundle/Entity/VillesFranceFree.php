<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VillesFranceFree
 *
 * @ORM\Table(name="villes_france_free")
 * @ORM\Entity(repositoryClass="LDS\PlatformBundle\Repository\VillesFranceFreeRepository")
 */

class VillesFranceFree
{
    /**
     * @var string
     *
     * @ORM\Column(name="ville_departement", type="string", length=3, nullable=true)
     */
    private $villeDepartement;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_nom", type="string", length=45, nullable=true)
     */
    private $villeNom;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_nom_reel", type="string", length=45, nullable=true)
     */
    private $villeNomReel;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_code_postal", type="string", length=255, nullable=true)
     */
    private $villeCodePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_commune", type="string", length=3, nullable=true)
     */
    private $villeCommune;

    /**
     * @var integer
     *
     * @ORM\Column(name="ville_densite_2010", type="integer", nullable=true)
     */
    private $villeDensite2010;

    /**
     * @var float
     *
     * @ORM\Column(name="ville_surface", type="float", precision=10, scale=0, nullable=true)
     */
    private $villeSurface;

    /**
     * @var float
     *
     * @ORM\Column(name="ville_longitude_deg", type="float", precision=10, scale=0, nullable=true)
     */
    private $villeLongitudeDeg;

    /**
     * @var float
     *
     * @ORM\Column(name="ville_latitude_deg", type="float", precision=10, scale=0, nullable=true)
     */
    private $villeLatitudeDeg;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_longitude_grd", type="string", length=9, nullable=true)
     */
    private $villeLongitudeGrd;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_latitude_grd", type="string", length=8, nullable=true)
     */
    private $villeLatitudeGrd;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_longitude_dms", type="string", length=9, nullable=true)
     */
    private $villeLongitudeDms;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_latitude_dms", type="string", length=8, nullable=true)
     */
    private $villeLatitudeDms;

    /**
     * @var integer
     *
     * @ORM\Column(name="ville_zmin", type="integer", nullable=true)
     */
    private $villeZmin;

    /**
     * @var integer
     *
     * @ORM\Column(name="ville_zmax", type="integer", nullable=true)
     */
    private $villeZmax;

    /**
     * @var integer
     *
     * @ORM\Column(name="ville_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $villeId;



    /**
     * Set villeDepartement
     *
     * @param string $villeDepartement
     *
     * @return VillesFranceFree
     */
    public function setVilleDepartement($villeDepartement)
    {
        $this->villeDepartement = $villeDepartement;

        return $this;
    }

    /**
     * Get villeDepartement
     *
     * @return string
     */
    public function getVilleDepartement()
    {
        return $this->villeDepartement;
    }

    /**
     * Set villeNom
     *
     * @param string $villeNom
     *
     * @return VillesFranceFree
     */
    public function setVilleNom($villeNom)
    {
        $this->villeNom = $villeNom;

        return $this;
    }

    /**
     * Get villeNom
     *
     * @return string
     */
    public function getVilleNom()
    {
        return $this->villeNom;
    }

    /**
     * Set villeNomReel
     *
     * @param string $villeNomReel
     *
     * @return VillesFranceFree
     */
    public function setVilleNomReel($villeNomReel)
    {
        $this->villeNomReel = $villeNomReel;

        return $this;
    }

    /**
     * Get villeNomReel
     *
     * @return string
     */
    public function getVilleNomReel()
    {
        return $this->villeNomReel;
    }

    /**
     * Set villeCodePostal
     *
     * @param string $villeCodePostal
     *
     * @return VillesFranceFree
     */
    public function setVilleCodePostal($villeCodePostal)
    {
        $this->villeCodePostal = $villeCodePostal;

        return $this;
    }

    /**
     * Get villeCodePostal
     *
     * @return string
     */
    public function getVilleCodePostal()
    {
        return $this->villeCodePostal;
    }

    /**
     * Set villeCommune
     *
     * @param string $villeCommune
     *
     * @return VillesFranceFree
     */
    public function setVilleCommune($villeCommune)
    {
        $this->villeCommune = $villeCommune;

        return $this;
    }

    /**
     * Get villeCommune
     *
     * @return string
     */
    public function getVilleCommune()
    {
        return $this->villeCommune;
    }

    /**
     * Set villeDensite2010
     *
     * @param integer $villeDensite2010
     *
     * @return VillesFranceFree
     */
    public function setVilleDensite2010($villeDensite2010)
    {
        $this->villeDensite2010 = $villeDensite2010;

        return $this;
    }

    /**
     * Get villeDensite2010
     *
     * @return integer
     */
    public function getVilleDensite2010()
    {
        return $this->villeDensite2010;
    }

    /**
     * Set villeSurface
     *
     * @param float $villeSurface
     *
     * @return VillesFranceFree
     */
    public function setVilleSurface($villeSurface)
    {
        $this->villeSurface = $villeSurface;

        return $this;
    }

    /**
     * Get villeSurface
     *
     * @return float
     */
    public function getVilleSurface()
    {
        return $this->villeSurface;
    }

    /**
     * Set villeLongitudeDeg
     *
     * @param float $villeLongitudeDeg
     *
     * @return VillesFranceFree
     */
    public function setVilleLongitudeDeg($villeLongitudeDeg)
    {
        $this->villeLongitudeDeg = $villeLongitudeDeg;

        return $this;
    }

    /**
     * Get villeLongitudeDeg
     *
     * @return float
     */
    public function getVilleLongitudeDeg()
    {
        return $this->villeLongitudeDeg;
    }

    /**
     * Set villeLatitudeDeg
     *
     * @param float $villeLatitudeDeg
     *
     * @return VillesFranceFree
     */
    public function setVilleLatitudeDeg($villeLatitudeDeg)
    {
        $this->villeLatitudeDeg = $villeLatitudeDeg;

        return $this;
    }

    /**
     * Get villeLatitudeDeg
     *
     * @return float
     */
    public function getVilleLatitudeDeg()
    {
        return $this->villeLatitudeDeg;
    }

    /**
     * Set villeLongitudeGrd
     *
     * @param string $villeLongitudeGrd
     *
     * @return VillesFranceFree
     */
    public function setVilleLongitudeGrd($villeLongitudeGrd)
    {
        $this->villeLongitudeGrd = $villeLongitudeGrd;

        return $this;
    }

    /**
     * Get villeLongitudeGrd
     *
     * @return string
     */
    public function getVilleLongitudeGrd()
    {
        return $this->villeLongitudeGrd;
    }

    /**
     * Set villeLatitudeGrd
     *
     * @param string $villeLatitudeGrd
     *
     * @return VillesFranceFree
     */
    public function setVilleLatitudeGrd($villeLatitudeGrd)
    {
        $this->villeLatitudeGrd = $villeLatitudeGrd;

        return $this;
    }

    /**
     * Get villeLatitudeGrd
     *
     * @return string
     */
    public function getVilleLatitudeGrd()
    {
        return $this->villeLatitudeGrd;
    }

    /**
     * Set villeLongitudeDms
     *
     * @param string $villeLongitudeDms
     *
     * @return VillesFranceFree
     */
    public function setVilleLongitudeDms($villeLongitudeDms)
    {
        $this->villeLongitudeDms = $villeLongitudeDms;

        return $this;
    }

    /**
     * Get villeLongitudeDms
     *
     * @return string
     */
    public function getVilleLongitudeDms()
    {
        return $this->villeLongitudeDms;
    }

    /**
     * Set villeLatitudeDms
     *
     * @param string $villeLatitudeDms
     *
     * @return VillesFranceFree
     */
    public function setVilleLatitudeDms($villeLatitudeDms)
    {
        $this->villeLatitudeDms = $villeLatitudeDms;

        return $this;
    }

    /**
     * Get villeLatitudeDms
     *
     * @return string
     */
    public function getVilleLatitudeDms()
    {
        return $this->villeLatitudeDms;
    }

    /**
     * Set villeZmin
     *
     * @param integer $villeZmin
     *
     * @return VillesFranceFree
     */
    public function setVilleZmin($villeZmin)
    {
        $this->villeZmin = $villeZmin;

        return $this;
    }

    /**
     * Get villeZmin
     *
     * @return integer
     */
    public function getVilleZmin()
    {
        return $this->villeZmin;
    }

    /**
     * Set villeZmax
     *
     * @param integer $villeZmax
     *
     * @return VillesFranceFree
     */
    public function setVilleZmax($villeZmax)
    {
        $this->villeZmax = $villeZmax;

        return $this;
    }

    /**
     * Get villeZmax
     *
     * @return integer
     */
    public function getVilleZmax()
    {
        return $this->villeZmax;
    }

    /**
     * Get villeId
     *
     * @return integer
     */
    public function getVilleId()
    {
        return $this->villeId;
    }
}
