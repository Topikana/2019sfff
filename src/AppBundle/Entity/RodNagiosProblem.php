<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 11/09/18
 * Time: 14:12
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * RodNagiosProblem
 *
 * @ORM\Table(name="rod_nagios_problem")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RodNagiosProblemRepository")
 */
class RodNagiosProblem
{

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="vo", type="string", length=255)
     */
    private $vo;


    /**
     * @var int
     *
     * @ORM\Column(name="last_history_id", type="bigint", length=20)
     */
    private $last_history_id;


    /**
     * @var int
     *
     * @ORM\Column(name="ops_flag", type="integer", length=9)
     */
    private $ops_flags;

    /**
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *
     */
    private $created_at;

    /**
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updated_at;


    /**
     * @var string
     *
     * @ORM\Column(name="test_name", type="string", length=255)
     */
    private $test_name;

    /**
     * @var string
     *
     * @ORM\Column(name="host_name", type="string", length=255)
     */
    private $host_name;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=255)
     */
    private $service;

    /**
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private $start_date;

    /**
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=false)
     */
    private $end_date;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", length=6)
     */
    private $status;

//    mediumint


    /**
     * @var int
     *
     * @ORM\Column(name="flags", type="integer", length=9)
     */
    private $flags;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text")
     */
    private $details;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text")
     */
    private $summary;


    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="ngi", type="string", length=255)
     */
    private $ngi;

    /**
     * @var string
     *
     * @ORM\Column(name="url_to_history", type="string", length=255)
     */
    private $url_to_history;

//@ORM\JoinColumn(name="notepad_id", referencedColumnName="id")

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ROD\Ticket", inversedBy="alarms")
     */
    private $ticket;


    /**
     * @var string
     *
     *
     */
    private $notepad_id;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ROD\Ticket", inversedBy="alarms")
//     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
//     *
//     */
//    private $ticket_id;


    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return RodNagiosProblem
     */
    public function setId($id)
    {

        $this->id = $id;

        return $this;
    }

    /**
     * Set vo
     *
     * @param string $vo
     *
     * @return RodNagiosProblem
     */
    public function setVo($vo)
    {
        $this->vo = $vo;

        return $this;
    }

    /**
     * Get vo
     *
     * @return string
     */
    public function getVo()
    {
        return $this->vo;
    }

    /**
     * Set lastHistoryId
     *
     * @param integer $lastHistoryId
     *
     * @return RodNagiosProblem
     */
    public function setLastHistoryId($lastHistoryId)
    {
        $this->last_history_id = $lastHistoryId;

        return $this;
    }

    /**
     * Get lastHistoryId
     *
     * @return integer
     */
    public function getLastHistoryId()
    {
        return $this->last_history_id;
    }

    /**
     * Set opsFlags
     *
     * @param integer $opsFlags
     *
     * @return RodNagiosProblem
     */
    public function setOpsFlags($opsFlags)
    {
        $this->ops_flags = $opsFlags;

        return $this;
    }

    /**
     * Get opsFlags
     *
     * @return integer
     */
    public function getOpsFlags()
    {
        return $this->ops_flags;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return RodNagiosProblem
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return RodNagiosProblem
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set testName
     *
     * @param string $testName
     *
     * @return RodNagiosProblem
     */
    public function setTestName($testName)
    {
        $this->test_name = $testName;

        return $this;
    }

    /**
     * Get testName
     *
     * @return string
     */
    public function getTestName()
    {
        return $this->test_name;
    }

    /**
     * Set hostName
     *
     * @param string $hostName
     *
     * @return RodNagiosProblem
     */
    public function setHostName($hostName)
    {
        $this->host_name = $hostName;

        return $this;
    }

    /**
     * Get hostName
     *
     * @return string
     */
    public function getHostName()
    {
        return $this->host_name;
    }

    /**
     * Set service
     *
     * @param string $service
     *
     * @return RodNagiosProblem
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return RodNagiosProblem
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return RodNagiosProblem
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return RodNagiosProblem
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set flags
     *
     * @param integer $flags
     *
     * @return RodNagiosProblem
     */
    public function setFlags($flags)
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     * Get flags
     *
     * @return integer
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return RodNagiosProblem
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return RodNagiosProblem
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set site
     *
     * @param string $site
     *
     * @return RodNagiosProblem
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set ngi
     *
     * @param string $ngi
     *
     * @return RodNagiosProblem
     */
    public function setNgi($ngi)
    {
        $this->ngi = $ngi;

        return $this;
    }

    /**
     * Get ngi
     *
     * @return string
     */
    public function getNgi()
    {
        return $this->ngi;
    }

    /**
     * Set urlToHistory
     *
     * @param string $urlToHistory
     *
     * @return RodNagiosProblem
     */
    public function setUrlToHistory($urlToHistory)
    {
        $this->url_to_history = $urlToHistory;

        return $this;
    }

    /**
     * Get urlToHistory
     *
     * @return string
     */
    public function getUrlToHistory()
    {
        return $this->url_to_history;
    }

    /**
     * Set notepadId
     *
     * @param \AppBundle\Entity\Notepad $notepadId
     *
     * @return RodNagiosProblem
     */
    public function setNotepadId(\AppBundle\Entity\Notepad $notepadId = null)
    {
        $this->notepad_id = $notepadId;

        return $this;
    }

    /**
     * Get notepadId
     *
     * @return \AppBundle\Entity\Notepad
     */
    public function getNotepadId()
    {
        return $this->notepad_id;
    }

    /**
     * Set ticket
     *
     * @param \AppBundle\Entity\ROD\Ticket $ticket
     *
     * @return RodNagiosProblem
     */
    public function setTicket(\AppBundle\Entity\ROD\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \AppBundle\Entity\ROD\Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}
