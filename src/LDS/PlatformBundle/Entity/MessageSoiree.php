<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessageSoiree
 *
 * @ORM\Table(name="message_soiree")
 * @ORM\Entity(repositoryClass="LDS\PlatformBundle\Repository\MessageSoireeRepository")
 */
class MessageSoiree
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
   * @ORM\ManyToOne(targetEntity="LDS\PlatformBundle\Entity\FicheSoiree")
   * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
   */
    private $ficheSoiree;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=255)
     */
    private $commentaire;

    /**
   * @ORM\ManyToOne(targetEntity="LDS\PlatformBundle\Entity\Particulier")
   * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
   */
    private $particulier;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;


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
     * Set ficheSoiree
     *
     * @param string $ficheSoiree
     *
     * @return MessageSoiree
     */
    public function setFicheSoiree($ficheSoiree)
    {
        $this->ficheSoiree = $ficheSoiree;

        return $this;
    }

    /**
     * Get ficheSoiree
     *
     * @return string
     */
    public function getFicheSoiree()
    {
        return $this->ficheSoiree;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return MessageSoiree
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set particulier
     *
     * @param string $particulier
     *
     * @return MessageSoiree
     */
    public function setParticulier($particulier)
    {
        $this->particulier = $particulier;

        return $this;
    }

    /**
     * Get particulier
     *
     * @return string
     */
    public function getParticulier()
    {
        return $this->particulier;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return MessageSoiree
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

