<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Favori
 *
 * @ORM\Table(name="FavoriEtablissement")
 * @ORM\Entity(repositoryClass="LDS\PlatformBundle\Repository\FavoriEtablissementRepository")
 */
class FavoriEtablissement
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
     * @ORM\Column(name="login_particulier", type="string")
     */
    private $loginParticulier;

    /**
     * @var int
     *
     * @ORM\Column(name="siret", type="integer")
     */
    private $siret;


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
     * @param integer $loginParticulier
     *
     * @return Favori
     */
    public function setLoginParticulier($loginParticulier)
    {
        $this->loginParticulier = $loginParticulier;

        return $this;
    }

    /**
     * Get loginParticulier
     *
     * @return int
     */
    public function getLoginParticulier()
    {
        return $this->loginParticulier;
    }

    /**
     * Set siret
     *
     * @param integer $siret
     *
     * @return Favori
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
}

