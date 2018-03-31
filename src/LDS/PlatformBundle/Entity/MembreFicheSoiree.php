<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MembreFicheSoiree
 *
 * @ORM\Table(name="membre_fiche_soiree")
 * @ORM\Entity
 */
class MembreFicheSoiree
{
  /**
   * @ORM\ManyToOne(targetEntity="LDS\PlatformBundle\Entity\Particulier")
   * @ORM\JoinColumn(nullable=false)
   */
    private $login;

  /**
   * @ORM\ManyToOne(targetEntity="LDS\PlatformBundle\Entity\FicheSoiree")
   * @ORM\JoinColumn(nullable=false)
   */
    private $ficheSoiree;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

