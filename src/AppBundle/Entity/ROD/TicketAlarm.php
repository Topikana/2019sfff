<?php

namespace AppBundle\Entity\ROD;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketAlarm
 *
 * @ORM\Table(name="r_o_d_ticket_alarm")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ROD\TicketAlarmRepository")
 */
class TicketAlarm
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
     * @ORM\Column(name="idTicket", type="string", length=255)
     */
    private $idTicket;

    /**
     * @var string
     *
     * @ORM\Column(name="idAlarm", type="string", length=255)
     */
    private $idAlarm;


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
     * Set idTicket
     *
     * @param string $idTicket
     *
     * @return TicketAlarm
     */
    public function setIdTicket($idTicket)
    {
        $this->idTicket = $idTicket;

        return $this;
    }

    /**
     * Get idTicket
     *
     * @return string
     */
    public function getIdTicket()
    {
        return $this->idTicket;
    }

    /**
     * Set idAlarm
     *
     * @param string $idAlarm
     *
     * @return TicketAlarm
     */
    public function setIdAlarm($idAlarm)
    {
        $this->idAlarm = $idAlarm;

        return $this;
    }

    /**
     * Get idAlarm
     *
     * @return string
     */
    public function getIdAlarm()
    {
        return $this->idAlarm;
    }
}
