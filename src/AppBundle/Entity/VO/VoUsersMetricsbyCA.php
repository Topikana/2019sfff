<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoUsersMetricsbyCA
 *
 * @ORM\Table(name="vo_users_metrics_ca")
 * @ORM\Entity
 */
class VoUsersMetricsbyCA
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
     * @ORM\Column(name="ca", type="string", length=255)
     */
    private $ca;

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
     * Set ca
     *
     * @param string $ca
     *
     * @return VoUsersMetricsbyCA
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
     * Set dayDate
     *
     * @param string $dayDate
     *
     * @return VoUsersMetricsbyCA
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
     * @return VoUsersMetricsbyCA
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
