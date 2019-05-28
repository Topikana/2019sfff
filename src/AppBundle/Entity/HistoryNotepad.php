<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 01/10/18
 * Time: 14:23
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="history_notepad")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistoryNotepadRepository")
 */
class HistoryNotepad
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
     * @var integer
     * @ORM\Column(name="notepad_id", type="integer")
     */
    private $notepadId;


    /**
     * @var array
     * @ORM\Column(name="alarm_id", type="array")
     */
    private $alarmId;


    /**
     * @var array
     * @ORM\Column(name="status", type="array")
     */
    private $status;

    /**
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=false)
     *
     */
    private $creation_date;


    /**
     * @var int
     *
     * @ORM\Column(name="comment_id", type="integer", nullable=true)
     */
    private $commentId;



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
     * Set notepadId
     *
     * @param integer $notepadId
     *
     * @return HistoryNotepad
     */
    public function setNotepadId($notepadId)
    {
        $this->notepadId = $notepadId;

        return $this;
    }

    /**
     * Get notepadId
     *
     * @return integer
     */
    public function getNotepadId()
    {
        return $this->notepadId;
    }

    /**
     * Set alarmId
     *
     * @param array $alarmId
     *
     * @return HistoryNotepad
     */
    public function setAlarmId($alarmId)
    {
        $this->alarmId = $alarmId;

        return $this;
    }

    /**
     * Get alarmId
     *
     * @return array
     */
    public function getAlarmId()
    {
        return $this->alarmId;
    }

    /**
     * Set status
     *
     * @param array $status
     *
     * @return HistoryNotepad
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return HistoryNotepad
     */
    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }


    /**
     * Set commentId
     *
     * @param integer $commentId
     *
     * @return HistoryNotepad
     */
    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;

        return $this;
    }

    /**
     * Get commentId
     *
     * @return integer
     */
    public function getCommentId()
    {
        return $this->commentId;
    }
}
