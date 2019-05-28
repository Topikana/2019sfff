<?php
/**
 * Created by PhpStorm.
 * User: frebault
 * Date: 08/03/16
 * Time: 09:44
 */

namespace AppBundle\Entity\VO;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class AupFile
{
    /**
     * @Assert\File(
     *     maxSize = "5000000",
     *     mimeTypes = {"application/pdf","application/x-pdf","text/plain","text/htm","application/msword","application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.oasis.opendocument.text"},
     *     mimeTypesMessage = "Please upload a valid File"
     *
     * )
     */
    protected $aupFile;

    public $aupName;

    /**
     * @return File
     */
    public function getAupFile()
    {
        return $this->aupFile;
    }

    /**
     * @param File $aupFile
     */
    public function setAupFile(File $aupFile = null)
    {
        $this->aupFile = $aupFile;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->aupName;
    }

    /**
     * @param mixed $name
     */
    public function setName($aupName)
    {
        $this->aupName = $aupName;
    }



}
