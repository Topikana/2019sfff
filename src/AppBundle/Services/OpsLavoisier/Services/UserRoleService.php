<?php

namespace AppBundle\Services\OpsLavoisier\Services;

/**
 * User: Olivier LEQUEUX
 * Date: 15/11/13
 */

use \Lavoisier\Query;
use \Lavoisier\Hydrators\EntriesHydrator;
use \Lavoisier\Hydrators\SimpleXMLHydrator;
use \OpsLavoisier\BaseService;
use \OpsLavoisier\Exceptions\OpsLavoisierServiceException;

class UserRoleService extends BaseService
{
    /**
     * returns all users matching given DN
     * @param string $dn
     * @return \OpsLavoisier\Entries\UserRoleEntries
     */
    public function findByDN($dn)
    {
        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_users_roles');
        try {

            $userDN = str_replace("/emailAddress=", "/Email=", $dn);
            $predicate = sprintf("@key='%s' or @key='%s'", $dn, $userDN);
            $path = sprintf("/e:entries/e:entries[%s]/e:entries", $predicate);
            $lquery->setPath($path);
            $hydrator = new EntriesHydrator();
            $hydrator->setRootBinding("\OpsLavoisier\Entries\UserRoleEntries");
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;
    }

    /**
     * returns all users matching given DN
     * @param string $dn
     * @return array of simplexml objects
     */
    public function findByDN_old($dn)
    {
        $lquery = new Query($this->hostname, 'OPSCORE_users');
        try {

            $userDN = str_replace("/emailAddress=", "/Email=", $dn);
            $predicate = sprintf("CERTDN='%s' or CERTDN='%s'", $dn, $userDN);
            $path = sprintf("/root/EGEE_USER[%s]", ($predicate));
            $lquery->setPath($path);
            $lquery->setHydrator(new SimpleXMLHydrator);
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;

    }
}
