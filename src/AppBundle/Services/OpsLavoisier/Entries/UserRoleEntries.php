<?php

namespace AppBundle\Services\OpsLavoisier\Entries;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 15/11/13
 */

use \Lavoisier\Entries\Entries;

class UserRoleEntries extends Entries
{
    const KEY_USER = 'user';
    const KEY_ROLE = 'USER_ROLE';
    const KEY_ENTITY_TYPE = 'ENTITY_TYPE';
    const KEY_ENTITY_NAME = 'ON_ENTITY';


    public function getDN() {
       return $this[self::KEY_USER]['CERTDN'];
    }

    public function getCN() {
        return $this[self::KEY_USER]['CN'];
    }

    public function getId() {
        return $this[self::KEY_USER]['PRIMARY_KEY'];
    }

    public function getContactEmail() {
        return $this[self::KEY_USER]['EMAIL'];
    }

    /**
     * @param string $entityType
     * @param string $entityName
     * @param string $rolePattern, a regexp to match
     * @return boolean
     */
    public function hasRole($rolePattern = null, $entityName = null, $entityType = null)
    {
        $result = 1;
        // check if user has one role at least if all parameters set to null
        if (($rolePattern == null) && ($entityName == null) && ($entityType == null)) {
            return ($this->count() - 1) >= 1;
        }
        // check string type or null
        $aoi = $this->getIterator();
        for ($aoi->seek(1); $aoi->valid(); $aoi->next()) {
            $role = $aoi->current();
            $result = 1;
            if ($rolePattern !== null) {
                $result = (preg_match(sprintf("/%s/", $rolePattern), $role[self::KEY_ROLE]) === 1) ? 1 : 0;
            }
            if ($entityName !== null) {
                $result &= (strcasecmp($entityName, $role[self::KEY_ENTITY_NAME]) === 0 ? 1 : 0);
            }
            if ($entityType !== null) {
                $result &= (strcasecmp($entityType, $role[self::KEY_ENTITY_TYPE]) === 0 ? 1 : 0);
            }
            //break
            if ($result === 1) {
                return true;
            }
        }

        return ($result === 1 ? true : false);
    }

}