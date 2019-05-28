<?php

namespace AppBundle\Entity\Broadcast;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BroadcastMessage
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BroadcastMessage
{
    /**
     * @var string
     *
     * @ORM\Column(name="author_email", type="string", length=1000)
     */
    private $author_email;

    /**
     * @var string
     *
     * @ORM\Column(name="author_cn", type="string", length=1000)
     */
    private $author_cn;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=500)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="string")
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="targets_mail", type="string")
     */
    private $targets_mail;

    /**
     * @var string
     *
     * @ORM\Column(name="targets_label", type="string")
     */
    private $targets_label;

    /**
     * @var string
     *
     * @ORM\Column(name="targets_id", type="string")
     */
    private $targets_id;

    /**
     * @var string
     *
     * @ORM\Column(name="bcc", type="string", length=2000)
     */
    private $bcc;

    /**
     * @var string
     *
     * @ORM\Column(name="cc", type="string", length=2000)
     */
    private $cc;

    /**
     * @var int
     *
     * @ORM\Column(name="publication_type", type="integer")
     */
    private $publication_type;

    /**
     * @var string
     *
     * @ORM\Column(name="attachements", type="string")
     */
    private $attachements;

    /**
     * @var int
     *
     * @ORM\Column(name="old_id", type="integer")
     */
    private $old_id;

    /**
     * @var int
     *
     * @ORM\Column(name="category", type="integer")
     */
    private $category;

    /**
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable
     */
    private $updated_at;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;



    /**
     * Set authorEmail
     *
     * @param string $authorEmail
     *
     * @return BroadcastMessage
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->author_email = $authorEmail;

        return $this;
    }

    /**
     * Get authorEmail
     *
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->author_email;
    }

    /**
     * Set authorCn
     *
     * @param string $authorCn
     *
     * @return BroadcastMessage
     */
    public function setAuthorCn($authorCn)
    {
        $this->author_cn = $authorCn;

        return $this;
    }

    /**
     * Get authorCn
     *
     * @return string
     */
    public function getAuthorCn()
    {
        return $this->author_cn;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return BroadcastMessage
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return BroadcastMessage
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set targetsMail
     *
     * @param string $targetsMail
     *
     * @return BroadcastMessage
     */
    public function setTargetsMail($targetsMail)
    {
        $this->targets_mail = $targetsMail;

        return $this;
    }

    /**
     * Get targetsMail
     *
     * @return string
     */
    public function getTargetsMail()
    {
        return $this->targets_mail;
    }

    /**
     * Set targetsLabel
     *
     * @param string $targetsLabel
     *
     * @return BroadcastMessage
     */
    public function setTargetsLabel($targetsLabel)
    {
        $this->targets_label = $targetsLabel;

        return $this;
    }

    /**
     * Get targetsLabel
     *
     * @return string
     */
    public function getTargetsLabel()
    {
        return $this->targets_label;
    }

    /**
     * Set targetsId
     *
     * @param string $targetsId
     *
     * @return BroadcastMessage
     */
    public function setTargetsId($targetsId)
    {
        $this->targets_id = $targetsId;

        return $this;
    }

    /**
     * Get targetsId
     *
     * @return string
     */
    public function getTargetsId()
    {
        return $this->targets_id;
    }

    /**
     * Set bcc
     *
     * @param string $bcc
     * @codeCoverageIgnore
     * @return BroadcastMessage
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * Get bcc
     * @codeCoverageIgnore
     * @return string
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * Set cc
     *
     * @param string $cc
     *
     * @return BroadcastMessage
     */
    public function setCc($cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * Get cc
     *
     * @return string
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set publicationType
     *
     * @param int $publicationType
     *
     * @return BroadcastMessage
     */
    public function setPublicationType($publicationType)
    {
        $this->publication_type = $publicationType;

        return $this;
    }

    /**
     * Get publicationType
     *
     * @return int
     */
    public function getPublicationType()
    {
        return $this->publication_type;
    }

    /**
     * Set attachements
     *
     * @param string $attachements
     * @codeCoverageIgnore
     * @return BroadcastMessage
     */
    public function setAttachements($attachements)
    {
        $this->attachements = $attachements;

        return $this;
    }

    /**
     * Get attachements
     * @codeCoverageIgnore
     * @return string
     */
    public function getAttachements()
    {
        return $this->attachements;
    }

    /**
     * Set oldId
     *
     * @param int $oldId
     * @codeCoverageIgnore
     * @return BroadcastMessage
     */
    public function setOldId($oldId)
    {
        $this->old_id = $oldId;

        return $this;
    }

    /**
     * Get oldId
     * @codeCoverageIgnore
     * @return int
     */
    public function getOldId()
    {
        return $this->old_id;
    }

    /**
     * Set category
     *
     * @param int $category
     * @codeCoverageIgnore
     * @return BroadcastMessage
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     * @codeCoverageIgnore
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

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
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @codeCoverageIgnore
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     * @codeCoverageIgnore
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }


}
