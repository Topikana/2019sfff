<?php

/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 03/12/15
 * Time: 14:20
 */

namespace AppBundle\Entity\Home;

use Symfony\Component\Validator\Constraints as Assert;

class Mail
{

    /**
     * @Assert\NotBlank(message="Please enter a name.")
     * @Assert\Regex(
     * pattern="/^\w+/",
     * match=true,
     * message="Your name can only contain apha numeric information."
     * )
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Your name must be at least {{ limit }} characters long",
     *      maxMessage = "Your name cannot be longer than {{ limit }} characters"
     * )
     */
    protected $name;

    /**
     * @Assert\NotBlank(
     * message="Please enter a mail."
     * )
     * @Assert\Email(
     *      message="The value {{ value }} is not a valid email type."
     * )
     */
    protected $email;

    /**
     * @Assert\Email(
     *      message="The value {{ value }} is not a valid email type."
     * )
     */
    protected $cc;

    /**
     * @Assert\NotBlank(
     * message="Please enter the subject of your message."
     * )
    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "The subject must be at least {{ limit }} characters long",
     *      maxMessage = "The subject cannot be longer than {{ limit }} characters"
     * )
     */
    protected $subject;

    /**
     * @Assert\NotBlank(
    * message="Please enter the text of your message."
     * )
     * @Assert\Length(
     *      min = 2,
     *      max = 1200,
     *      minMessage = "The body of your message must be at least {{ limit }} characters long",
     *      maxMessage = "The body of your message cannot be longer than {{ limit }} characters"
     * )
     */
    protected  $body;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param mixed $cc
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }


}