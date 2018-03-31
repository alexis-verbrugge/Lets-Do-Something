<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParticipantsSoiree
 *
 * @ORM\Table(name="participants_soiree")
 * @ORM\Entity(repositoryClass="LDS\PlatformBundle\Repository\ParticipantsSoireeRepository")
 */
class ParticipantsSoiree
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
     * @ORM\Column(name="login", type="string", length=255)
     */
    private $login;

    /**
     * @var int
     *
     * @ORM\Column(name="id_soiree", type="integer")
     */
    private $idSoiree;

    /**
     * @var bool
     *
     * @ORM\Column(name="participe", type="boolean")
     */
    private $participe;


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
     * Set idSoiree
     *
     * @param integer $idSoiree
     *
     * @return ParticipantsSoiree
     */
    public function setIdSoiree($idSoiree)
    {
        $this->idSoiree = $idSoiree;

        return $this;
    }

    /**
     * Get idSoiree
     *
     * @return int
     */
    public function getIdSoiree()
    {
        return $this->idSoiree;
    }

    /**
     * Set participe
     *
     * @param boolean $participe
     *
     * @return ParticipantsSoiree
     */
    public function setParticipe($participe)
    {
        $this->participe = $participe;

        return $this;
    }

    /**
     * Get participe
     *
     * @return bool
     */
    public function getParticipe()
    {
        return $this->participe;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return ParticipantsSoiree
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }
}
