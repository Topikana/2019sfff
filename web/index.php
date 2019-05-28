<?php

echo phpinfo();

require_once(dirname(__FILE__).'/../../sf1/config/ProjectConfiguration.class.php');


$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);

sfContext::createInstance($configuration)->dispatch();