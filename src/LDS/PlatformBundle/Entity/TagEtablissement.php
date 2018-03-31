<?php

namespace LDS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TagEtablissement
 *
 * @ORM\Table(name="tag_etablissement")
 * @ORM\Entity
 */
class TagEtablissement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_tag", type="integer", nullable=false)
     */
    private $tag;

    /**
     * @var integer
     *
     * @ORM\Column(name="SIRET", type="integer", nullable=false)
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


}

