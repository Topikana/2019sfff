<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * VoRobotCertificate
 *
 * @ORM\Table(name="vo_robot_certificate")
 * @ORM\Entity
 */
class VoRobotCertificate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="vo_name", type="string", length=255)
     */
    private $vo_name;

    /**
     * @var string
     *
     * @ORM\Column(name="service_name", type="string", length=255)
     */
    private $service_name;

    /**
     * @var string
     *
     * @ORM\Column(name="service_url", type="string", length=255)
     */
    private $service_url;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="robot_dn", type="string", length=255)
     */
    private $robot_dn;

    /**
     * @var int
     *
     * @ORM\Column(name="use_sub_proxies", type="integer", length=2)
     */
    private $use_sub_proxies;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO_test\VoRobotCertificate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial", referencedColumnName="serial")
     * })
     */
    private $Vo;


    /**
     *
     * @ORM\Column(name="validation_date", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $validation_date;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Vo = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * @return string
     */
    public function getVoName()
    {
        return $this->vo_name;
    }

    /**
     * @param string $vo_name
     */
    public function setVoName($vo_name)
    {
        $this->vo_name = $vo_name;
    }


    /**
     * Set robotCertificateId
     *
     * @param string $robotCertificateId
     *
     * @return VoRobotCertificate
     */
    public function setRobotCertificateId($robotCertificateId)
    {
        $this->robot_certificate_id = $robotCertificateId;

        return $this;
    }

    /**
     * Get robotCertificateId
     *
     * @return string
     */
    public function getRobotCertificateId()
    {
        return $this->robot_certificate_id;
    }



    /**
     * Set serviceName
     *
     * @param string $serviceName
     *
     * @return VoRobotCertificate
     */
    public function setServiceName($serviceName)
    {
        $this->service_name = $serviceName;

        return $this;
    }

    /**
     * Get serviceName
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Set serviceUrl
     *
     * @param string $serviceUrl
     *
     * @return VoRobotCertificate
     */
    public function setServiceUrl($serviceUrl)
    {
        $this->service_url = $serviceUrl;

        return $this;
    }

    /**
     * Get serviceUrl
     *
     * @return string
     */
    public function getServiceUrl()
    {
        return $this->service_url;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return VoRobotCertificate
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
     * Set robotDn
     *
     * @param string $robotDn
     *
     * @return VoRobotCertificate
     */
    public function setRobotDn($robotDn)
    {
        $this->robot_dn = $robotDn;

        return $this;
    }

    /**
     * Get robotDn
     *
     * @return string
     */
    public function getRobotDn()
    {
        return $this->robot_dn;
    }

    /**
     * Set useSubProxies
     *
     * @param int $useSubProxies
     *
     * @return VoRobotCertificate
     */
    public function setUseSubProxies($useSubProxies)
    {
        $this->use_sub_proxies = $useSubProxies;

        return $this;
    }

    /**
     * Get useSubProxies
     *
     * @return int
     */
    public function getUseSubProxies()
    {
        return $this->use_sub_proxies;
    }

    /**
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return VoRobotCertificate
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


    /**
     * @return mixed
     */
    public function getValidationDate()
    {
        return $this->validation_date;
    }

    /**
     * @codeCoverageIgnore
     * @param mixed $created_at
     */
    public function setValidationDate($validation_date)
    {
        $this->validation_date = $validation_date;
    }

}
