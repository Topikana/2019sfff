<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoStatus
 *
 * @ORM\Table(name="vo_status")
 * @ORM\Entity
 */
class VoStatus
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
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VoHeader", mappedBy="AppBundle\Entity\VO_test\VoStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="status_id")
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
     * Set status
     *
     * @param string $status
     *
     * @return VoStatus
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return VoStatus
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
     * @return VoStatus
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
