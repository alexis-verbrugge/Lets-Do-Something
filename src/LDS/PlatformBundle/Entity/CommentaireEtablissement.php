<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CommentaireEtablissement
 *
 * @ORM\Table(name="commentaire_etablissement")
 * @ORM\Entity(repositoryClass="LDS\PlatformBundle\Repository\CommentaireEtablissementRepository")
 */
class CommentaireEtablissement
{

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", nullable=false)
     * @Assert\Length(min=25, minMessage="Mettez un commentaire un peu plus constructif !")
     */
    private $commentaire;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")

     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="note", type="integer", nullable=false)
     */
    private $note;

    /**
   * @ORM\ManyToOne(targetEntity="LDS\PlatformBundle\Entity\Etablissement")
   * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
   */
  private $etablissement;

  /**
   * @ORM\ManyToOne(targetEntity="LDS\PlatformBundle\Entity\Particulier")
   * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
   */
  private $particulier;

   /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    /**
     * Set siret
     *
     * @param integer $siret
     *
     * @return CommentaireEtablissement
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return integer
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return CommentaireEtablissement
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

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return CommentaireEtablissement
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set note
     *
     * @param integer $note
     *
     * @return CommentaireEtablissement
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return integer
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set etablissement
     *
     * @param \LDS\PlatformBundle\Entity\Etablissement $etablissement
     *
     * @return CommentaireEtablissement
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
     * Set particulier
     *
     * @param \LDS\PlatformBundle\Entity\Particulier $particulier
     *
     * @return CommentaireEtablissement
     */
    public function setParticulier(\LDS\PlatformBundle\Entity\Particulier $particulier)
    {
        $this->particulier = $particulier;

        return $this;
    }

    /**
     * Get particulier
     *
     * @return \LDS\PlatformBundle\Entity\Particulier
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
     * @return CommentaireEtablissement
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
