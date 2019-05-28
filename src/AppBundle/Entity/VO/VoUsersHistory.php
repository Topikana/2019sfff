<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoUsersHistory
 *
 * @ORM\Table(name="vo_users_history")
 * @ORM\Entity
 */
class VoUsersHistory
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
     * @var int
     *
     * @ORM\Column(name="u_month", type="integer", length=2)
     */
    private $u_month;

    /**
     * @var int
     *
     * @ORM\Column(name="u_year", type="integer", length=4)
     */
    private $u_year;

    /**
     * @var int
     *
     * @ORM\Column(name="nbtotal", type="integer", length=5)
     */
    private $nbtotal;

    /**
     * @var int
     *
     * @ORM\Column(name="nbremoved", type="integer", length=5)
     */
    private $nbremoved;

    /**
     * @var int
     *
     * @ORM\Column(name="nbadded", type="integer", length=5)
     */
    private $nbadded;



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
     * @return VoUsersHistory
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
     * Set uMonth
     *
     * @param int $uMonth
     *
     * @return VoUsersHistory
     */
    public function setUMonth($uMonth)
    {
        $this->u_month = $uMonth;

        return $this;
    }

    /**
     * Get uMonth
     *
     * @return int
     */
    public function getUMonth()
    {
        return $this->u_month;
    }

    /**
     * Set uYear
     *
     * @param int $uYear
     *
     * @return VoUsersHistory
     */
    public function setUYear($uYear)
    {
        $this->u_year = $uYear;

        return $this;
    }

    /**
     * Get uYear
     *
     * @return int
     */
    public function getUYear()
    {
        return $this->u_year;
    }

    /**
     * Set nbtotal
     *
     * @param int $nbtotal
     *
     * @return VoUsersHistory
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

    /**
     * Set nbremoved
     *
     * @param int $nbremoved
     *
     * @return VoUsersHistory
     */
    public function setNbremoved($nbremoved)
    {
        $this->nbremoved = $nbremoved;

        return $this;
    }

    /**
     * Get nbremoved
     *
     * @return int
     */
    public function getNbremoved()
    {
        return $this->nbremoved;
    }

    /**
     * Set nbadded
     *
     * @param int $nbadded
     *
     * @return VoUsersHistory
     */
    public function setNbadded($nbadded)
    {
        $this->nbadded = $nbadded;

        return $this;
    }

    /**
     * Get nbadded
     *
     * @return int
     */
    public function getNbadded()
    {
        return $this->nbadded;
    }
}
