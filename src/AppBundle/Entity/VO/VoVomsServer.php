<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoVomsServer
 *
 * @ORM\Table(name="vo_voms_server")
 * @ORM\Entity
 */
class VoVomsServer
{
    /**
     * @var int
     *
     * @ORM\Column(name="serial", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $serial;

    /**
     * @var string
     *
     * @ORM\Column(name="hostname", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $hostname;

    /**
     * @var int
     *
     * @ORM\Column(name="https_port", type="integer", length=4)
     */
    private $https_port;

    /**
     * @var int
     *
     * @ORM\Column(name="vomses_port", type="integer", length=4)
     */
    private $vomses_port;

    /**
     * @var int
     *
     * @ORM\Column(name="is_vomsadmin_server", type="integer", length=1)
     */
    private $is_vomsadmin_server;

    /**
     * @var string
     *
     * @ORM\Column(name="members_list_url", type="string", length=255)
     */
    private $members_list_url;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO_test\VoVomsServer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial", referencedColumnName="serial")
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
     * Set serial
     *
     * @param int $serial
     *
     * @return VoVomsServer
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
     * Set hostname
     *
     * @param string $hostname
     *
     * @return VoVomsServer
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set httpsPort
     *
     * @param int $httpsPort
     *
     * @return VoVomsServer
     */
    public function setHttpsPort($httpsPort)
    {
        $this->https_port = $httpsPort;

        return $this;
    }

    /**
     * Get httpsPort
     *
     * @return int
     */
    public function getHttpsPort()
    {
        return $this->https_port;
    }

    /**
     * Set vomsesPort
     *
     * @param int $vomsesPort
     *
     * @return VoVomsServer
     */
    public function setVomsesPort($vomsesPort)
    {
        $this->vomses_port = $vomsesPort;

        return $this;
    }

    /**
     * Get vomsesPort
     *
     * @return int
     */
    public function getVomsesPort()
    {
        return $this->vomses_port;
    }

    /**
     * Set isVomsadminServer
     *
     * @param int $isVomsadminServer
     *
     * @return VoVomsServer
     */
    public function setIsVomsadminServer($isVomsadminServer)
    {
        $this->is_vomsadmin_server = $isVomsadminServer;

        return $this;
    }

    /**
     * Get isVomsadminServer
     *
     * @return int
     */
    public function getIsVomsadminServer()
    {
        return $this->is_vomsadmin_server;
    }

    /**
     * Set membersListUrl
     *
     * @param string $membersListUrl
     *
     * @return VoVomsServer
     */
    public function setMembersListUrl($membersListUrl)
    {
        $this->members_list_url = $membersListUrl;

        return $this;
    }

    /**
     * Get membersListUrl
     *
     * @return string
     */
    public function getMembersListUrl()
    {
        return $this->members_list_url;
    }

    /**
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return VoVomsServer
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
