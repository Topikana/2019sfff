<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoMailingList
 *
 * @ORM\Table(name="vo_mailing_list")
 * @ORM\Entity
 */
class VoMailingList
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
     * @ORM\Column(name="admins_mailing_list", type="string", length=255)
     */
    private $admins_mailing_list;

    /**
     * @var string
     *
     * @ORM\Column(name="operations_mailing_list", type="string", length=255)
     */
    private $operations_mailing_list;

    /**
     * @var string
     *
     * @ORM\Column(name="user_support_mailing_list", type="string", length=255)
     */
    private $user_support_mailing_list;

    /**
     * @var string
     *
     * @ORM\Column(name="users_mailing_list", type="string", length=255)
     */
    private $users_mailing_list;

    /**
     * @var string
     *
     * @ORM\Column(name="security_contact_mailing_list", type="string", length=255)
     */
    private $security_contact_mailing_list;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insert_date", type="datetime", length=25)
     */
    private $insert_date;

    /**
     * @var int
     *
     * @ORM\Column(name="serial", type="integer", length=4)
     */
    private $serial;

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
     * @ORM\ManyToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO_test\VoMailingList")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="mailing_list_id")
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
     * Set adminsMailingList
     *
     * @param string $adminsMailingList
     *
     * @return VoMailingList
     */
    public function setAdminsMailingList($adminsMailingList)
    {
        $this->admins_mailing_list = $adminsMailingList;

        return $this;
    }

    /**
     * Get adminsMailingList
     *
     * @return string
     */
    public function getAdminsMailingList()
    {
        return $this->admins_mailing_list;
    }

    /**
     * Set operationsMailingList
     *
     * @param string $operationsMailingList
     *
     * @return VoMailingList
     */
    public function setOperationsMailingList($operationsMailingList)
    {
        $this->operations_mailing_list = $operationsMailingList;

        return $this;
    }

    /**
     * Get operationsMailingList
     *
     * @return string
     */
    public function getOperationsMailingList()
    {
        return $this->operations_mailing_list;
    }

    /**
     * Set userSupportMailingList
     *
     * @param string $userSupportMailingList
     *
     * @return VoMailingList
     */
    public function setUserSupportMailingList($userSupportMailingList)
    {
        $this->user_support_mailing_list = $userSupportMailingList;

        return $this;
    }

    /**
     * Get userSupportMailingList
     *
     * @return string
     */
    public function getUserSupportMailingList()
    {
        return $this->user_support_mailing_list;
    }

    /**
     * Set usersMailingList
     *
     * @param string $usersMailingList
     *
     * @return VoMailingList
     */
    public function setUsersMailingList($usersMailingList)
    {
        $this->users_mailing_list = $usersMailingList;

        return $this;
    }

    /**
     * Get usersMailingList
     *
     * @return string
     */
    public function getUsersMailingList()
    {
        return $this->users_mailing_list;
    }

    /**
     * Set securityContactMailingList
     *
     * @param string $securityContactMailingList
     *
     * @return VoMailingList
     */
    public function setSecurityContactMailingList($securityContactMailingList)
    {
        $this->security_contact_mailing_list = $securityContactMailingList;

        return $this;
    }

    /**
     * Get securityContactMailingList
     *
     * @return string
     */
    public function getSecurityContactMailingList()
    {
        return $this->security_contact_mailing_list;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return VoMailingList
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
     * Set insertDate
     *
     * @param \DateTime $insertDate
     *
     * @return VoMailingList
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
     * Set serial
     *
     * @param int $serial
     *
     * @return VoMailingList
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
     * Set validated
     *
     * @param string $validated
     *
     * @return VoMailingList
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
     * @return VoMailingList
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
     * @return VoMailingList
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
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return VoMailingList
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
