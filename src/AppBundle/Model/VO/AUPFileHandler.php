<?php

namespace AppBundle\Model\VO;

use Symfony\Component\Finder\Finder;

/**
 * Created by JetBrains PhpStorm.
 * User: Pierre Frébault
 * Date: 04/10/13
 * Time: 11:04
 * To change this template use File | Settings | File Templates.
 */

class AUPFileHandler
{
    public $voName = NULL;
    public $path = NULL;

    public function __construct($voName, $path)
    {
        $this->voName = $voName;
        $this->path = $path;
    }

    public function find()
    {
        $finder = new Finder();
        return $finder->files()->in($this->path)->name($this->voName . '*')->sortByModifiedTime();
    }

    public function getFileNames()
    {
        $finder = new Finder();
        $finder->files()->in($this->path)->name($this->voName . '*')->sortByModifiedTime();

        $listFile = array();

        foreach ($finder as $file) {
            $listFile[$file->getFilename()] = $file->getFilename();
        }
        return $listFile;
    }
}