<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoScope
 *
 * @ORM\Table(name="vo_scope")
 * @ORM\Entity
 */
class VoScope
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
     * @ORM\Column(name="scope", type="string")
     */
    private $scope;

    /**
     * @var string
     *
     * @ORM\Column(name="roc", type="string")
     */
    private $roc;

    /**
     * @var int
     *
     * @ORM\Column(name="decommissioned", type="integer", length=1)
     */
    private $decommissioned;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VoHeader", mappedBy="AppBundle\Entity\VO_test\VoScope")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="scope_id")
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
     * Set scope
     *
     * @param string $scope
     *
     * @return VoScope
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set roc
     *
     * @param string $roc
     *
     * @return VoScope
     */
    public function setRoc($roc)
    {
        $this->roc = $roc;

        return $this;
    }

    /**
     * Get roc
     *
     * @return string
     */
    public function getRoc()
    {
        return $this->roc;
    }

    /**
     * Set decommissioned
     *
     * @param int $decommissioned
     *
     * @return VoScope
     */
    public function setDecommissioned($decommissioned)
    {
        $this->decommissioned = $decommissioned;

        return $this;
    }

    /**
     * Get decommissioned
     *
     * @return int
     */
    public function getDecommissioned()
    {
        return $this->decommissioned;
    }

    /**
     * Add voHeader
     *
     * @param \AppBundle\Entity\VO\VoHeader $voHeader
     * @codeCoverageIgnore
     * @return VoScope
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
