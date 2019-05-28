<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoDisciplines
 *
 * @ORM\Table(name="vo_disciplines")
 * @ORM\Entity
 */
class VoDisciplines
{
    /**
     * @var int
     *
     * @ORM\Column(name="vo_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $vo_id;

    /**
     * @var int
     *
     * @ORM\Column(name="discipline_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $discipline_id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO\VoDisciplines")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vo_id", referencedColumnName="serial")
     * })
     */
    private $Vo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Disciplines", mappedBy="AppBundle\Entity\VO\VoDisciplines")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="discipline_id", referencedColumnName="discipline_id")
     * })
     */
    private $Disciplines;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Vo = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Disciplines = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set voId
     *
     * @param int $voId
     *
     * @return VoDisciplines
     */
    public function setVoId($voId)
    {
        $this->vo_id = $voId;

        return $this;
    }

    /**
     * Get voId
     *
     * @return int
     */
    public function getVoId()
    {
        return $this->vo_id;
    }

    /**
     * Set disciplineId
     *
     * @param int $disciplineId
     *
     * @return VoDisciplines
     */
    public function setDisciplineId($disciplineId)
    {
        $this->discipline_id = $disciplineId;

        return $this;
    }

    /**
     * Get disciplineId
     *
     * @return int
     */
    public function getDisciplineId()
    {
        return $this->discipline_id;
    }

    /**
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return VoDisciplines
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
     * Add discipline
     *
     * @param \AppBundle\Entity\VO\Disciplines $discipline
     * @codeCoverageIgnore
     * @return VoDisciplines
     */
    public function addDiscipline(\AppBundle\Entity\VO\Disciplines $discipline)
    {
        $this->Disciplines[] = $discipline;

        return $this;
    }

    /**
     * Remove discipline
     *
     * @param \AppBundle\Entity\VO\Disciplines $discipline
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDiscipline(\AppBundle\Entity\VO\Disciplines $discipline)
    {
        return $this->Disciplines->removeElement($discipline);
    }

    /**
     * Get disciplines
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDisciplines()
    {
        return $this->Disciplines;
    }
}
