<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Notepad
 *
 * @ORM\Table(name="notepad")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotepadRepository")
 */
class Notepad
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var array
     *
     * @ORM\Column(name="comment", type="array")
     */
    private $comment;

    /**
     * @var array
     *
     * @ORM\Column(name="carbon_copy", type="array")
     */
    private $carbonCopy;


    /**
     * @var array
     *
     * @ORM\Column(name="group_alarms", type="array")
     */
    public $group_alarms;


    /**
     * @var string
     *
     * @ORM\Column(name="currentPlace", type="string", length=255)
     */
    public $currentPlace = 'create';


    /**
     * @var string
     *
     * @ORM\Column(name="site", type="text")
     *
     */
    private $site;

    /**
     * @var boolean
     *
     * @ORM\Column(name="linktoalarm", type="boolean")
     */
    private $linkToAlarm = true;

    /**
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=false)
     *
     */
    private $creation_date;


    /**
     *
     * @ORM\Column(name="last_update", type="datetime", nullable=false)
     */
    private $last_update;


    /**
     * @var string
     *
     * @ORM\Column(name="last_modifer", type="string", length=255)
     *
     */
    private $last_modifer;

    /**
     * @var integer
     * @ORM\Column(name="status", type="integer", length=9)
     *
     */
    private $status;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->group_alarms = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set subject
     *
     * @param string $subject
     *
     * @return Notepad
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }


    /**
     * Set carbonCopy
     *
     * @param array $carbonCopy
     *
     * @return Notepad
     */
    public function setCarbonCopy($carbonCopy)
    {
        $this->carbonCopy = $carbonCopy;

        return $this;
    }

    /**
     * Get carbonCopy
     *
     * @return array
     */
    public function getCarbonCopy()
    {
        return $this->carbonCopy;
    }

    /**
     * Set currentPlace
     *
     * @param string $currentPlace
     *
     * @return Notepad
     */
    public function setCurrentPlace($currentPlace)
    {
        $this->currentPlace = $currentPlace;

        return $this;
    }

    /**
     * Get currentPlace
     *
     * @return string
     */
    public function getCurrentPlace()
    {
        return $this->currentPlace;
    }

    /**
     * Set site
     *
     * @param string $site
     *
     * @return Notepad
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
     * Set linkToAlarm
     *
     * @param boolean $linkToAlarm
     *
     * @return Notepad
     */
    public function setLinkToAlarm($linkToAlarm)
    {
        $this->linkToAlarm = $linkToAlarm;

        return $this;
    }

    /**
     * Get linkToAlarm
     *
     * @return boolean
     */
    public function getLinkToAlarm()
    {
        return $this->linkToAlarm;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Notepad
     */
    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return Notepad
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->last_update = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->last_update;
    }

    /**
     * Set lastModifer
     *
     * @param string $lastModifer
     *
     * @return Notepad
     */
    public function setLastModifer($lastModifer)
    {
        $this->last_modifer = $lastModifer;

        return $this;
    }

    /**
     * Get lastModifer
     *
     * @return string
     */
    public function getLastModifer()
    {
        return $this->last_modifer;
    }

    /**
     * Set groupAlarms
     *
     * @param array $groupAlarms
     *
     * @return Notepad
     */
    public function setGroupAlarms($groupAlarms)
    {
        $this->group_alarms = $groupAlarms;

        return $this;
    }

    /**
     * Get groupAlarms
     *
     * @return string
     */
    public function getGroupAlarms()
    {
        return $this->group_alarms;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Notepad
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
     * Set comment
     *
     * @param array $comment
     *
     * @return Notepad
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return array
     */
    public function getComment()
    {
        return $this->comment;
    }
}