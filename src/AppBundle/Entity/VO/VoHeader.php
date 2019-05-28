<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * VoHeader
 *
 * @ORM\Table(name="vo_header")
 * @ORM\Entity
 */
class VoHeader
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Regex(
     *      pattern="^([a-z0-9\-]{1,255}\.)+[a-z]{2,4}$^",
     *      message="Invalid VO name, see the help")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="grid_id", type="string", length=255)
     */
    private $grid_id;

    /**
     * @var int
     *
     * @ORM\Column(name="serial", type="integer", length=4)
     */
    private $serial;

    /**
     * @var string
     *
     * @ORM\Column(name="enrollment_url", type="string", length=255)
     */
    private $enrollment_url;

    /**
     * @var string
     *
     * @ORM\Column(name="homepage_url", type="string", length=255)
     */
    private $homepage_url;

    /**
     * @var string
     *
     * @ORM\Column(name="support_procedure_url", type="string", length=255)
     */
    private $support_procedure_url;

    /**
     * @var string
     *
     * @ORM\Column(name="aup", type="string", length=4000)
     */
    private $aup;

    /**
     * @var string
     *
     * @ORM\Column(name="aup_type", type="string", length=4)
     */
    private $aup_type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=4000)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="arc_supported", type="integer", length=1)
     */
    private $arc_supported;

    /**
     * @var int
     *
     * @ORM\Column(name="glite_supported", type="integer", length=1)
     */
    private $glite_supported;

    /**
     * @var int
     *
     * @ORM\Column(name="unicore_supported", type="integer", length=1)
     */
    private $unicore_supported;

    /**
     * @var int
     *
     * @ORM\Column(name="globus_supported", type="integer", length=1)
     */
    private $globus_supported;

    /**
     * @var int
     *
     * @ORM\Column(name="cloud_computing_supported", type="integer", length=1)
     */
    private $cloud_computing_supported;

    /**
     * @var int
     *
     * @ORM\Column(name="cloud_storage_supported", type="integer", length=1)
     */
    private $cloud_storage_supported;

    /**
     * @var int
     *
     * @ORM\Column(name="desktop_grid_supported", type="integer", length=1)
     */
    private $desktop_grid_supported;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validation_date", type="date", length=25)
     */
    private $validation_date;

    /**
     * @var int
     *
     * @ORM\Column(name="discipline_id", type="integer", length=4)
     */
    private $discipline_id;

    /**
     * @var int
     *
     * @ORM\Column(name="scope_id", type="integer", length=4)
     */
    private $scope_id;

    /**
     * @var int
     *
     * @ORM\Column(name="status_id", type="integer", length=4)
     */
    private $status_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insert_date", type="datetime", length=25)
     */
    private $insert_date;

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
     * @var int
     *
     * @ORM\Column(name="notify_sites", type="integer", length=1)
     */
    private $notify_sites;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoDiscipline", mappedBy="AppBundle\Entity\VO\VoHeader")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
     * })
     */
    private $VoDiscipline;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoScope", mappedBy="AppBundle\Entity\VO\VoHeader")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="scope_id", referencedColumnName="id")
     * })
     */
    private $VoScope;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoStatus", mappedBy="AppBundle\Entity\VO\VoHeader")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    private $VoStatus;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO\VoHeader")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="header_id")
     * })
     */
    private $Vo;


    /**
     * @var int
     *
     * @ORM\Column(name="perun", type="integer", length=1)
     */
    private $perun;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->VoDiscipline = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoScope = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoStatus = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return VoHeader
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return VoHeader
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set gridId
     *
     * @param string $gridId
     *
     * @return VoHeader
     */
    public function setGridId($gridId)
    {
        $this->grid_id = $gridId;

        return $this;
    }

    /**
     * Get gridId
     *
     * @return string
     */
    public function getGridId()
    {
        return $this->grid_id;
    }

    /**
     * Set serial
     *
     * @param int $serial
     *
     * @return VoHeader
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
     * Set enrollmentUrl
     *
     * @param string $enrollmentUrl
     *
     * @return VoHeader
     */
    public function setEnrollmentUrl($enrollmentUrl)
    {
        $this->enrollment_url = $enrollmentUrl;

        return $this;
    }

    /**
     * Get enrollmentUrl
     *
     * @return string
     */
    public function getEnrollmentUrl()
    {
        return $this->enrollment_url;
    }

    /**
     * Set homepageUrl
     *
     * @param string $homepageUrl
     *
     * @return VoHeader
     */
    public function setHomepageUrl($homepageUrl)
    {
        $this->homepage_url = $homepageUrl;

        return $this;
    }

    /**
     * Get homepageUrl
     *
     * @return string
     */
    public function getHomepageUrl()
    {
        return $this->homepage_url;
    }

    /**
     * Set supportProcedureUrl
     *
     * @param string $supportProcedureUrl
     *
     * @return VoHeader
     */
    public function setSupportProcedureUrl($supportProcedureUrl)
    {
        $this->support_procedure_url = $supportProcedureUrl;

        return $this;
    }

    /**
     * Get supportProcedureUrl
     *
     * @return string
     */
    public function getSupportProcedureUrl()
    {
        return $this->support_procedure_url;
    }

    /**
     * Set aup
     *
     * @param string $aup
     *
     * @return VoHeader
     */
    public function setAup($aup)
    {
        $this->aup = $aup;

        return $this;
    }

    /**
     * Get aup
     *
     * @return string
     */
    public function getAup()
    {
        return $this->aup;
    }

    /**
     * Set aupType
     *
     * @param string $aupType
     *
     * @return VoHeader
     */
    public function setAupType($aupType)
    {
        $this->aup_type = $aupType;

        return $this;
    }

    /**
     * Get aupType
     *
     * @return string
     */
    public function getAupType()
    {
        return $this->aup_type;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return VoHeader
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
     * Set arcSupported
     *
     * @param int $arcSupported
     *
     * @return VoHeader
     */
    public function setArcSupported($arcSupported)
    {
        $this->arc_supported = $arcSupported;

        return $this;
    }

    /**
     * Get arcSupported
     *
     * @return int
     */
    public function getArcSupported()
    {
        return $this->arc_supported;
    }

    /**
     * Set gliteSupported
     *
     * @param int $gliteSupported
     *
     * @return VoHeader
     */
    public function setGliteSupported($gliteSupported)
    {
        $this->glite_supported = $gliteSupported;

        return $this;
    }

    /**
     * Get gliteSupported
     *
     * @return int
     */
    public function getGliteSupported()
    {
        return $this->glite_supported;
    }

    /**
     * Set unicoreSupported
     *
     * @param int $unicoreSupported
     *
     * @return VoHeader
     */
    public function setUnicoreSupported($unicoreSupported)
    {
        $this->unicore_supported = $unicoreSupported;

        return $this;
    }

    /**
     * Get unicoreSupported
     *
     * @return int
     */
    public function getUnicoreSupported()
    {
        return $this->unicore_supported;
    }

    /**
     * Set globusSupported
     *
     * @param int $globusSupported
     *
     * @return VoHeader
     */
    public function setGlobusSupported($globusSupported)
    {
        $this->globus_supported = $globusSupported;

        return $this;
    }

    /**
     * Get globusSupported
     *
     * @return int
     */
    public function getGlobusSupported()
    {
        return $this->globus_supported;
    }

    /**
     * Set cloudComputingSupported
     *
     * @param int $cloudComputingSupported
     *
     * @return VoHeader
     */
    public function setCloudComputingSupported($cloudComputingSupported)
    {
        $this->cloud_computing_supported = $cloudComputingSupported;

        return $this;
    }

    /**
     * Get cloudComputingSupported
     *
     * @return int
     */
    public function getCloudComputingSupported()
    {
        return $this->cloud_computing_supported;
    }

    /**
     * Set cloudStorageSupported
     *
     * @param int $cloudStorageSupported
     *
     * @return VoHeader
     */
    public function setCloudStorageSupported($cloudStorageSupported)
    {
        $this->cloud_storage_supported = $cloudStorageSupported;

        return $this;
    }

    /**
     * Get cloudStorageSupported
     *
     * @return int
     */
    public function getCloudStorageSupported()
    {
        return $this->cloud_storage_supported;
    }

    /**
     * Set desktopGridSupported
     *
     * @param int $desktopGridSupported
     *
     * @return VoHeader
     */
    public function setDesktopGridSupported($desktopGridSupported)
    {
        $this->desktop_grid_supported = $desktopGridSupported;

        return $this;
    }

    /**
     * Get desktopGridSupported
     *
     * @return int
     */
    public function getDesktopGridSupported()
    {
        return $this->desktop_grid_supported;
    }

    /**
     * Set validationDate
     *
     * @param \DateTime $validationDate
     *
     * @return VoHeader
     */
    public function setValidationDate($validationDate)
    {
        $this->validation_date = $validationDate;

        return $this;
    }

    /**
     * Get validationDate
     *
     * @return \DateTime
     */
    public function getValidationDate()
    {
        return $this->validation_date;
    }

    /**
     * Set disciplineId
     *
     * @param int $disciplineId
     *
     * @return VoHeader
     */
    public function setDisciplineId($disciplineId)
    {
        $this->discipline_id = $disciplineId;

        return $this;
    }

    /**
     * Get disciplineId
     *
     * @return int
     */
    public function getDisciplineId()
    {
        return $this->discipline_id;
    }

    /**
     * Set scopeId
     *
     * @param int $scopeId
     *
     * @return VoHeader
     */
    public function setScopeId($scopeId)
    {
        $this->scope_id = $scopeId;

        return $this;
    }

    /**
     * Get scopeId
     *
     * @return int
     */
    public function getScopeId()
    {
        return $this->scope_id;
    }

    /**
     * Set statusId
     *
     * @param int $statusId
     *
     * @return VoHeader
     */
    public function setStatusId($statusId)
    {
        $this->status_id = $statusId;

        return $this;
    }

    /**
     * Get statusId
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->status_id;
    }

    /**
     * Set insertDate
     *
     * @param \DateTime $insertDate
     *
     * @return VoHeader
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
     * Set user
     *
     * @param string $user
     *
     * @return VoHeader
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
     * @return VoHeader
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
     * @return VoHeader
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
     * @param int $notifySites
     *
     * @return VoHeader
     */
    public function setNotifySites($notifySites)
    {
        $this->notify_sites = $notifySites;

        return $this;
    }

    /**
     * Get notifySites
     *
     * @return int
     */
    public function getNotifySites()
    {
        return $this->notify_sites;
    }

    /**
     * Add voDiscipline
     *
     * @param \AppBundle\Entity\VO\VoDiscipline $voDiscipline
     * @codeCoverageIgnore
     * @return VoHeader
     */
    public function addVoDiscipline(\AppBundle\Entity\VO\VoDiscipline $voDiscipline)
    {
        $this->VoDiscipline[] = $voDiscipline;

        return $this;
    }

    /**
     * Remove voDiscipline
     *
     * @param \AppBundle\Entity\VO\VoDiscipline $voDiscipline
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoDiscipline(\AppBundle\Entity\VO\VoDiscipline $voDiscipline)
    {
        return $this->VoDiscipline->removeElement($voDiscipline);
    }

    /**
     * Get voDiscipline
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoDiscipline()
    {
        return $this->VoDiscipline;
    }

    /**
     * Add voScope
     *
     * @param \AppBundle\Entity\VO\VoScope $voScope
     * @codeCoverageIgnore
     * @return VoHeader
     */
    public function addVoScope(\AppBundle\Entity\VO\VoScope $voScope)
    {
        $this->VoScope[] = $voScope;

        return $this;
    }

    /**
     * Remove voScope
     *
     * @param \AppBundle\Entity\VO\VoScope $voScope
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoScope(\AppBundle\Entity\VO\VoScope $voScope)
    {
        return $this->VoScope->removeElement($voScope);
    }

    /**
     * Get voScope
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoScope()
    {
        return $this->VoScope;
    }

    /**
     * Add voStatus
     *
     * @param \AppBundle\Entity\VO\VoStatus $voStatus
     * @codeCoverageIgnore
     * @return VoHeader
     */
    public function addVoStatus(\AppBundle\Entity\VO\VoStatus $voStatus)
    {
        $this->VoStatus[] = $voStatus;

        return $this;
    }

    /**
     * Remove voStatus
     *
     * @param \AppBundle\Entity\VO\VoStatus $voStatus
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoStatus(\AppBundle\Entity\VO\VoStatus $voStatus)
    {
        return $this->VoStatus->removeElement($voStatus);
    }

    /**
     * Get voStatus
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoStatus()
    {
        return $this->VoStatus;
    }

    /**
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return VoHeader
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


    /**
     * Set perun.
     *
     * @param int $perun
     *
     * @return VoHeader
     */
    public function setPerun($perun)
    {
        $this->perun = $perun;

        return $this;
    }

    /**
     * Get perun.
     *
     * @return int
     */
    public function getPerun()
    {
        return $this->perun;
    }
}
