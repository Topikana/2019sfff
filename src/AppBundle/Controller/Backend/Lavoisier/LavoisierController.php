<?php

namespace AppBundle\Controller\Backend\Lavoisier;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Lavoisier\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Helper\Backend\BackendHelper;

/**
 * @Route("/a/backend/lavoisier")
 */
class LavoisierController extends Controller
{

    /**
     * @Route("/globalStatus", name="globalStatus")
     * page global des vues en status Failure et OK pour cclavoisier01 et ccosvms0086
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function globalStatusAction()
    {


        $backendHelper = new BackendHelper();

        $statusLavoisier01 = $backendHelper->testURl($this->container->getParameter("lavoisier01Url"));
        $statusLavoisierfr = $backendHelper->testURl($this->container->getParameter("lavoisierfrUrl"));
        $statusLavoisier0086 = $backendHelper->testURl($this->container->getParameter("lavoisier0086Url"));
        $statusLavoisier0089 = $backendHelper->testURl($this->container->getParameter("lavoisier0089Url"));

        return $this->render(':backend/Lavoisier:globalStatus.html.twig',
            array("pageTitle" => "Global Status", "lavoisierStatus" => array(
                "cclavoisier01" => array("lavoisier" => $this->container->getParameter("lavoisier01Url"), "statusLavoisier" => $statusLavoisier01),
                "cclavoisierfr" => array("lavoisier" => $this->container->getParameter("lavoisierfrUrl"), "statusLavoisier" => $statusLavoisierfr),
                "ccosvms0086" => array("lavoisier" => $this->container->getParameter("lavoisier0086Url"), "statusLavoisier" => $statusLavoisier0086),
                "ccosvms0089" => array("lavoisier" => $this->container->getParameter("lavoisier0089Url"), "statusLavoisier" => $statusLavoisier0089)
            ))
        );


    }


    /**
     * @Route("/OkNokViews", name="OkNokViews")
     * page global des vues en status Failure et OK pour cclavoisier01 et ccosvms0086
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function OkNokViewsAction()
    {
        $backendHelper = new BackendHelper();

        $arrayError = array();

        /////////////////
        //cclavoisier01
        /////////////////

        if ($backendHelper->testURl($this->container->getParameter("lavoisier01Url")) == "Reachable") {
            // status = failure
            try {
                $arrayFailed01 = $this->callLavoisierGlobalStatus($this->container->getParameter("lavoisier01"), '1');

                // @codeCoverageIgnoreStart
            } catch (\Exception $e) {
                $this->addFlash("danger", "lavoisier cclavoisier01 call failed - view status for status FAILURE - " . $e->getMessage());
                return $this->render(":backend/Lavoisier:OkNokViews.html.twig", array("pageTitle" => "Failed and Succeed Builds"));
            }
            // @codeCoverageIgnoreEnd

            // status = success
            try {
                $arraySuccess01 = $this->callLavoisierGlobalStatus($this->container->getParameter("lavoisier01"), '3');
                // @codeCoverageIgnoreStart
            } catch (\Exception $e) {
                $this->addFlash("danger", "lavoisier cclavoisier01 call failed - view status for status OK - " . $e->getMessage());
                return $this->render(":backend/Lavoisier:OkNokViews.html.twig", array("pageTitle" => "Failed and Succeed Builds"));
            }
            // @codeCoverageIgnoreEnd
        } else {
            $arrayError["errorcclavoisier01"] = "lavoisier cclavoisier01 instance is unreachable for now...";

        }

        /////////////////
        //ccosvms0086
        /////////////////
        if ($backendHelper->testURl($this->container->getParameter("lavoisier0086Url")) == "Reachable") {

        // status = failure
        try {
            $arrayFailed0086 = $this->callLavoisierGlobalStatus($this->container->getParameter("lavoisier0086"), '1');
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $this->addFlash("danger", "lavoisier ccosvms0086 call failed - view status for status FAILURE - " . $e->getMessage());
            return $this->render(":backend/Lavoisier:OkNokViews.html.twig", array("pageTitle" => "Failed and Succeed Builds"));
        }
        // @codeCoverageIgnoreEnd


        // status = success
        try {
            $arraySuccess0086 = $this->callLavoisierGlobalStatus($this->container->getParameter("lavoisier0086"), '3');
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $this->addFlash("danger", "lavoisier ccosvms0086 call failed - view status for status OK -" . $e->getMessage());
            return $this->render(":backend/Lavoisier:OkNokViews.html.twig", array("pageTitle" => "Failed and Succeed Builds"));
        }
        // @codeCoverageIgnoreEnd

        } else {
            $arrayError["errorccosvms0086"] = "lavoisier ccosvms0086 instance is unreachable for now...";
        }


        if (count($arrayError) > 0) {
            return $this->render(':backend/Lavoisier:OkNokViews.html.twig', array(
                "pageTitle" => "Failure and Success Build",
                "errors" => $arrayError
            ));
        }

        return $this->render(':backend/Lavoisier:OkNokViews.html.twig', array(
            "pageTitle" => "Failure and Success Build",
            "lavoisierFailedViewsProd01" => $arrayFailed01,
            "lavoisierSuccessViewsProd01" => $arraySuccess01,
            "lavoisierFailedViewsProd0086" => $arrayFailed0086,
            "lavoisierSuccessViewsProd0086" => $arraySuccess0086
        ));

    }

    /**
     * appel de notify a lavoisier pour une vue donnée
     * @Route("/notifyLavoisierAjax", name="notifyLavoisierAjax")
     * notify lavoisier view
     * @param Request $request
     */
    public function notifyLavoisierAjaxAction(Request $request)
    {
        $lavoisier = $request->get("lavoisier");
        $view = $request->get("view");

        if ($lavoisier == null || $lavoisier == "") {
            return new Response("error on lavoisier call to notify view : missing lavoisier argument", 500);
        }

        if ($view == null || $view == "") {
            return new Response("error on lavoisier call to notify view : missing view argument", 500);
        }

        try {
            $lquery = new Query($lavoisier, $view, 'notify');
            $lquery->execute();
            return new Response("lavoisier call - " . $lavoisier . " - to notify view - " . $view . " was successfull !", 200);

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            return new Response("error on lavoisier call - " . $lavoisier . " - to notify view - " . $view . " - " . $e->getMessage(), 500);
        }
        // @codeCoverageIgnoreEnd


    }


