<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoRegexp
 *
 * @ORM\Table(name="vo_regexp")
 * @ORM\Entity
 */
class VoRegexp
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="regexpression", type="string", length=255)
     */
    private $regexpression;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100)
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VoField", mappedBy="AppBundle\Entity\VO_test\VoRegexp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="field_regexp_id")
     * })
     */
    private $VoField;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->VoField = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set regexpression
     *
     * @param string $regexpression
     *
     * @return VoRegexp
     */
    public function setRegexpression($regexpression)
    {
        $this->regexpression = $regexpression;

        return $this;
    }

    /**
     * Get regexpression
     *
     * @return string
     */
    public function getRegexpression()
    {
        return $this->regexpression;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return VoRegexp
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
     * Add voField
     *
     * @param \AppBundle\Entity\VO\VoField $voField
     * @codeCoverageIgnore
     * @return VoRegexp
     */
    public function addVoField(\AppBundle\Entity\VO\VoField $voField)
    {
        $this->VoField[] = $voField;

        return $this;
    }

    /**
     * Remove voField
     *
     * @param \AppBundle\Entity\VO\VoField $voField
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoField(\AppBundle\Entity\VO\VoField $voField)
    {
        return $this->VoField->removeElement($voField);
    }

    /**
     * Get voField
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoField()
    {
        return $this->VoField;
    }
}
