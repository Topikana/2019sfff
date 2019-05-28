<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 23/02/16
 * Time: 15:57
 */

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('is_numeric', array($this, 'isNumericFilter')),
            new \Twig_SimpleFilter('count_substring', array($this, 'countLinkFilter')),
            new \Twig_SimpleFilter('unserialize', array($this, 'unserializeFilter')),

        );
    }

    public function isNumericFilter($str){
        return is_numeric($str);
    }

    public function countLinkFilter($str) {

        return substr_count($str,'@');
    }

    public function unserializeFilter($str) {
        return unserialize($str);
    }

    public function getName()
    {
        return 'app_extension';
    }
}