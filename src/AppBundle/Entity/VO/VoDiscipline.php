<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoDiscipline
 *
 * @ORM\Table(name="vo_discipline")
 * @ORM\Entity
 */
class VoDiscipline
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
     * @ORM\Column(name="discipline", type="string")
     */
    private $discipline;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000)
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoHeader", mappedBy="AppBundle\Entity\VO_test\VoDiscipline")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="discipline_id")
     * })
     */
    private $VoHeader;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->VoHeader = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set discipline
     *
     * @param string $discipline
     *
     * @return VoDiscipline
     */
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;

        return $this;
    }

    /**
     * Get discipline
     *
     * @return string
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return VoDiscipline
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
     * Add voHeader
     *
     * @param \AppBundle\Entity\VO\VoHeader $voHeader
     * @codeCoverageIgnore
     * @return VoDiscipline
     */
    public function addVoHeader(\AppBundle\Entity\VO\VoHeader $voHeader)
    {
        $this->VoHeader[] = $voHeader;

        return $this;
    }

    /**
     * Remove voHeader
     *
     * @param \AppBundle\Entity\VO\VoHeader $voHeader
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoHeader(\AppBundle\Entity\VO\VoHeader $voHeader)
    {
        return $this->VoHeader->removeElement($voHeader);
    }

    /**
     * Get voHeader
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoHeader()
    {
        return $this->VoHeader;
    }
}
