<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
{

    public function __construct()
    {
        $this->infrastructure = 'prod';
        $this->certifiedStatus = 'Certified';
        $this->alarmStatus = [2];
        $this->profile_alarm = ['OPS_MONITOR','ARGO_MON_OPERATORS','MW_MONITOR'];
        $this->filter_columns = 1;
    }


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
     * @ORM\Column(name="infrastructure", type="string", length=255)
     */
    private $infrastructure;

    /**
     * @var string
     *
     * @ORM\Column(name="certified_status", type="string", length=255)
     */
    private $certifiedStatus;

    /**
     * @var array
     *
     * @ORM\Column(name="alarm_status", type="array", length=255)
     */
    private $alarmStatus;

    /**
     * @var array
     *
     * @ORM\Column(name="profile_alarm", type="array", length=255)
     */
    private $profile_alarm;

    /**
     * @var string
     *
     * @ORM\Column(name="filter_columns", type="string", length=255)
     */
    private $filter_columns;

    /**
     * One Setting has One User.
     * @ORM\OneToOne(targetEntity="User", inversedBy="setting", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


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
     * Set infrastructure
     *
     * @param string $infrastructure
     *
     * @return Settings
     */
    public function setInfrastructure($infrastructure)
    {
        $this->infrastructure = $infrastructure;

        return $this;
    }

    /**
     * Get infrastructure
     *
     * @return string
     */
    public function getInfrastructure()
    {
        return $this->infrastructure;
    }

    /**
     * Set certifiedStatus
     *
     * @param string $certifiedStatus
     *
     * @return Settings
     */
    public function setCertifiedStatus($certifiedStatus)
    {
        $this->certifiedStatus = $certifiedStatus;

        return $this;
    }

    /**
     * Get certifiedStatus
     *
     * @return string
     */
    public function getCertifiedStatus()
    {
        return $this->certifiedStatus;
    }


    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Settings
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set profileAlarm
     *
     * @param string $profileAlarm
     *
     * @return Settings
     */
    public function setProfileAlarm($profileAlarm)
    {
        $this->profile_alarm = $profileAlarm;

        return $this;
    }

    /**
     * Get profileAlarm
     *
     * @return string
     */
    public function getProfileAlarm()
    {
        return $this->profile_alarm;
    }

    /**
     * Set alarmStatus
     *
     * @param string $alarmStatus
     *
     * @return Settings
     */
    public function setAlarmStatus($alarmStatus)
    {
        $this->alarmStatus = $alarmStatus;

        return $this;
    }

    /**
     * Get alarmStatus
     *
     * @return string
     */
    public function getAlarmStatus()
    {
        return $this->alarmStatus;
    }

    /**
     * Set filterColumns
     *
     * @param string $filterColumns
     *
     * @return Settings
     */
    public function setFilterColumns($filterColumns)
    {
        $this->filter_columns = $filterColumns;

        return $this;
    }

    /**
     * Get filterColumns
     *
     * @return string
     */
    public function getFilterColumns()
    {
        return $this->filter_columns;
    }
}
