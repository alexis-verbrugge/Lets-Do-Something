<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FavoriEvenement
 *
 * @ORM\Table(name="favori_evenement")
 * @ORM\Entity(repositoryClass="LDS\PlatformBundle\Repository\FavoriEvenementRepository")
 */
class FavoriEvenement
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
     * @var string
     *
     * @ORM\Column(name="LoginParticulier", type="string", length=255)
     */
    private $loginParticulier;

    /**
     * @var int
     *
     * @ORM\Column(name="IdEvenement", type="integer")
     */
    private $idEvenement;


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
     * Set loginParticulier
     *
     * @param string $loginParticulier
     *
     * @return FavoriEvenement
     */
    public function setLoginParticulier($loginParticulier)
    {
        $this->loginParticulier = $loginParticulier;

        return $this;
    }

    /**
     * Get loginParticulier
     *
     * @return string
     */
    public function getLoginParticulier()
    {
        return $this->loginParticulier;
    }

    /**
     * Set idEvenement
     *
     * @param integer $idEvenement
     *
     * @return FavoriEvenement
     */
    public function setIdEvenement($idEvenement)
    {
        $this->idEvenement = $idEvenement;

        return $this;
    }

    /**
     * Get idEvenement
     *
     * @return int
     */
    public function getIdEvenement()
    {
        return $this->idEvenement;
    }
}

