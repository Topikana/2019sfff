<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 17/11/16
 * Time: 14:40
 */

namespace AppBundle\Helper\Backend;

use Lavoisier\Query;
use Lavoisier\Exceptions\CurlLException;

class BackendHelper
{


    /**
     * @Route("/testUrl", name="testURl")
     * test d'une url pour savoir si elle est accessible ou non
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testURl($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        $result = curl_exec($curl);
        if ($result !== false) {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($statusCode == 404) {
                return "Not reachable";
            } else {
                return "Reachable";
            }
        } else {
            return "Not reachable";
        }
    }

    /**
     * @param $lavoisierView
     * @return int
     * @throws \Exception
     * @throws \Lavoisier\Exceptions\CurlException
     * get nb views NOK (build failed) for a lavoisier site
     */
    public function getNbViewNOK($lavoisierView)
    {
        // status = success
        try {
            $lquery = new Query($lavoisierView, 'status', 'lavoisier');
            $lquery->setPath("/jmx/domain/group/mbean[@status='1']");

            $xml = simplexml_load_string($lquery->execute(), 'SimpleXMLElement', LIBXML_NOCDATA);

            $result = json_decode(json_encode($xml), TRUE);

            if (count($result) > 0) {
                if (isset($result["mbean"]["@attributes"])) {
                    return 1;
                } else {
                    return count($result["mbean"]);

                }
            } else {
                return count($result);

            }

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw $e;
        }
        // @codeCoverageIgnoreEnd

    }

    /**
     * get number of users modified today
     * @param $manager
     * @return mixed
     */
  /*  public function getNbUsersUpdatedToday($manager) {
        $nbVoUsers = $manager
            ->createQuery('SELECT count(vc) as nb FROM AppBundle:VO\VoContact vc')
            ->setParameter("now", new \DateTime())
            ->getResult();

        return $nbVoUsers[0]["nb"];

    }*/

    /**
     * get ten last modified vo users contact
     * @param $manager
     * @return mixed
     */
    public function  getTenLastInsertedContacts($doctrine) {
        try {
            $listVoContacts = $doctrine->getRepository("AppBundle:VO\VoContacts")->findBy(array(), array("id" => "DESC"), 10);

            return $listVoContacts;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * filtre un array sur un charactere
     * @param $fst
     * @param $arr
     * @return array
     */
    public function filter($fst, $arr){
        $new_arr=array();

        for($i=0;$i<=(count($arr)-1);$i++){
            if ($fst == "none") {
                if (trim($arr[$i]["last_name"]) == '') {
                    $new_arr[] = $arr[$i];
                }
            } else if ($fst == "special"){
                if (preg_match('@[&\'"/\\%*#()?$%-]@', substr($arr[$i]["last_name"], 0, 1))) {
                    $new_arr[] = $arr[$i];
                }
            }  else if ($fst == "numeric"){
                if (preg_match('/^[1-9][0-9]*$/', substr($arr[$i]["last_name"], 0, 1))) {
                    $new_arr[] = $arr[$i];
                }
            } else if(substr($arr[$i]["last_name"], 0, 1) == $fst){
                $new_arr[] = $arr[$i];
            }

        }
        return $new_arr;
    }

    /**
     * recuperation de la date de dernier update d'une table
     * @param $table
     * @throws \Exception
     */
    public function getLastUpdateDateForTable($container, $table) {
        try {
            $lquery = new Query($container->getParameter("lavoisierfr"), 'OPSCORE_lastupdate_table', 'lavoisier');
            $lquery->setMethod("POST");
            $lquery->setPostFields(array("table" => $table));

            $xml = simplexml_load_string($lquery->execute(), 'SimpleXMLElement', LIBXML_NOCDATA);

            $result = json_decode(json_encode($xml), TRUE);

            return $result["row"]["max_updated_at_"];

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw $e;
        }
        // @codeCoverageIgnoreEnd
    }

}