<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vo
 *
 * @ORM\Table(name="vo")
 * @ORM\Entity
 */
class Vo
{
    /**
     * @var int
     *
     * @ORM\Column(name="serial", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $serial;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validation_date", type="datetime", length=25)
     */
    private $validation_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime", length=25)
     */
    private $creation_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change", type="datetime", length=25)
     */
    private $last_change;

    /**
     * @var int
     *
     * @ORM\Column(name="header_id", type="integer", length=4)
     */
    private $header_id;

    /**
     * @var int
     *
     * @ORM\Column(name="ressources_id", type="integer", length=4)
     */
    private $ressources_id;

    /**
     * @var int
     *
     * @ORM\Column(name="mailing_list_id", type="integer", length=4)
     */
    private $mailing_list_id;

    /**
     * @var int
     *
     * @ORM\Column(name="ggus_ticket_id", type="integer", length=4)
     */
    private $ggus_ticket_id;

    /**
     * @var int
     *
     * @ORM\Column(name="need_voms_help", type="integer", length=1)
     */
    private $need_voms_help;

    /**
     * @var int
     *
     * @ORM\Column(name="need_ggus_support", type="integer", length=1)
     */
    private $need_ggus_support;

    /**
     * @var int
     *
     * @ORM\Column(name="voms_ticket_id", type="integer", length=4)
     */
    private $voms_ticket_id;

    /**
     * @var int
     *
     * @ORM\Column(name="ggus_ticket_id_su_creation", type="integer", length=4)
     */
    private $ggus_ticket_id_su_creation;

    /**
     * @var int
     *
     * @ORM\Column(name="monitored", type="integer", length=2)
     */
    private $monitored;

