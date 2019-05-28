<?php
/**
 * Created by PhpStorm.
 * User: debarban
 * Date: 03/08/2018
 * Time: 14:49
 */

namespace AppBundle\Document\PlugSla;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Ticket
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $author;

    /**
     * @MongoDB\Field(type="string")
     */
    private $subject;

    /**
     * @MongoDB\Field(type="string")
     */
    private $sla;

    /**
     * @MongoDB\Field(type="string")
     */
    private $surname;

    /**
     * @MongoDB\Field(type="string")
     */
    private $email;

    /**
     * @MongoDB\Field(type="string")
     */
    private $displayName;

    /**
     * @MongoDB\Field(type="string")
     */
    private $EOSCUniqueID;

    /**
     * @MongoDB\Field(type="string")
     */
    private $institution;

    /**
     * @MongoDB\Field(type="string")
     */
    private $departement;

    /**
     * @MongoDB\Field(type="string")
     */
    private $departementWebPage;

    /**
     * @MongoDB\Field(type="string")
     */
    private $linkedinProfile;

    /**
     * @MongoDB\Field(type="string")
     */
    private $description;

    /**
     * @MongoDB\Field(type="string")
     */
    private $reasonToAccess;


    /**
    * @MongoDB\Field(type="date")
    */
    private $dateCreated;

    /**
     * @MongoDB\Field(type="date")
     */
    private $dateUpdated;

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
     * Set author
     *
     * @param string $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Get author
     *
     * @return string $author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Get subject
     *
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set dateCreated
     *
     * @param date $dateCreated
     * @return $this
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return date $dateCreated
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateUpdated
     *
     * @param date $dateUpdated
     * @return $this
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return date $dateUpdated
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Set sla
     *
     * @param string $sla
     * @return $this
     */
    public function setSla($sla)
    {
        $this->sla = $sla;
        return $this;
    }

    /**
     * Get sla
     *
     * @return string $sla
     */
    public function getSla()
    {
        return $this->sla;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return $this
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * Get surname
     *
     * @return string $surname
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get displayName
     *
     * @return string $displayName
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set eOSCUniqueID
     *
     * @param string $eOSCUniqueID
     * @return $this
     */
    public function setEOSCUniqueID($eOSCUniqueID)
    {
        $this->EOSCUniqueID = $eOSCUniqueID;
        return $this;
    }

    /**
     * Get eOSCUniqueID
     *
     * @return string $eOSCUniqueID
     */
    public function getEOSCUniqueID()
    {
        return $this->EOSCUniqueID;
    }

    /**
     * Set institution
     *
     * @param string $institution
     * @return $this
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;
        return $this;
    }

    /**
     * Get institution
     *
     * @return string $institution
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set departement
     *
     * @param string $departement
     * @return $this
     */
    public function setDepartement($departement)
    {
        $this->departement = $departement;
        return $this;
    }

    /**
     * Get departement
     *
     * @return string $departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set departementWebPage
     *
     * @param string $departementWebPage
     * @return $this
     */
    public function setDepartementWebPage($departementWebPage)
    {
        $this->departementWebPage = $departementWebPage;
        return $this;
    }

    /**
     * Get departementWebPage
     *
     * @return string $departementWebPage
     */
    public function getDepartementWebPage()
    {
        return $this->departementWebPage;
    }

    /**
     * Set linkedinProfile
     *
     * @param string $linkedinProfile
     * @return $this
     */
    public function setLinkedinProfile($linkedinProfile)
    {
        $this->linkedinProfile = $linkedinProfile;
        return $this;
    }

    /**
     * Get linkedinProfile
     *
     * @return string $linkedinProfile
     */
    public function getLinkedinProfile()
    {
        return $this->linkedinProfile;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set reasonToAccess
     *
     * @param string $reasonToAccess
     * @return $this
     */
    public function setReasonToAccess($reasonToAccess)
    {
        $this->reasonToAccess = $reasonToAccess;
        return $this;
    }

    /**
     * Get reasonToAccess
     *
     * @return string $reasonToAccess
     */
    public function getReasonToAccess()
    {
        return $this->reasonToAccess;
    }
}
