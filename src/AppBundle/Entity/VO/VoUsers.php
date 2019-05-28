<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoUsers
 *
 * @ORM\Table(name="vo_users")
 * @ORM\Entity
 */
class VoUsers
{
    /**
     * @var string
     *
     * @ORM\Column(name="dn", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $dn;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=500)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="vo", type="string", length=50)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $vo;

    /**
     * @var string
     *
     * @ORM\Column(name="uservo", type="string", length=200)
     */
    private $uservo;

    /**
     * @var string
     *
     * @ORM\Column(name="ca", type="string", length=500)
     */
    private $ca;

    /**
     * @var string
     *
     * @ORM\Column(name="urlvo", type="string", length=1000)
     */
    private $urlvo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="datetime", length=25)
     */
    private $last_update;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="first_update", type="datetime", length=25)
     */
    private $first_update;



    /**
     * Set dn
     *
     * @param string $dn
     *
     * @return VoUsers
     */
    public function setDn($dn)
    {
        $this->dn = $dn;

        return $this;
    }

    /**
     * Get dn
     *
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return VoUsers
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set vo
     *
     * @param string $vo
     *
     * @return VoUsers
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
     * Set uservo
     *
     * @param string $uservo
     *
     * @return VoUsers
     */
    public function setUservo($uservo)
    {
        $this->uservo = $uservo;

        return $this;
    }

    /**
     * Get uservo
     *
     * @return string
     */
    public function getUservo()
    {
        return $this->uservo;
    }

    /**
     * Set ca
     *
     * @param string $ca
     *
     * @return VoUsers
     */
    public function setCa($ca)
    {
        $this->ca = $ca;

        return $this;
    }

    /**
     * Get ca
     *
     * @return string
     */
    public function getCa()
    {
        return $this->ca;
    }

    /**
     * Set urlvo
     *
     * @param string $urlvo
     *
     * @return VoUsers
     */
    public function setUrlvo($urlvo)
    {
        $this->urlvo = $urlvo;

        return $this;
    }

    /**
     * Get urlvo
     *
     * @return string
     */
    public function getUrlvo()
    {
        return $this->urlvo;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return VoUsers
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
     * Set firstUpdate
     *
     * @param \DateTime $firstUpdate
     *
     * @return VoUsers
     */
    public function setFirstUpdate($firstUpdate)
    {
        $this->first_update = $firstUpdate;

        return $this;
    }

    /**
     * Get firstUpdate
     *
     * @return \DateTime
     */
    public function getFirstUpdate()
    {
        return $this->first_update;
    }
}
