<?php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 19/03/13
 * Time: 14:33
 */
interface IVoEmailGetter
{
    public function VoManagers(ContainerInterface $container, $serial);

    public function NgiManagers(ContainerInterface $container,$serial);

    public function SiteManagers(ContainerInterface $container,$voName);
}