    /**
     * lavoisier call pour récupérer l'ensemble des vues d'un status donné pour un lavoisier de prod
     * @param $lavoisierView
     * @param $statusType
     * @return array
     * @throws \Exception
     */
    private function callLavoisierGlobalStatus($lavoisierView, $statusType)
    {

        $arrayTemp = array();
        $arrayReturn = array();

        try {
            $lquery = new Query($lavoisierView, 'status', 'lavoisier');
            $lquery->setPath("/jmx/domain/group/mbean[@status='" . $statusType . "']");

            $xml = simplexml_load_string($lquery->execute(), 'SimpleXMLElement', LIBXML_NOCDATA);

            $result = json_decode(json_encode($xml), TRUE);


            if (count($result) > 0) {

                //construction du resultat : CAS 1 - 1 seule vue récupérée
                if (count($result["mbean"]) > 0 && count($result["mbean"]) < 2) {

                    foreach ($result["mbean"] as $key => $value) {


                        if (isset($value["name"])) {
                            $arrayTemp["view"] = $value["name"];

                        }

                        if (isset($value["notifiable"])) {
                            $arrayTemp["notifiable"] = $value["notifiable"];

                        }

                        foreach ($value as $k1 => $lvl1) {


                            if (isset($lvl1["@attributes"])) {

                                if ($statusType == "1") {


                                    if ($lvl1["@attributes"]["name"] == "LastBuildDate") {

                                        $arrayTemp["lastBuildDate"] = $lvl1["value"];
                                    }

                                    if ($lvl1["@attributes"]["name"] == "LastErrorDate") {
                                        $arrayTemp["lastErrorDate"] = $lvl1["value"];
                                    }

                                    if ($lvl1["@attributes"]["name"] == "LastException") {
                                        $arrayTemp["lastException"] = $lvl1["value"];
                                    }

                                    if ($lvl1["@attributes"]["name"] == "LastStackTrace") {
                                        $arrayTemp["lastStackTrace"] = $lvl1["value"];
                                    }
                                } else {
                                    if ($lvl1["@attributes"]["name"] == "LastBuildDate") {

                                        $arrayTemp["lastBuildDate"] = $lvl1["value"];
                                    }

                                    if ($lvl1["@attributes"]["name"] == "LastBuildTime") {
                                        $arrayTemp["lastBuildDuration"] = $lvl1["value"] / 1000;
                                    }

                                    if ($lvl1["@attributes"]["name"] == "LastBytesSize") {
                                        $arrayTemp["lastBuildSize"] = ceil($lvl1["value"] / 1024);
                                    }
                                }
                            }
                        }

                    }

                    $arrayReturn[] = $arrayTemp;

                    //construction du resultat : CAS 2 - plusieurs vues récupérées
                } else if (count($result["mbean"]) >= 2) {

                    // Infos à garder : mbean/@name, attribute/LastBuildDate, attribute/lastBuildDuration, attribute/lastBuildSize
                    foreach ($result["mbean"] as $key => $value) {
                        foreach ($value as $k1 => $lvl1) {

                            if (isset($lvl1["name"])) {
                                $arrayTemp["view"] = $lvl1["name"];
                            }

                            if (isset($lvl1["notifiable"])) {
                                $arrayTemp["notifiable"] = $lvl1["notifiable"];

                            }

                            foreach ($lvl1 as $k => $v) {
                                if (isset($v["@attributes"])) {

                                    if ($statusType == "1") {

                                        if ($v["@attributes"]["name"] == "LastBuildDate") {

                                            $arrayTemp["lastBuildDate"] = $v["value"];
                                        }
                                        if ($v["@attributes"]["name"] == "LastErrorDate") {
                                            $arrayTemp["lastErrorDate"] = $v["value"];
                                        }

                                        if ($v["@attributes"]["name"] == "LastException") {
                                            $arrayTemp["lastException"] = $v["value"];
                                        }

                                        if ($v["@attributes"]["name"] == "LastStackTrace") {
                                            $arrayTemp["lastStackTrace"] = $v["value"];
                                        }
                                    } else {

                                        if ($v["@attributes"]["name"] == "LastBuildDate") {
                                            $arrayTemp["lastBuildDate"] = $v["value"];
                                        }

                                        if ($v["@attributes"]["name"] == "LastBuildTime") {
                                            $arrayTemp["lastBuildDuration"] = $v["value"] / 1000;
                                        }

                                        if ($v["@attributes"]["name"] == "LastBytesSize") {
                                            $arrayTemp["lastBuildSize"] = ceil($v["value"] / 1024);
                                        }
                                    }

                                }

                            }
                        }
                        $arrayReturn[] = $arrayTemp;

                    }
                }
            }

            return $arrayReturn;


            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {

            throw $e;

        }
        // @codeCoverageIgnoreEnd

    }
}
