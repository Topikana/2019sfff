<?php

namespace AppBundle\Entity\ROD;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table(name="r_o_d_ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ROD\TicketRepository")
 */
class Ticket
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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @var int
     *
     * @ORM\Column(name="pb_number", type="integer")
     */
    private $pbNumber;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RodNagiosProblem", mappedBy="ticket_id")
     */
    private $alarms;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alarms = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set site
     *
     * @param string $site
     *
     * @return Ticket
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
     * @return Ticket
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
     * Set description
     *
     * @param string $description
     *
     * @return Ticket
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
     * Set link
     *
     * @param string $link
     *
     * @return Ticket
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set pbNumber
     *
     * @param integer $pbNumber
     *
     * @return Ticket
     */
    public function setPbNumber($pbNumber)
    {
        $this->pbNumber = $pbNumber;

        return $this;
    }

    /**
     * Get pbNumber
     *
     * @return integer
     */
    public function getPbNumber()
    {
        return $this->pbNumber;
    }

    /**
     * Add alarm
     *
     * @param \AppBundle\Entity\RodNagiosProblem $alarm
     *
     * @return Ticket
     */
    public function addAlarm(\AppBundle\Entity\RodNagiosProblem $alarm)
    {
        $this->alarms[] = $alarm;

        return $this;
    }

    /**
     * Remove alarm
     *
     * @param \AppBundle\Entity\RodNagiosProblem $alarm
     */
    public function removeAlarm(\AppBundle\Entity\RodNagiosProblem $alarm)
    {
        $this->alarms->removeElement($alarm);
    }

    /**
     * Get alarms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlarms()
    {
        return $this->alarms;
    }
}
