<?php
/**
 * Created by PhpStorm.
 * User: frebault
 * Date: 15/12/15
 * Time: 15:30
 */

namespace AppBundle\Controller\Backend;

use AppBundle\Entity\Downtime\EmailStatusRepository;
use AppBundle\Helper\Backend\BackendHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Lavoisier\Hydrators\EntriesHydrator;
use Lavoisier\Query;

/**
 * Class BackendController
 * @package AppBundle\Controller\Backend
 * @Route("/a/backend")
 */
class BackendController extends Controller
{


    /**
     * home page
     * @Route("/", name="backendHome")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function backendHomeAction(Request $request)
    {


        $arrayLavoisier = array($this->container->getParameter("lavoisier01Url"),
            $this->container->getParameter("lavoisierfrUrl"),
            $this->container->getParameter("lavoisier0086Url"),
            $this->container->getParameter("lavoisier0089Url"));


        $nbLavoiserNOK = 0;

        $backendHelper = new BackendHelper();

        foreach ($arrayLavoisier as $lavoisier) {
            if ($backendHelper->testURl($lavoisier) != "Reachable") {
                $nbLavoiserNOK++;
            }
        }

        if ($nbLavoiserNOK == 0) {
            try {
                $nbViewsNOK01 = $backendHelper->getNbViewNOK($this->container->getParameter("lavoisier01"));
                $nbViewsNOK0086 = $backendHelper->getNbViewNOK($this->container->getParameter("lavoisier0086"));
                return $this->render(':backend/Home:home.html.twig', array("pageTitle" => "Home",
                    "nbLavoiserNOK" => $nbLavoiserNOK,
                    "nbViewsNOK01" => $nbViewsNOK01,
                    "nbViewsNOK0086" => $nbViewsNOK0086));
                //@codeCoverageIgnoreStart
            } catch (\Exception $e) {
                $this->addFlash("danger", $e->getMessage());
                return $this->render(':backend/Home:home.html.twig', array("pageTitle" => "Home",
                    "nbLavoiserNOK" => $nbLavoiserNOK,
                    "nbViewsNOK01" => "N.A",
                    "nbViewsNOK0086" => "N.A"));
            }
            //@codeCoverageIgnoreEnd
        } else {
            return $this->render(':backend/Home:home.html.twig', array("pageTitle" => "Home",
                "nbLavoiserNOK" => $nbLavoiserNOK,
                "nbViewsNOK01" => "N.A",
                "nbViewsNOK0086" => "N.A"));
        }
    }

    /**
     * get 10 last news vo contact added in DB
     * @Route("/lastTenVoContactAdd", name="lastTenVoContactAdd")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lastTenVoContactAddAction()
    {
        $backendHelper = new BackendHelper();

        try {
            $listVoContacts = $backendHelper->getTenLastInsertedContacts($this->getDoctrine());
        } catch (\Exception $e) {
            return $this->render(":backend/Home:lastTenContactsAdd.html.twig", array("error" => $e->getMessage()));
        }

        return $this->render(":backend/Home:lastTenContactsAdd.html.twig", array("listVoContacts" => $listVoContacts));

    }

    /**
     * get date of last update in nagios tables
     * @Route("/lastUpdatedTable", name="lastUpdatedTable")
     */
    public function lastUpdatedTableAction()
    {
        $backendHelper = new BackendHelper();

        $arrayTable = array("rod_nagios_problem", "csi_nagios_problem", "vdb_nagios_problem");

        $arrayTableUpdated = array();

        try {
            foreach ($arrayTable as $table) {
                $arrayTableUpdated[$table] = $backendHelper->getLastUpdateDateForTable($this->container, $table);
            }
        } catch (\Exception $e) {
            $this->addFlash("danger", $e->getMessage());
            return $this->render(":backend/Home:lastUpdateTable.html.twig", array("error" => $e->getMessage()));
        }
        return $this->render(":backend/Home:lastUpdateTable.html.twig", array("tabLastUpdate" => $arrayTableUpdated));

    }

    /**
     * get date of last email spool for sf1 and sf3
     * @Route("/lastSpool", name="lastSpool")
     */
    public function lastSpoolAction()
    {
        $arraySpool = array();

        try {

            // last spool SF3
            $lquery = new Query($this->container->getParameter("lavoisier01"), 'OPSCORE_portal_spoolMail', 'lavoisier');
            $xml = simplexml_load_string($lquery->execute(), 'SimpleXMLElement', LIBXML_NOCDATA);

            $tabSpoolSF3 = json_decode(json_encode($xml), TRUE);


            $arraySpool["sf3 lavoisier01"] = array(
                "GenerationDate" => trim(str_replace("[", "", explode("]", $tabSpoolSF3["entry"])[0])),
                "Sent" => trim(explode("...", $tabSpoolSF3["entry"])[1])
            );

            $lquery = new Query($this->container->getParameter("lavoisierfr"), 'OPSCORE_portal_spoolMail', 'lavoisier');
            $xml = simplexml_load_string($lquery->execute(), 'SimpleXMLElement', LIBXML_NOCDATA);

            $tabSpoolSF3 = json_decode(json_encode($xml), TRUE);


            $arraySpool["sf3 lavoisierfr"] = array(
                "GenerationDate" => trim(str_replace("[", "", explode("]", $tabSpoolSF3["entry"])[0])),
                "Sent" => trim(explode("...", $tabSpoolSF3["entry"])[1])
            );


            //last spool SF1
            $lquery = new Query($this->container->getParameter("lavoisier01"), 'OPSCORE_portal_cron_sendMail', 'lavoisier');
            $xml = simplexml_load_string($lquery->execute(), 'SimpleXMLElement', LIBXML_NOCDATA);

            $tabSpoolSF1 = json_decode(json_encode($xml), TRUE);

            $arraySpool["sf1 lavoisier01"] = array(
                "GenerationDate" => $tabSpoolSF1["GenerationDate"],
                "Sent" => $tabSpoolSF1["Sent"] . " emails sent"
            );

            $lquery = new Query($this->container->getParameter("lavoisierfr"), 'OPSCORE_portal_cron_sendMail', 'lavoisier');
            $xml = simplexml_load_string($lquery->execute(), 'SimpleXMLElement', LIBXML_NOCDATA);

            $tabSpoolSF1 = json_decode(json_encode($xml), TRUE);

            $arraySpool["sf1 lavoisierfr"] = array(
                "GenerationDate" => $tabSpoolSF1["GenerationDate"],
                "Sent" => $tabSpoolSF1["Sent"] . " emails sent"
            );


            return $this->render(":backend/Home:lastSpool.html.twig", array("tabSpool" => $arraySpool));

        } catch (\Exception $e) {
            return $this->render(":backend/Home:lastSpool.html.twig", array("error" => $e->getMessage()));
        }

    }

    /**
     * get date of last notification for downtimes
     * @Route("/lastDowntimeNotification", name="lastDowntimeNotification")
     */
    public function lastDowntimeNotificationAction()
    {
        try {
            /** @var  $downtime /AppBundle/Entity/Downtime/EmailStatus */
            $downtime = $this->getDoctrine()->getRepository("AppBundle:Downtime\EmailStatus")->findOneBy(array(), array("addingSentDate" => "DESC"));

            $dateDowntime = $downtime->getAddingSentDate();

            return $this->render(":backend/Home:lastDowntimeNotification.html.twig", array("dateDowntime" => $dateDowntime));
        } catch (\Exception $e) {
            return $this->render(":backend/Home:lastDowntimeNotification.html.twig", array("error" => $e->getMessage()));
        }

    }


}