<?php

namespace AppBundle\Entity\PlugSla;

use Doctrine\ORM\Mapping as ORM;

/**
 * SOProvideur
 *
 * @ORM\Table(name="plug_sla_s_o_provideur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlugSla\SOProvideurRepository")
 */
class SOProvideur
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
     *
     * @ORM\Column(name="SO", type="string", length=255)
     */
    private $sO;

    /**
     * @var string
     *
     * @ORM\Column(name="provideur", type="string", length=255)
     */
    private $provideur;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="idTicket", type="string", length=255)
     */
    private $idTicket;

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="array")
     */
    private $data;


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
     * Set sO
     *
     * @param string $sO
     *
     * @return SOProvideur
     */
    public function setSO($sO)
    {
        $this->sO = $sO;

        return $this;
    }

    /**
     * Get sO
     *
     * @return string
     */
    public function getSO()
    {
        return $this->sO;
    }

    /**
     * Set provideur
     *
     * @param string $provideur
     *
     * @return SOProvideur
     */
    public function setProvideur($provideur)
    {
        $this->provideur = $provideur;

        return $this;
    }

    /**
     * Get provideur
     *
     * @return string
     */
    public function getProvideur()
    {
        return $this->provideur;
    }

    /**
     * Set data
     *
     * @param array $data
     *
     * @return SOProvideur
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set idTicket
     *
     * @param string $idTicket
     *
     * @return SOProvideur
     */
    public function setIdTicket($idTicket)
    {
        $this->idTicket = $idTicket;

        return $this;
    }

    /**
     * Get idTicket
     *
     * @return string
     */
    public function getIdTicket()
    {
        return $this->idTicket;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return SOProvideur
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }
}
