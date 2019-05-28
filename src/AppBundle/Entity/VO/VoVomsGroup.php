<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoVomsGroup
 *
 * @ORM\Table(name="vo_voms_group")
 * @ORM\Entity
 */
class VoVomsGroup
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
     * @ORM\Column(name="group_role", type="string", length=255)
     */
    private $group_role;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="is_group_used", type="integer", length=1)
     */
    private $is_group_used;

    /**
     * @var string
     *
     * @ORM\Column(name="group_type", type="string", length=255)
     */
    private $group_type;

    /**
     * @var int
     *
     * @ORM\Column(name="allocated_ressources", type="integer", length=4)
     */
    private $allocated_ressources;

    /**
     * @var int
     *
     * @ORM\Column(name="serial", type="integer", length=4)
     */
    private $serial;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO_test\VoVomsGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial", referencedColumnName="serial")
     * })
     */
    private $Vo;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Vo = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set groupRole
     *
     * @param string $groupRole
     *
     * @return VoVomsGroup
     */
    public function setGroupRole($groupRole)
    {
        $this->group_role = $groupRole;

        return $this;
    }

    /**
     * Get groupRole
     *
     * @return string
     */
    public function getGroupRole()
    {
        return $this->group_role;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return VoVomsGroup
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
     * Set isGroupUsed
     *
     * @param int $isGroupUsed
     *
     * @return VoVomsGroup
     */
    public function setIsGroupUsed($isGroupUsed)
    {
        $this->is_group_used = $isGroupUsed;

        return $this;
    }

    /**
     * Get isGroupUsed
     *
     * @return int
     */
    public function getIsGroupUsed()
    {
        return $this->is_group_used;
    }

    /**
     * Set groupType
     *
     * @param string $groupType
     *
     * @return VoVomsGroup
     */
    public function setGroupType($groupType)
    {
        $this->group_type = $groupType;

        return $this;
    }

    /**
     * Get groupType
     *
     * @return string
     */
    public function getGroupType()
    {
        return $this->group_type;
    }

    /**
     * Set allocatedRessources
     *
     * @param int $allocatedRessources
     *
     * @return VoVomsGroup
     */
    public function setAllocatedRessources($allocatedRessources)
    {
        $this->allocated_ressources = $allocatedRessources;

        return $this;
    }

    /**
     * Get allocatedRessources
     *
     * @return int
     */
    public function getAllocatedRessources()
    {
        return $this->allocated_ressources;
    }

    /**
     * Set serial
     *
     * @param int $serial
     *
     * @return VoVomsGroup
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     *
     * @return int
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return VoVomsGroup
     */
    public function addVo(\AppBundle\Entity\VO\Vo $vo)
    {
        $this->Vo[] = $vo;

        return $this;
    }

    /**
     * Remove vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVo(\AppBundle\Entity\VO\Vo $vo)
    {
        return $this->Vo->removeElement($vo);
    }

    /**
     * Get vo
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVo()
    {
        return $this->Vo;
    }
}
