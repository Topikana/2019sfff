<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoYearlyValidation
 *
 * @ORM\Table(name="vo_yearly_validation")
 * @ORM\Entity
 */
class VoYearlyValidation
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
     * @var int
     *
     * @ORM\Column(name="serial", type="integer", length=4)
     */
    private $serial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_validation", type="datetime", length=25)
     */
    private $date_validation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_last_email_sending", type="datetime", length=25)
     */
    private $date_last_email_sending;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO_test\VoYearlyValidation")
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set serial
     *
     * @param int $serial
     *
     * @return VoYearlyValidation
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
     * Set dateValidation
     *
     * @param \DateTime $dateValidation
     *
     * @return VoYearlyValidation
     */
    public function setDateValidation($dateValidation)
    {
        $this->date_validation = $dateValidation;

        return $this;
    }

    /**
     * Get dateValidation
     *
     * @return \DateTime
     */
    public function getDateValidation()
    {
        return $this->date_validation;
    }

    /**
     * Set dateLastEmailSending
     *
     * @param \DateTime $dateLastEmailSending
     *
     * @return VoYearlyValidation
     */
    public function setDateLastEmailSending($dateLastEmailSending)
    {
        $this->date_last_email_sending = $dateLastEmailSending;

        return $this;
    }

    /**
     * Get dateLastEmailSending
     *
     * @return \DateTime
     */
    public function getDateLastEmailSending()
    {
        return $this->date_last_email_sending;
    }

    /**
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return VoYearlyValidation
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
     * set the date for date_validation field
     */
    public function setVoValidation(){
        date_default_timezone_set('UTC');
        $this->setDateValidation(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
        date_default_timezone_set('Europe/Paris');
    }

}
