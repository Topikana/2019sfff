<?php

if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', "134.158.231.28","134.158.231.26","134.158.239.26","147.251.17.229","149.156.238.102","193.206.208.201","134.158.231.106","134.158.231.111")))

{

    require_once(dirname(__FILE__) .'/../../sf1/index_notauthorized.php');

}
else {
    require_once(dirname(__FILE__) . '/../../sf1/config/ProjectConfiguration.class.php');
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
    sfContext::createInstance($configuration)->dispatch();

}
