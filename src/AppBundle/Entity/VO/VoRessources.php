<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoRessources
 *
 * @ORM\Table(name="vo_ressources")
 * @ORM\Entity
 */
class VoRessources
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="serial", type="integer", length=4, nullable=true)
     */
    private $serial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insert_date", type="datetime", length=25)
     */
    private $insert_date;

    /**
     * @var integer
     *
     * @ORM\Column(name="ram386", type="integer", length=4, nullable=true)
     */
    private $ram386;

    /**
     * @var integer
     *
     * @ORM\Column(name="ram64", type="integer", length=4,nullable=true)
     */
    private $ram64;

    /**
     * @var integer
     *
     * @ORM\Column(name="job_scratch_space", type="integer", length=4, nullable=true)
     */
    private $job_scratch_space;

    /**
     * @var integer
     *
     * @ORM\Column(name="job_max_cpu", type="integer", length=4, nullable=true)
     */
    private $job_max_cpu;

    /**
     * @var integer
     *
     * @ORM\Column(name="job_max_wall", type="integer", length=4, nullable=true)
     */
    private $job_max_wall;

    /**
     * @var string
     *
     * @ORM\Column(name="other_requirements", type="string", length=4000)
     */
    private $other_requirements;

    /**
     * @var integer
     *
     * @ORM\Column(name="cpu_core", type="integer", length=4, nullable=true)
     */
    private $cpu_core;

    /**
     * @var integer
     *
     * @ORM\Column(name="vm_ram", type="integer", length=4, nullable=true)
     */
    private $vm_ram;

    /**
     * @var integer
     *
     * @ORM\Column(name="storage_size", type="integer", length=4, nullable=true)
     */
    private $storage_size;

    /**
     * @var string
     *
     * @ORM\Column(name="public_ip", type="string", length=50, nullable=true)
     */
    private $public_ip;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="validated", type="string", length=1)
     */
    private $validated;

    /**
     * @var string
     *
     * @ORM\Column(name="reject_reason", type="string", length=4000)
     */
    private $reject_reason;

    /**
     * @var integer
     *
     * @ORM\Column(name="notify_sites", type="integer", length=1, nullable=true)
     */
    private $notify_sites;

    /**
     * @var string
     *
     * @ORM\Column(name="cvmfs", type="string", length=4000, nullable=true)
     */
    private $cvmfs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO\VoRessources")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="ressources_id")
     * })
     */
    private $Vo;

    /**
     * @var integer
     * @ORM\Column(name="number_cores", type="integer", length=6, nullable=true )
     */
    private $numberCores;

      /**
       * @var integer
       * @ORM\Column(name="minimum_ram", type="integer", length=6, nullable=true)
       */
    private $minimumRam;

    /**
     * @var integer
     * @ORM\Column(name="scratch_space_values", type="integer", length=6, nullable=true)
     */
    private $scratchSpaceValues;

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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set serial
     *
     * @param integer $serial
     *
     * @return VoRessources
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     *
     * @return integer
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set insertDate
     *
     * @param \DateTime $insertDate
     *
     * @return VoRessources
     */
    public function setInsertDate($insertDate)
    {
        $this->insert_date = $insertDate;

        return $this;
    }

    /**
     * Get insertDate
     *
     * @return \DateTime
     */
    public function getInsertDate()
    {
        return $this->insert_date;
    }

    /**
     * Set ram386
     *
     * @param integer $ram386
     *
     * @return VoRessources
     */
    public function setRam386($ram386)
    {
        $this->ram386 = $ram386;

        return $this;
    }

    /**
     * Get ram386
     *
     * @return integer
     */
    public function getRam386()
    {
        return $this->ram386;
    }

    /**
     * Set ram64
     *
     * @param integer $ram64
     *
     * @return VoRessources
     */
    public function setRam64($ram64)
    {
        $this->ram64 = $ram64;

        return $this;
    }

    /**
     * Get ram64
     *
     * @return integer
     */
    public function getRam64()
    {
        return $this->ram64;
    }

    /**
     * Set jobScratchSpace
     *
     * @param integer $jobScratchSpace
     *
     * @return VoRessources
     */
    public function setJobScratchSpace($jobScratchSpace)
    {
        $this->job_scratch_space = $jobScratchSpace;

        return $this;
    }

    /**
     * Get jobScratchSpace
     *
     * @return integer
     */
    public function getJobScratchSpace()
    {
        return $this->job_scratch_space;
    }

    /**
     * Set jobMaxCpu
     *
     * @param integer $jobMaxCpu
     *
     * @return VoRessources
     */
    public function setJobMaxCpu($jobMaxCpu)
    {
        $this->job_max_cpu = $jobMaxCpu;

        return $this;
    }

    /**
     * Get jobMaxCpu
     *
     * @return integer
     */
    public function getJobMaxCpu()
    {
        return $this->job_max_cpu;
    }

    /**
     * Set jobMaxWall
     *
     * @param integer $jobMaxWall
     *
     * @return VoRessources
     */
    public function setJobMaxWall($jobMaxWall)
    {
        $this->job_max_wall = $jobMaxWall;

        return $this;
    }

    /**
     * Get jobMaxWall
     *
     * @return integer
     */
    public function getJobMaxWall()
    {
        return $this->job_max_wall;
    }

    /**
     * Set otherRequirements
     *
     * @param string $otherRequirements
     *
     * @return VoRessources
     */
    public function setOtherRequirements($otherRequirements)
    {
        $this->other_requirements = $otherRequirements;

        return $this;
    }

    /**
     * Get otherRequirements
     *
     * @return string
     */
    public function getOtherRequirements()
    {
        return $this->other_requirements;
    }

    /**
     * Set cpuCore
     *
     * @param integer $cpuCore
     *
     * @return VoRessources
     */
    public function setCpuCore($cpuCore)
    {
        $this->cpu_core = $cpuCore;

        return $this;
    }

    /**
     * Get cpuCore
     *
     * @return integer
     */
    public function getCpuCore()
    {
        return $this->cpu_core;
    }

    /**
     * Set vmRam
     *
     * @param integer $vmRam
     *
     * @return VoRessources
     */
    public function setVmRam($vmRam)
    {
        $this->vm_ram = $vmRam;

        return $this;
    }

    /**
     * Get vmRam
     *
     * @return integer
     */
    public function getVmRam()
    {
        return $this->vm_ram;
    }

    /**
     * Set storageSize
     *
     * @param integer $storageSize
     *
     * @return VoRessources
     */
    public function setStorageSize($storageSize)
    {
        $this->storage_size = $storageSize;

        return $this;
    }

    /**
     * Get storageSize
     *
     * @return integer
     */
    public function getStorageSize()
    {
        return $this->storage_size;
    }

    /**
     * Set publicIp
     *
     * @param string $publicIp
     *
     * @return VoRessources
     */
    public function setPublicIp($publicIp)
    {
        $this->public_ip = $publicIp;

        return $this;
    }

    /**
     * Get publicIp
     *
     * @return string
     */
    public function getPublicIp()
    {
        return $this->public_ip;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return VoRessources
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set validated
     *
     * @param string $validated
     *
     * @return VoRessources
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get validated
     *
     * @return string
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set rejectReason
     *
     * @param string $rejectReason
     *
     * @return VoRessources
     */
    public function setRejectReason($rejectReason)
    {
        $this->reject_reason = $rejectReason;

        return $this;
    }

    /**
     * Get rejectReason
     *
     * @return string
     */
    public function getRejectReason()
    {
        return $this->reject_reason;
    }

    /**
     * Set notifySites
     *
     * @param integer $notifySites
     *
     * @return VoRessources
     */
    public function setNotifySites($notifySites)
    {
        $this->notify_sites = $notifySites;

        return $this;
    }

    /**
     * Get notifySites
     *
     * @return integer
     */
    public function getNotifySites()
    {
        return $this->notify_sites;
    }

    /**
     * Set cvmfs
     *
     * @param string $cvmfs
     *
     * @return VoRessources
     */
    public function setCvmfs($cvmfs)
    {
        $this->cvmfs = $cvmfs;

        return $this;
    }

    /**
     * Get cvmfs
     *
     * @return string
     */
    public function getCvmfs()
    {
        return $this->cvmfs;
    }


    /**
     * Set numberCores
     *
     * @param integer $numberCores
     *
     * @return VoRessources
     */
    public function setNumberCores($numberCores)
    {
        $this->numberCores = $numberCores;

        return $this;
    }

    /**
     * Get numberCores
     *
     * @return integer
     */
    public function getNumberCores()
    {
        return $this->numberCores;
    }


    /**
     * Set minimumRam
     *
     * @param integer $minimumRam
     *
     * @return VoRessources
     */
    public function setMinimumRam($minimumRam)
    {
        $this->minimumRam = $minimumRam;

        return $this;
    }

    /**
     * Get minimumRam
     *
     * @return integer
     */
    public function getMinimumRam()
    {
        return $this->minimumRam;
    }

    /**
     * Set scratchSpaceValues
     *
     * @param integer $scratchSpaceValues
     *
     * @return VoRessources
     */
    public function setScratchSpaceValues($scratchSpaceValues)
    {
        $this->scratchSpaceValues = $scratchSpaceValues;

        return $this;
    }

    /**
     * Get scratchSpaceValues
     *
     * @return integer
     */
    public function getScratchSpaceValues()
    {
        return $this->scratchSpaceValues;
    }

    /**
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     *
     * @return VoRessources
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
     */
    public function removeVo(\AppBundle\Entity\VO\Vo $vo)
    {
        $this->Vo->removeElement($vo);
    }

    /**
     * Get vo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVo()
    {
        return $this->Vo;
    }
}
