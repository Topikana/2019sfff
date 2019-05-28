<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoUsersMetrics
 *
 * @ORM\Table(name="vo_users_metrics")
 * @ORM\Entity
 */
class VoUsersMetrics
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
     * @ORM\Column(name="vo", type="string", length=255)
     */
    private $vo;

    /**
     * @var string
     *
     * @ORM\Column(name="discipline", type="string", length=255)
     */
    private $discipline;

    /**
     * @var string
     *
     * @ORM\Column(name="day_date", type="string", length=10)
     */
    private $day_date;

    /**
     * @var int
     *
     * @ORM\Column(name="nbtotal", type="integer", length=5)
     */
    private $nbtotal;



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
     * Set vo
     *
     * @param string $vo
     *
     * @return VoUsersMetrics
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
     * Set discipline
     *
     * @param string $discipline
     *
     * @return VoUsersMetrics
     */
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;

        return $this;
    }

    /**
     * Get discipline
     *
     * @return string
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * Set dayDate
     *
     * @param string $dayDate
     *
     * @return VoUsersMetrics
     */
    public function setDayDate($dayDate)
    {
        $this->day_date = $dayDate;

        return $this;
    }

    /**
     * Get dayDate
     *
     * @return string
     */
    public function getDayDate()
    {
        return $this->day_date;
    }

    /**
     * Set nbtotal
     *
     * @param int $nbtotal
     *
     * @return VoUsersMetrics
     */
    public function setNbtotal($nbtotal)
    {
        $this->nbtotal = $nbtotal;

        return $this;
    }

    /**
     * Get nbtotal
     *
     * @return int
     */
    public function getNbtotal()
    {
        return $this->nbtotal;
    }
}
