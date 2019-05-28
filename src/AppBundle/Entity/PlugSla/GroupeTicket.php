<?php

namespace AppBundle\Entity\PlugSla;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupeTicket
 *
 * @ORM\Table(name="plug_sla_groupe_ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlugSla\GroupeTicketRepository")
 */
class GroupeTicket
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PlugSla\TypeTicket", inversedBy="groupeTickets")
     * @ORM\JoinColumn(name="typeTicket_id", referencedColumnName="id")
     */
    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="DN_authorized", type="string", length=255)
     */
    private $dNAuthorized;

    /**
     * @var int
     *
     * @ORM\Column(name="idUser", type="integer", nullable=true)
     */
    private $idUser;


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
     * Set dNAuthorized
     *
     * @param string $dNAuthorized
     *
     * @return GroupeTicket
     */
    public function setDNAuthorized($dNAuthorized)
    {
        $this->dNAuthorized = $dNAuthorized;

        return $this;
    }

    /**
     * Get dNAuthorized
     *
     * @return string
     */
    public function getDNAuthorized()
    {
        return $this->dNAuthorized;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return GroupeTicket
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\PlugSla\TypeTicket $type
     *
     * @return GroupeTicket
     */
    public function setType(\AppBundle\Entity\PlugSla\TypeTicket $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\PlugSla\TypeTicket
     */
    public function getType()
    {
        return $this->type;
    }
}
