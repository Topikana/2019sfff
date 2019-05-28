<?php

namespace AppBundle\Entity\Broadcast;

use Doctrine\ORM\Mapping as ORM;

/**
 * MailMessage
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MailMessage
{
    /**
     * @var string
     *
     * @ORM\Column(name="message", type="blob")
     */
    private $message;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;




    /**
     * Set message
     *
     * @param string $message
     *
     * @return MailMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get id
     * @codeCoverageIgnore
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


}
