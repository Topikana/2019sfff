<?php


if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', "134.158.231.106","134.158.231.28","134.158.231.26","134.158.239.26","134.158.231.106","134.158.231.111")))

{

    require_once(dirname(__FILE__) .'/../../sf1/index_notauthorized.php');

}
else {
    require_once(dirname(__FILE__) . '/../../sf1/../config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
    sfContext::createInstance($configuration)->dispatch();

}
