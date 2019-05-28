<?php

namespace AppBundle\Entity\PlugSla;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TypeTicket
 *
 * @ORM\Table(name="plug_sla_type_ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlugSla\TypeTicketRepository")
 */
class TypeTicket
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlugSla\GroupeTicket", mappedBy="typeTicket")
     */
    private $groupeTickets;

    public function __construct()
    {
        $this->groupeTickets = new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;




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
     * Set type
     *
     * @param string $type
     *
     * @return TypeTicket
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add groupeTicket
     *
     * @param \AppBundle\Entity\PlugSla\GroupeTicket $groupeTicket
     *
     * @return TypeTicket
     */
    public function addGroupeTicket(\AppBundle\Entity\PlugSla\GroupeTicket $groupeTicket)
    {
        $this->groupeTickets[] = $groupeTicket;

        return $this;
    }

    /**
     * Remove groupeTicket
     *
     * @param \AppBundle\Entity\PlugSla\GroupeTicket $groupeTicket
     */
    public function removeGroupeTicket(\AppBundle\Entity\PlugSla\GroupeTicket $groupeTicket)
    {
        $this->groupeTickets->removeElement($groupeTicket);
    }

    /**
     * Get groupeTickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupeTickets()
    {
        return $this->groupeTickets;
    }

    public function __toString()
    {
        return $this->getType();
    }
}
