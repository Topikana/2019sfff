<?php
/**
 * Created by PhpStorm.
 * User: debarban
 * Date: 04/09/2018
 * Time: 16:39
 */

namespace AppBundle\Entity\PlugSla;


use Doctrine\ORM\Mapping as ORM;

/**
 * TicketStatus
 *
 * @ORM\Table(name="ticket_status")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TicketStatusRepository")
 */
class TicketStatus
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
     * @ORM\Column(name="idTicket", type="string", length=255, unique=true)
     */
    private $idTicket;

    /**
     * @var string
     *
     * @ORM\Column(name="status",type="string", columnDefinition="enum('incoming', 'accepted', 'rejected')",nullable=false)
     */
    private $status;


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
     * @return TicketStatus
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
     * Set status
     *
     * @param string $status
     *
     * @return TicketStatus
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
}
