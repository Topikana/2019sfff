<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoMetrics
 *
 * @ORM\Table(name="vo_metrics")
 * @ORM\Entity
 */
class VoMetrics
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
     * @ORM\Column(name="nb_vo", type="integer", length=3)
     */
    private $nb_vo;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_added", type="integer", length=3)
     */
    private $nb_added;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_removed", type="integer", length=10)
     */
    private $nb_removed;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_inter_vo", type="integer", length=3)
     */
    private $nb_inter_vo;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_inter_added", type="integer", length=3)
     */
    private $nb_inter_added;

    /**
     * @var int
     *
     * @ORM\Column(name="nb__inter_removed", type="integer", length=10)
     */
    private $nb__inter_removed;

    /**
     * @var string
     *
     * @ORM\Column(name="day_date", type="string", length=10)
     */
    private $day_date;



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
     * Set nbVo
     *
     * @param int $nbVo
     *
     * @return VoMetrics
     */
    public function setNbVo($nbVo)
    {
        $this->nb_vo = $nbVo;

        return $this;
    }

    /**
     * Get nbVo
     *
     * @return int
     */
    public function getNbVo()
    {
        return $this->nb_vo;
    }

    /**
     * Set nbAdded
     *
     * @param int $nbAdded
     *
     * @return VoMetrics
     */
    public function setNbAdded($nbAdded)
    {
        $this->nb_added = $nbAdded;

        return $this;
    }

    /**
     * Get nbAdded
     *
     * @return int
     */
    public function getNbAdded()
    {
        return $this->nb_added;
    }

    /**
     * Set nbRemoved
     *
     * @param int $nbRemoved
     *
     * @return VoMetrics
     */
    public function setNbRemoved($nbRemoved)
    {
        $this->nb_removed = $nbRemoved;

        return $this;
    }

    /**
     * Get nbRemoved
     *
     * @return int
     */
    public function getNbRemoved()
    {
        return $this->nb_removed;
    }

    /**
     * Set nbInterVo
     *
     * @param int $nbInterVo
     *
     * @return VoMetrics
     */
    public function setNbInterVo($nbInterVo)
    {
        $this->nb_inter_vo = $nbInterVo;

        return $this;
    }

    /**
     * Get nbInterVo
     *
     * @return int
     */
    public function getNbInterVo()
    {
        return $this->nb_inter_vo;
    }

    /**
     * Set nbInterAdded
     *
     * @param int $nbInterAdded
     *
     * @return VoMetrics
     */
    public function setNbInterAdded($nbInterAdded)
    {
        $this->nb_inter_added = $nbInterAdded;

        return $this;
    }

    /**
     * Get nbInterAdded
     *
     * @return int
     */
    public function getNbInterAdded()
    {
        return $this->nb_inter_added;
    }

    /**
     * Set nbInterRemoved
     *
     * @param int $nbInterRemoved
     *
     * @return VoMetrics
     */
    public function setNbInterRemoved($nbInterRemoved)
    {
        $this->nb__inter_removed = $nbInterRemoved;

        return $this;
    }

    /**
     * Get nbInterRemoved
     *
     * @return int
     */
    public function getNbInterRemoved()
    {
        return $this->nb__inter_removed;
    }

    /**
     * Set dayDate
     *
     * @param string $dayDate
     *
     * @return VoMetrics
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
}
