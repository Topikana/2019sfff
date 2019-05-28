<?php

namespace AppBundle\Tests\Entity\User;

use AppBundle\Entity\User;

class Populate extends \PHPUnit\Framework\TestCase
{

    /**
     * @return User
     */
    public static function createUser()
    {

        $data = Populate::datasource();

        $voTest = array(
            'atlas' => array(
                0 => 'VO MANAGER'
            ),
            'aegis' => array(
                0 => 'VO DEPUTY'
            ),
            "voNameExpert" => array(
                0 => "VO EXPERT"
            )
        );


        $userGridBody = new User($data["user.dn"], null, null, array("ROLE_USER"), $data["user.opRoles"]);
        $user = new User("/O=GRID-FR/C=FR/O=CNRS/OU=IPGP/CN=Marc Hufschmitt", null, null, array("ROLE_USER"), array());
        $userVo = new User("/C=GR/O=HellasGrid/OU=auth.gr/CN=Paschalis Korosoglou", null, null, array("ROLE_USER"), array("vo" => $voTest));
        $userVoExpert = new User("/O=GRID-FR/C=FR/O=CNRS/OU=IPGP/CN=Marc Hufschmitt", null, null, array("ROLE_USER"), array("vo" => array("atlas" => array(0=>"VO EXPERT"))));

        return array(
            "user" => $user,
            "userGB" => $userGridBody,
            "userVo" => $userVo,
            "userVoExpert" => $userVoExpert
        );

    }

    /**
     * data source for populate
     * @return array
     */
    public static function datasource()
    {

        $data = [];

        $data["user.dn"] = "/O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Cyril Lorphelin";
        $data["user.name"] = "Cyril Lorphelin";
        $data["user.opRoles"] = array(
            "ngi" => array(
                "NGI_FRANCE" => array(
                    0 => "NGI Security Officer",
                    1 => "NGI Operations Deputy Manager"
                )
            ),
            "site" => array(
                "IN2P3-CC-T2" => array(
                    0 => "Site Security Officer",
                    1 => "Site Operations Manager"
                ),
                "GRIDOPS-SAM" => array(0 => "Site Administrator"),
                "GRIDOPS-OPSPORTAL" => array(0 => "Site Operations Manager"),
                "IN2P3-CC" => array(0 => "Site Operations Manager")
            ),
            "project" => array(
                "EGI" => array(
                    0 => "GRID BODY"
                )
            )
        );

        return $data;
    }
}
