<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 26/09/18
 * Time: 10:51
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Comment
 *
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 */
class Comment
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
     * @var string
     * @ORM\Column(name="commentary", type="string", length=350)
     */
    private $commentary;

    /**
     * @var integer
     * @ORM\Column(name="notepad_id", type="integer")
     *
     */
    private $notepadId;

    /**
     * @var string
     * @ORM\Column(name="author", type="string", length=100)
     */
    private $author;

    /**
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=false)
     *
     */
    private $creation_date;


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
     * @return Comment
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
     * Set author
     *
     * @param string $author
     *
     * @return Comment
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set commentary
     *
     * @param string $commentary
     *
     * @return Comment
     */
    public function setCommentary($commentary)
    {
        $this->commentary = $commentary;

        return $this;
    }

    /**
     * Get commentary
     *
     * @return string
     */
    public function getCommentary()
    {
        return $this->commentary;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Comment
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
}
