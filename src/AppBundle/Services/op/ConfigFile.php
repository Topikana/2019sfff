<?php

namespace AppBundle\Services\op;

use Symfony\Component\Yaml\Parser;

/**
 * Use a specific Yaml file
 *
 *
 *
 * @package  operation-portal
 * @author     Olivier Lequeux <olivier.lequeux@cc.in2p3.fr>
 * @version    1 20011-01-06
 */
class opConfigFile {

    private  $path = NULL;
    private  $fileName = NULL;
    private  $conf   = NULL;


    function __construct($moduleName, $fileName, $path=NULL) {

        $this->fileName = $fileName;

        if($path) {
            $this->path =$path;
        }
        else {
            $this->path =$this->container->getParameter('kernel.cache_dir').DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR.$this->fileName;
            if(!file_exists( $this->path)) {
                 throw new \Exception('Unable to find config file '.$this->path);
            }
        }


        try {
            $yaml = new Parser();

            $this->conf = $yaml->parse(file_get_contents($this->path));

        }
        catch (\Exception $e) {
              throw new exception( $e);
            //throw new exception('Unable to load config file from '.$this->path);
        }
    }

    public function getConf() {return  $this->conf;}
    public function getFileName() {return  $this->fileName;}
    public function getPath() {return  $this->path;}


    public function renderTemplate($templateName, array $parameters) {

        $content = '';
        if (isset($this->conf[$templateName])) {

        // build matching paterns
            $paterns = array();
            foreach($parameters as $key => $item) {
               $paterns[] ='/{{'.$key.'}}/';
            }

            $content = preg_replace($paterns, $parameters, $this->conf[$templateName]);
            return $content;
        }
        else throw new \Exception("Unable to find $templateName template");

    }



}
?>
