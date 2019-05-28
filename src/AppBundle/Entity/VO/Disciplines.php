<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * Disciplines
 *
 * @ORM\Table(name="disciplines")
 * @ORM\Entity
 */
class Disciplines
{
    /**
     * @var int
     *
     * @ORM\Column(name="discipline_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $discipline_id;

    /**
     * @var string
     *
     * @ORM\Column(name="discipline_label", type="string", length=100)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $discipline_label;



    /**
     * Set disciplineId
     *
     * @param int $disciplineId
     *
     * @return Disciplines
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
     * Set disciplineLabel
     *
     * @param string $disciplineLabel
     *
     * @return Disciplines
     */
    public function setDisciplineLabel($disciplineLabel)
    {
        $this->discipline_label = $disciplineLabel;

        return $this;
    }

    /**
     * Get disciplineLabel
     *
     * @return string
     */
    public function getDisciplineLabel()
    {
        return $this->discipline_label;
    }
}
