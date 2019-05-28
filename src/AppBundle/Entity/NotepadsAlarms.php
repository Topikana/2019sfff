<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotepadsAlarms
 *
 * @ORM\Table(name="notepads_alarms")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotepadRepository")
 */
class NotepadsAlarms
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var int
     *
     * @ORM\Column(name="id_notepad",type="integer")
     */
    private $idNotepad;


    /**
     * @var string
     *
     * @ORM\Column(name="id_alarm", type="string")
     */
    private $idAlarm;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idNotepad
     *
     * @param integer $idNotepad
     *
     * @return NotepadsAlarms
     */
    public function setIdNotepad($idNotepad)
    {
        $this->idNotepad = $idNotepad;

        return $this;
    }

    /**
     * Get idNotepad
     *
     * @return integer
     */
    public function getIdNotepad()
    {
        return $this->idNotepad;
    }

    /**
     * Set idAlarm
     *
     * @param string $idAlarm
     *
     * @return NotepadsAlarms
     */
    public function setIdAlarm($idAlarm)
    {
        $this->idAlarm = $idAlarm;

        return $this;
    }

    /**
     * Get idAlarm
     *
     * @return string
     */
    public function getIdAlarm()
    {
        return $this->idAlarm;
    }
}