    /**
     * @var int
     *
     * @ORM\Column(name="enable_team_ticket", type="integer", length=1)
     */
    private $enable_team_ticket;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoHeader", mappedBy="AppBundle\Entity\VO_test\Vo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="header_id", referencedColumnName="id",  onDelete="CASCADE")
     * })
     */
    private $VoHeader;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoRessources", mappedBy="AppBundle\Entity\VO_test\Vo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ressources_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $VoRessources;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoMailingList", mappedBy="AppBundle\Entity\VO_test\Vo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mailing_list_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $VoMailingList;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VoContactHasProfile", mappedBy="AppBundle\Entity\VO_test\Vo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial", referencedColumnName="serial")
     * })
     */
    private $VoContactHasProfile;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VoVomsGroup", mappedBy="AppBundle\Entity\VO_test\Vo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial", referencedColumnName="serial")
     * })
     */
    private $VoVomsGroup;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VoVomsServer", mappedBy="AppBundle\Entity\VO_test\Vo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial", referencedColumnName="serial")
     * })
     */
    private $VoVomsServer;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoDisciplines", mappedBy="AppBundle\Entity\VO_test\Vo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial", referencedColumnName="vo_id")
     * })
     */
    private $VoDisciplines;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->VoHeader = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoRessources = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoMailingList = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoContactHasProfile = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoVomsGroup = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoVomsServer = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoDisciplines = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Vo
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
     * Set validationDate
     *
     * @param \DateTime $validationDate
     *
     * @return Vo
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Vo
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
     * Set lastChange
     *
     * @param \DateTime $lastChange
     *
     * @return Vo
     */
    public function setLastChange($lastChange)
    {
        $this->last_change = $lastChange;

        return $this;
    }

    /**
     * Get lastChange
     *
     * @return \DateTime
     */
    public function getLastChange()
    {
        return $this->last_change;
    }

    /**
     * Set headerId
     *
     * @param int $headerId
     *
     * @return Vo
     */
    public function setHeaderId($headerId)
    {
        $this->header_id = $headerId;

        return $this;
    }

    /**
     * Get headerId
     *
     * @return int
     */
    public function getHeaderId()
    {
        return $this->header_id;
    }

    /**
     * Set ressourcesId
     *
     * @param int $ressourcesId
     *
     * @return Vo
     */
    public function setRessourcesId($ressourcesId)
    {
        $this->ressources_id = $ressourcesId;

        return $this;
    }

    /**
     * Get ressourcesId
     *
     * @return int
     */
    public function getRessourcesId()
    {
        return $this->ressources_id;
    }

    /**
     * Set mailingListId
     *
     * @param int $mailingListId
     *
     * @return Vo
     */
    public function setMailingListId($mailingListId)
    {
        $this->mailing_list_id = $mailingListId;

        return $this;
    }

    /**
     * Get mailingListId
     *
     * @return int
     */
    public function getMailingListId()
    {
        return $this->mailing_list_id;
    }

    /**
     * Set ggusTicketId
     *
     * @param int $ggusTicketId
     *
     * @return Vo
     */
    public function setGgusTicketId($ggusTicketId)
    {
        $this->ggus_ticket_id = $ggusTicketId;

        return $this;
    }

    /**
     * Get ggusTicketId
     *
     * @return int
     */
    public function getGgusTicketId()
    {
        return $this->ggus_ticket_id;
    }

    /**
     * Set needVomsHelp
     *
     * @param int $needVomsHelp
     *
     * @return Vo
     */
    public function setNeedVomsHelp($needVomsHelp)
    {
        $this->need_voms_help = $needVomsHelp;

        return $this;
    }

    /**
     * Get needVomsHelp
     *
     * @return int
     */
    public function getNeedVomsHelp()
    {
        return $this->need_voms_help;
    }

    /**
     * Set needGgusSupport
     *
     * @param int $needGgusSupport
     *
     * @return Vo
     */
    public function setNeedGgusSupport($needGgusSupport)
    {
        $this->need_ggus_support = $needGgusSupport;

        return $this;
    }

    /**
     * Get needGgusSupport
     *
     * @return int
     */
    public function getNeedGgusSupport()
    {
        return $this->need_ggus_support;
    }

    /**
     * Set vomsTicketId
     *
     * @param int $vomsTicketId
     *
     * @return Vo
     */
    public function setVomsTicketId($vomsTicketId)
    {
        $this->voms_ticket_id = $vomsTicketId;

        return $this;
    }

    /**
     * Get vomsTicketId
     *
     * @return int
     */
    public function getVomsTicketId()
    {
        return $this->voms_ticket_id;
    }

    /**
     * Set ggusTicketIdSuCreation
     *
     * @param int $ggusTicketIdSuCreation
     *
     * @return Vo
     */
    public function setGgusTicketIdSuCreation($ggusTicketIdSuCreation)
    {
        $this->ggus_ticket_id_su_creation = $ggusTicketIdSuCreation;

        return $this;
    }

    /**
     * Get ggusTicketIdSuCreation
     *
     * @return int
     */
    public function getGgusTicketIdSuCreation()
    {
        return $this->ggus_ticket_id_su_creation;
    }

    /**
     * Set monitored
     *
     * @param int $monitored
     *
     * @return Vo
     */
    public function setMonitored($monitored)
    {
        $this->monitored = $monitored;

        return $this;
    }

    /**
     * Get monitored
     *
     * @return int
     */
    public function getMonitored()
    {
        return $this->monitored;
    }

    /**
     * Set enableTeamTicket
     *
     * @param int $enableTeamTicket
     *
     * @return Vo
     */
    public function setEnableTeamTicket($enableTeamTicket)
    {
        $this->enable_team_ticket = $enableTeamTicket;

        return $this;
    }

    /**
     * Get enableTeamTicket
     *
     * @return int
     */
    public function getEnableTeamTicket()
    {
        return $this->enable_team_ticket;
    }

    /**
     * Add voHeader
     *
     * @param \AppBundle\Entity\VO\VoHeader $voHeader
     * @codeCoverageIgnore
     * @return Vo
     */
    public function addVoHeader(\AppBundle\Entity\VO\VoHeader $voHeader)
    {
        $this->VoHeader[] = $voHeader;

        return $this;
    }

    /**
     * Remove voHeader
     *
     * @param \AppBundle\Entity\VO\VoHeader $voHeader
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoHeader(\AppBundle\Entity\VO\VoHeader $voHeader)
    {
        return $this->VoHeader->removeElement($voHeader);
    }

    /**
     * Get voHeader
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoHeader()
    {
        return $this->VoHeader;
    }

    /**
     * Add voRessource
     *
     * @param \AppBundle\Entity\VO\VoRessources $voRessource
     * @codeCoverageIgnore
     * @return Vo
     */
    public function addVoRessource(\AppBundle\Entity\VO\VoRessources $voRessource)
    {
        $this->VoRessources[] = $voRessource;

        return $this;
    }

    /**
     * Remove voRessource
     *
     * @param \AppBundle\Entity\VO\VoRessources $voRessource
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoRessource(\AppBundle\Entity\VO\VoRessources $voRessource)
    {
        return $this->VoRessources->removeElement($voRessource);
    }

    /**
     * Get voRessources
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoRessources()
    {
        return $this->VoRessources;
    }

    /**
     * Add voMailingList
     *
     * @param \AppBundle\Entity\VO\VoMailingList $voMailingList
     * @codeCoverageIgnore
     * @return Vo
     */
    public function addVoMailingList(\AppBundle\Entity\VO\VoMailingList $voMailingList)
    {
        $this->VoMailingList[] = $voMailingList;

        return $this;
    }

    /**
     * Remove voMailingList
     *
     * @param \AppBundle\Entity\VO\VoMailingList $voMailingList
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoMailingList(\AppBundle\Entity\VO\VoMailingList $voMailingList)
    {
        return $this->VoMailingList->removeElement($voMailingList);
    }

    /**
     * Get voMailingList
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoMailingList()
    {
        return $this->VoMailingList;
    }

    /**
     * Add voContactHasProfile
     *
     * @param \AppBundle\Entity\VO\VoContactHasProfile $voContactHasProfile
     * @codeCoverageIgnore
     * @return Vo
     */
    public function addVoContactHasProfile(\AppBundle\Entity\VO\VoContactHasProfile $voContactHasProfile)
    {
        $this->VoContactHasProfile[] = $voContactHasProfile;

        return $this;
    }

    /**
     * Remove voContactHasProfile
     *
     * @param \AppBundle\Entity\VO\VoContactHasProfile $voContactHasProfile
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoContactHasProfile(\AppBundle\Entity\VO\VoContactHasProfile $voContactHasProfile)
    {
        return $this->VoContactHasProfile->removeElement($voContactHasProfile);
    }

    /**
     * Get voContactHasProfile
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoContactHasProfile()
    {
        return $this->VoContactHasProfile;
    }

    /**
     * Add voVomsGroup
     *
     * @param \AppBundle\Entity\VO\VoVomsGroup $voVomsGroup
     * @codeCoverageIgnore
     * @return Vo
     */
    public function addVoVomsGroup(\AppBundle\Entity\VO\VoVomsGroup $voVomsGroup)
    {
        $this->VoVomsGroup[] = $voVomsGroup;

        return $this;
    }

    /**
     * Remove voVomsGroup
     *
     * @param \AppBundle\Entity\VO\VoVomsGroup $voVomsGroup
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoVomsGroup(\AppBundle\Entity\VO\VoVomsGroup $voVomsGroup)
    {
        return $this->VoVomsGroup->removeElement($voVomsGroup);
    }

    /**
     * Get voVomsGroup
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoVomsGroup()
    {
        return $this->VoVomsGroup;
    }

    /**
     * Add voVomsServer
     *
     * @param \AppBundle\Entity\VO\VoVomsServer $voVomsServer
     * @codeCoverageIgnore
     * @return Vo
     */
    public function addVoVomsServer(\AppBundle\Entity\VO\VoVomsServer $voVomsServer)
    {
        $this->VoVomsServer[] = $voVomsServer;

        return $this;
    }

    /**
     * Remove voVomsServer
     *
     * @param \AppBundle\Entity\VO\VoVomsServer $voVomsServer
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoVomsServer(\AppBundle\Entity\VO\VoVomsServer $voVomsServer)
    {
        return $this->VoVomsServer->removeElement($voVomsServer);
    }

    /**
     * Get voVomsServer
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoVomsServer()
    {
        return $this->VoVomsServer;
    }

    /**
     * Add voDiscipline
     *
     * @param \AppBundle\Entity\VO\VoDisciplines $voDiscipline
     * @codeCoverageIgnore
     * @return Vo
     */
    public function addVoDiscipline(\AppBundle\Entity\VO\VoDisciplines $voDiscipline)
    {
        $this->VoDisciplines[] = $voDiscipline;

        return $this;
    }

    /**
     * Remove voDiscipline
     *
     * @param \AppBundle\Entity\VO\VoDisciplines $voDiscipline
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoDiscipline(\AppBundle\Entity\VO\VoDisciplines $voDiscipline)
    {
        return $this->VoDisciplines->removeElement($voDiscipline);
    }

    /**
     * Get voDisciplines
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoDisciplines()
    {
        return $this->VoDisciplines;
    }
}
