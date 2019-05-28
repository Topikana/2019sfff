<?php
/**
 * Created by PhpStorm.
 * User: debarban
 * Date: 09/08/2018
 * Time: 14:31
 */

namespace AppBundle\Document\PlugSla;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */

class Version
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $idTicket;

    /**
     * @MongoDB\Field(type="string")
     */
    private $idDocument;

    /**
     * @MongoDB\Field(type="date")
     */
    private $date;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idTicket
     *
     * @param string $idTicket
     * @return $this
     */
    public function setIdTicket($idTicket)
    {
        $this->idTicket = $idTicket;
        return $this;
    }

    /**
     * Get idTicket
     *
     * @return string $idTicket
     */
    public function getIdTicket()
    {
        return $this->idTicket;
    }

    /**
     * Set idDocument
     *
     * @param string $idDocument
     * @return $this
     */
    public function setIdDocument($idDocument)
    {
        $this->idDocument = $idDocument;
        return $this;
    }

    /**
     * Get idDocument
     *
     * @return string $idDocument
     */
    public function getIdDocument()
    {
        return $this->idDocument;
    }

    /**
     * Set date
     *
     * @param date $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->date;
    }
}
