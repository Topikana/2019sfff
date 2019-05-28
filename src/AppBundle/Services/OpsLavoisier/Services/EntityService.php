<?php

namespace AppBundle\Services\OpsLavoisier\Services;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 18/10/13
 */

use \Lavoisier\Query;
use \Lavoisier\Hydrators\EntriesHydrator;
use \Lavoisier\Hydrators\StringHydrator;
use AppBundle\Services\OpsLavoisier\BaseService;
use AppBundle\Services\OpsLavoisier\Exceptions\OpsLavoisierServiceException;
use AppBundle\Services\OpsLavoisier\Hydrators\NGISitesHydrator;
use AppBundle\Services\OpsLavoisier\Hydrators\SiteInfoHydrator;

class EntityService extends BaseService
{


    /**
     *
     *
     * @return
     */
    public function getSitesInfo()
    {

        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_prod_entities');
        try {

            $path = sprintf(
                "/NGIs/NGI/Sites/Site"
            );

            $lquery->setPath($path);
            $hydrator = new SiteInfoHydrator();
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;
    }


    /**
     * @param array $matchNN , list of NGI names to match
     * @param array $matchSN , list of site names to match
     * @return array, list of Site names
     */
    public function getSiteNames(array $matchNN = array(), array $matchSN = array(), $combine = false)
    {

        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_prod_entities');
        try {

            $path = sprintf(
                "/NGIs/NGI[%s]/Sites/Site[%s]/@name",
                Query::buildPredicate('@name', $matchNN),
                Query::buildPredicate('@name', $matchSN)
            );

            $lquery->setPath($path);
            $hydrator = new EntriesHydrator();
            $hydrator->setValueAsKey($combine);
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;
    }

    /**
     * @param array $matchNN , list of NGI names to match
     * @param array $matchSN , list of site names to match
     * @return array, list of NGI names
     */
    public function getNGINames(array $matchNN = array(), array $matchSN = array(), $combine = false)
    {
        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_prod_entities');
        try {
            $path = sprintf(
                "/NGIs/NGI[%s]/@name[%s]",
                Query::buildPredicate('Sites/Site/@name', $matchSN),
                Query::buildPredicate('.', $matchNN)
            );

            $lquery->setPath($path);
            $lquery->setHydrator(new EntriesHydrator($combine));
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }

        return $result;
    }

    /**
     * returns each NGI with a all sites in a tree structure
     * @return array
     */
    public function getTreeNames()
    {
        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_prod_entities_certified');
        try {

            $lquery->setHydrator(new NGISitesHydrator);
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;
    }

    /**
     * @param $entityType , ngi | site
     * @param $entryKey , one of entity attribute
     * @param $entryValue , one of entity attribute
     * @param $xpathPredicate
     * @return \Lavoisier\Query
     */
    public function getMapQuery($entityType, $entryKey, $entryValue, $xpathPredicate = 'true()')
    {
        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_entities_map');

        $path = sprintf(
            "/e:entries/e:entry[%s]",
            $xpathPredicate
        );

        $lquery->setPath($path);
        $lquery->setMethod('POST');
        $lquery->setPostFields(array(
            'entity_type' => $entityType,
            'entry_key' => $entryKey,
            'entry_value' => $entryValue
        ));
        return $lquery;

    }

    /**
     * returns true if a given site belongs to a given NGI
     * @param $siteName
     * @param $NGIName
     * @return bool
     */
    public function isSiteBelongsNGI($siteName, $NGIName)
    {
        $res = $this->getSiteNames(array($NGIName), array($siteName));
        return (count($res) > 0);
    }

    public function isSiteExists($siteName)
    {
        $res = $this->getSiteNames(array(), array($siteName));
        return (count($res) > 0);
    }

    public function getSiteNGI($siteName)
    {
        $xpathPredicate = sprintf("@key='%s'", $siteName);
        $query = $this->getMapQuery('site', 'name', 'ngi', $xpathPredicate);
        $query->setHydrator(new StringHydrator);
        $str = $query->execute();
        return $str;
    }

    public function getSiteContactEmail($siteName)
    {
        $xpathPredicate = sprintf("@key='%s'", $siteName);
        $query = $this->getMapQuery('site', 'name', 'contact_email', $xpathPredicate);
        $query->setHydrator(new StringHydrator);
        return $query->execute();

    }

    public function getSiteSecurityContactEmail($siteName)
    {
        $xpathPredicate = sprintf("@key='%s'", $siteName);
        $query = $this->getMapQuery('site', 'name', 'csirt_email', $xpathPredicate);
        $query->setHydrator(new StringHydrator);
        return $query->execute();

    }

    public function getNGIContactEmail($NGIName)
    {

        $xpathPredicate = sprintf("@key='%s'", $NGIName);
        $query = $this->getMapQuery('ngi', 'name', 'contact_email', $xpathPredicate);
        $query->setHydrator(new StringHydrator);
        return $query->execute();
    }

    public function getNGIRODEmail($NGIName)
    {
        $xpathPredicate = sprintf("@key='%s'", $NGIName);
        $query = $this->getMapQuery('ngi', 'name', 'rod_email', $xpathPredicate);
        $query->setHydrator(new StringHydrator);
        return $query->execute();
    }

    public function getNGISecurityEmail($NGIName)
    {
        $xpathPredicate = sprintf("@key='%s'", $NGIName);
        $query = $this->getMapQuery('ngi', 'name', 'SecurityMail', $xpathPredicate);
        $query->setHydrator(new StringHydrator);
        return $query->execute();
    }



    public function getNGIGgusSU($NGIName)
    {
        $xpathPredicate = sprintf("@key='%s'", $NGIName);
        $query = $this->getMapQuery('ngi', 'name', 'ggus_su', $xpathPredicate);
        $query->setHydrator(new StringHydrator);
        return $query->execute();
    }


    public function getNGINameByGgusSU($ggus_su)
    {
        $xpathPredicate = sprintf("@key='%s'", $ggus_su);
        $query = $this->getMapQuery('ngi', 'ggus_su', 'name', $xpathPredicate);
        $query->setHydrator(new StringHydrator);
        return $query->execute();
    }

    public function getSupportUnits()
    {
        $query = $this->getMapQuery('ngi', 'ggus_su', 'name', "not(@key='EGI.eu')");
        $query->setHydrator(new EntriesHydrator);
        return $query->execute();
    }

    public function getCODMailingList()
    {
        return 'operations@egi.eu';
    }

    public function getRODMailingList()
    {
        return 'all-operator-on-duty@mailman.egi.eu';
    }

    /**
     * returns a map formatted like : [site_name] = ngi_name for given sites
     * @param array $siteNames
     * @return \Lavoisier\Query
     */
    public function getParents(array $siteNames)
    {
        $predicate = Query::buildPredicate('@key', $siteNames, $operator = 'or', $encode_url = false);
        $query = $this->getMapQuery('site', 'name', 'ngi', $predicate);
        $query->setHydrator(new EntriesHydrator);
        return $query->execute();
    }

    public function getSiteAvailabilitiesReliabilities(array $siteNames = NULL)
    {

        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_get_site_av_re');

        if (isset($siteNames)) {
            $siteNames[0] = $siteNames[0] . "*";
            $siteList = implode("*", $siteNames);
            $siteList = $siteList . "*";
            $path = sprintf("/e:entries/e:entries[contains('%s', concat(@key,'*'))]", $siteList);
            $lquery->setPath($path);
        }



        try {
            $hydrator = new EntriesHydrator();

            $lquery->setHydrator($hydrator);


            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }


        return $result;


      //  return array();

    }

    /*
     * return optional site, ngi, rod or just ngi rod email list + entities names
     * allow to set same label if rod and ngi have same email
     */
    public function getEntitiesInfo($entityName, $entityType, $mergeValues = true,$isCsi=false)
    {
        if ($entityType !== 'site' && $entityType != 'ngi') {
            throw new \Exception("The type '$entityType' is not supported please use site|ngi");
        }

        if ($entityType === 'site') {
            $ngiName = $this->getSiteNGI($entityName);
        } else {
            $ngiName = $entityName;
        }
        $rod_label = 'rod';
        $ngi_label = 'ngi';

        if ($isCsi)
        {
            $ngi_mail = $this->getNGISecurityEmail($ngiName);
            $rod_mail = $ngi_mail ;
        }
            else {
                $ngi_mail = $this->getNGIContactEmail($ngiName);
                $rod_mail = $this->getNGIRODEmail($ngiName);
            }

        if ($ngi_mail === $rod_mail) {
            $ngi_label = $rod_label = 'ngi \/ rod';
        }

        if ($mergeValues === false) {

            $entitiesEmails = array(
                'ngi' => array('ngi_label' => $ngi_label, 'ngi_mail' => $ngi_mail, 'ngi_name' => $ngiName),
                'rod' => array('rod_label' => $rod_label, 'rod_mail' => $rod_mail),

            );

            if ($entityType === 'site') {

                if ($isCsi)
                    $site_mail=$this->getSiteSecurityContactEmail($entityName);
                else
                    $site_mail=$this->getSiteContactEmail($entityName);

                $entitiesEmails = array_merge(
                    $entitiesEmails,
                    array(
                        'site' => array(
                            'site_mail' => $site_mail,
                            'site_label' => 'site',
                            'site_name'=> $entityName
                        )
                    ));
            }


        } else {
            $entitiesEmails = array(
                'ngi_label' => $ngi_label,
                'ngi_mail' => $ngi_mail,
                'rod_label' => $rod_label,
                'rod_mail' => $rod_mail,
                'ngi_name' => $ngiName

            );

            if ($entityType === 'site') {

                if ($isCsi)
                    $site_mail=$this->getSiteSecurityContactEmail($entityName);
                else
                    $site_mail=$this->getSiteContactEmail($entityName);

                $entitiesEmails = array_merge(
                    $entitiesEmails,
                    array(
                        'site_mail' => $site_mail,
                        'site_label' => 'site',
                        'site_name' => $entityName
                    ));
            }

        }

        return $entitiesEmails;
    }


}
