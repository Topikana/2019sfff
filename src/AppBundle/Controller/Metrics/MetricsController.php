<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 14/03/16
 * Time: 16:07
 */

namespace AppBundle\Controller\Metrics;


use AppBundle\Form\Metrics\MetricsReportType;
use AppBundle\Model\Metrics\ModelMetrics;
use AppBundle\Model\VO\ModelVO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Lavoisier\Query;
use Lavoisier\Hydrators\EntriesHydrator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

use AppBundle\Entity\VO\VoRobotCertificate;

/**
 * Class MetricsController
 * @package AppBundle\Controller\Metrics
 * @Route("/metrics")
 */
class MetricsController extends Controller
{

    /**
     * @var $user User
     */
    public $user = null;


    /**
     * @var $container \Symfony\Component\DependencyInjection\Container
     */
    protected $container;


    /**
     * @Route("/metricsReports", name="metricsReports")
     * @Route("/")
     * form to get metrics report list
     */
    public function metricsReportsAction()
    {

        $this->user = $this->getUser();

        //build the form with MetricsReportType
        $metricsReportForm = $this->createForm(MetricsReportType::class);

        return $this->render(":Metrics:metricsReports.html.twig",
            array("metricsReportsForm" => $metricsReportForm->createView(),
                "isSuUser" => $this->user->isSuUser()));

    }

    /**
     * @param Request $request
     * redirect to correct reportsList controller
     * @Route("/redirectToReportsList", name="redirectToReportsList")
     */
    public function redirectToReportsListAction(Request $request)
    {

        $metricsReportForm = $this->createForm(MetricsReportType::class);

        //get the form data POSTED
        $metricsReportForm->handleRequest($request);

        //check the form
        if ($metricsReportForm->isSubmitted()) {

            //get entity and date from form
            $entity = $metricsReportForm->get('entity')->getData();
            $date = null;
            $date_start = null;
            $date_end = null;

            if ($entity != "voActivities") {
                $date = $metricsReportForm->get('begin_date')->getData();

                //format date
                $date = $date->format('Y-m-d H:i:s');

            } else {
                $date_start = $metricsReportForm->get('start_date')->getData();
                $date_end = $metricsReportForm->get('end_date')->getData();

                //format date
                $date_start = $date_start->format('Y-m');
                $date_end = $date_end->format('Y-m');

            }


            //get the url to redirect
            $url = "";
            if ($entity == "ca" || $entity == "vo") {
                $url = "metricsReportsList";
            } else if ($entity == "discipline") {
                $url = "disciplineMetricsReports";
            } else if ($entity == "national") {
                $url = "internationalMetricsReportsTable";
            } else if ($entity == "voActivities") {
                $url = "voActivitiesReportsTable";
                //redirect to correct controller method
                return $this->redirect($this->generateUrl($url, array("entity" => $entity, "start" => $date_start, 'end' => $date_end)));

            }

            //redirect to correct controller method
            return $this->redirect($this->generateUrl($url,
                array("entity" => $entity, "date" => $date)));

            //redirect to form page if form is incorrect
        } else {
            $this->addFlash("danger", "Error on submitting form... Please <a href='" . $this->generateUrl("contact") . "'>contact us</a>");

            return $this->redirect($this->generateUrl("metricsReports"));
        }

    }

    /**
     * user metrics per VO or CA
     * @Route("/metricsReportsList/{entity}/{date}", name="metricsReportsList")
     */
    public function metricsReportsListAction($entity, $date)
    {

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //construct a tab of dates (months)

        $date = strtotime($date);

        $dateCurrent = strtotime('-1 year', $date);
        $dateCurrent = date('Y-m', strtotime('-6 month', $dateCurrent));
        $tabdates = array(0 => $dateCurrent);

        $dateCurrent = date('Y-m', strtotime('-1 year', $date));
        $tabdates[] = $dateCurrent;

        $dateCurrent = date('Y-m', strtotime('-6 month', $date));
        $tabdates[] = $dateCurrent;

        $dateCurrent = date('Y-m', strtotime('+0 day', $date));
        $tabdates[] = $dateCurrent;

        ///////////////////////////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //begin to compose the parameters for lavoisier call
        $array_POST = array();

        if ($entity == 'ca') {
            $array_POST = array("option" => "CA");
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //get history
        $lavoisierUrl = $this->container->getParameter("lavoisierUrl");


        try {

            //compose dates parameters
            $nb = 1;
            foreach ($tabdates as $dates) {
                $array_POST["date" . $nb] = $dates . '-01';
                $nb++;
            }

            ///////////////////////////////////////////////////////////////////////////////////////////////////
            //call to lavoisier view
            $lquery = new Query($lavoisierUrl, 'VouserAccountingHistory', 'lavoisier');

            $lquery->setMethod('POST');
            $lquery->setPostFields($array_POST);
            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);

            $array = $lquery->execute();
            $entityMetricsraw = $array->getArrayCopy();

            ///////////////////////////////////////////////////////////////////////////////////////////////////


            ///////////////////////////////////////////////////////////////////////////////////////////////////
            //recompose result
            $entityMetrics = array();

            foreach ($entityMetricsraw as $keyDate => $entities) {
                $entityMetrics["total"][$tabdates[$keyDate]] = 0;
                if ($entities) {

                    foreach ($entities as $usersEntity) {
                        $entityMetrics["entity"][$usersEntity[strtoupper($entity)]][$tabdates[$keyDate]] = $usersEntity["nbUsers"];
                        $entityMetrics["total"][$tabdates[$keyDate]] += $usersEntity["nbUsers"];

                    }
                }
            }

            ///////////////////////////////////////////////////////////////////////////////////////////////////


            ///////////////////////////////////////////////////////////////////////////////////////////////////
            // compose diff
            foreach ($entityMetrics["entity"] as $vo => $nbstart) {
                $oldValue = null;
                foreach ($tabdates as $month => $ddate) {
                    if ($vo != "total") {
                        if (isset($entityMetrics["entity"][$vo][$ddate])) {
                            $entityMetrics["nbUsers"][$vo][$ddate] = $entityMetrics["entity"][$vo][$ddate];
                        } else {
                            $entityMetrics["nbUsers"][$vo][$ddate] = 0;
                        }


                        if (isset($oldValue)) {
                            $styleNbUsers = "color:darkred;font-size:smaller;padding-right:20px";
                            $diffNbUsers = $entityMetrics["nbUsers"][$vo][$ddate] - $oldValue;
                            if ($diffNbUsers >= 0) {
                                $styleNbUsers = "color:darkgreen;font-size:smaller;padding-right:20px";
                                $diffNbUsers = "+" . $diffNbUsers;
                            }
                        } else {
                            $diffNbUsers = "N.A";
                            $styleNbUsers = "color:gray;font-size:smaller;padding-right:20px";
                        }
                        $entityMetrics["nbUsersStyle"][$vo][$ddate] = $styleNbUsers;
                        $entityMetrics["nbUsersDiff"][$vo][$ddate] = $diffNbUsers;


                        $oldValue = $entityMetrics["nbUsers"][$vo][$ddate];
                    }

                }
            }
            ///////////////////////////////////////////////////////////////////////////////////////////////////

            ///////////////////////////////////////////////////////////////////////////////////////////////////
            //construction of tab headers
            $styleColLeft = "color:darkred;font-size:smaller;padding-right:20px";
            $diffColLeft = $entityMetrics["total"][$tabdates[1]] - $entityMetrics["total"][$tabdates[0]];
            if ($diffColLeft >= 0) {
                $styleColLeft = "color:darkgreen;font-size:smaller;padding-right:20px";
                $diffColLeft = "+" . $diffColLeft;
            }


            $styleColMid = "color:darkred;font-size:smaller;padding-right:20px";
            $diffColMid = $entityMetrics["total"][$tabdates[2]] - $entityMetrics["total"][$tabdates[1]];
            if ($diffColMid >= 0) {
                $styleColMid = "color:darkgreen;font-size:smaller;padding-right:20px";
                $diffColMid = "+" . $diffColMid;
            }

            $styleColRight = "color:darkred;font-size:smaller;padding-right:20px";
            $diffColRight = $entityMetrics["total"][$tabdates[3]] - $entityMetrics["total"][$tabdates[2]];
            if ($diffColRight >= 0) {
                $styleColRight = "color:darkgreen;font-size:smaller;padding-right:20px";
                $diffColRight = "+" . $diffColRight;
            }
            ///////////////////////////////////////////////////////////////////////////////////////////////////

            ///////////////////////////////////////////////////////////////////////////////////////////////////
            // construction of data used in chart and for csv export
            $csv = "";
            $csvLimited = $csv;

            foreach ($tabdates as $month => $ddate) {
                $csv .= "\n" . $ddate;
                $csvLimited .= "\n" . $ddate;
                $cpt = 10;
                foreach ($entityMetrics["entity"] as $usersEntity => $nbstart) {

                    if (isset($entityMetrics["entity"][$usersEntity][$ddate])) {
                        $csv .= "," . $usersEntity . "," . $entityMetrics["entity"][$usersEntity][$ddate];
                        if ($cpt > 0)
                            $csvLimited .= "," . $usersEntity . "," . $entityMetrics["entity"][$usersEntity][$ddate];
                    } else {
                        $csv .= "," . $usersEntity . ",000000";
                        if ($cpt > 0)
                            $csvLimited .= "," . $usersEntity . ",000000";

                    }
                    $cpt--;

                }

                if (isset($entityMetrics["total"][$ddate])) {
                    $csv .= "," . "total" . "," . $entityMetrics["total"][$ddate];
                    $csvLimited .= "," . "total" . "," . $entityMetrics["total"][$ddate];
                }

            }


            $csvLimited = trim($csvLimited, "\n");
            ///////////////////////////////////////////////////////////////////////////////////////////////////


            ///////////////////////////////////////////////////////////////////////////////////////////////////
            //construction of breadcrumb
            $breadCrumbs = array(
                "basePage" => array("title" => "EGI Reports", "route" => "metricsReports"),
                "currentPage" => array(
                    "title" => "Users Numbers by $entity",
                    "route" => "metricsReportsList",
                    "entity" => $entity,
                    "date" => $date,
                ));
            ///////////////////////////////////////////////////////////////////////////////////////////////////


            return $this->render(":Metrics:metricsReportsList.html.twig", array(
                "breadcrumbs" => $breadCrumbs,
                "tabdates" => $tabdates,
                "csvExport" => $csv,
                "csv" => $csvLimited,
                "entityMetrics" => $entityMetrics,
                "diffColLeft" => $diffColLeft,
                "styleColLeft" => $styleColLeft,
                "styleColMid" => $styleColMid,
                "diffColMid" => $diffColMid,
                "diffColRight" => $diffColRight,
                "styleColRight" => $styleColRight,
                "entity" => $entity));

            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getCode() . " " . $e;

            $this->addFlash("danger", $error);

            return $this->redirect($this->generateUrl("metricsReports"));

        }
        //@codeCoverageIgnoreEnd
    }

    /**
     * user metrics per disciplines
     * @Route("/disciplineMetricsReports/{entity}/{date}/{id}", name="disciplineMetricsReports")
     */
    public function disciplineMetricsReportsAction($entity, $date, $id = null)
    {
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //start to compose parameters for lavoisier call
        $POST_ARRAY = array();

        if ($id != null) {
            $POST_ARRAY["DisciplineId"] = $id;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////


        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //construct a tab of dates
        $date = strtotime($date);

        $dateCurrent = strtotime('-1 year', $date);
        $dateCurrent = date('Y-m', strtotime('-6 month', $dateCurrent));
        $tabdates = array(0 => $dateCurrent);

        $dateCurrent = date('Y-m', strtotime('-1 year', $date));
        $tabdates[] = $dateCurrent;

        $dateCurrent = date('Y-m', strtotime('-6 month', $date));
        $tabdates[] = $dateCurrent;

        $dateCurrent = date('Y-m', strtotime('+0 day', $date));
        $tabdates[] = $dateCurrent;
        ///////////////////////////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //get history by lavoisier call

        try {
            $voMetricsraw = array();

            $lavoisierUrl = $this->container->getParameter("lavoisierUrl");

            foreach ($tabdates as $dates) {
                $query = new \Lavoisier\Query(
                    $lavoisierUrl,
                    'VoDisciplinesTree_Metrics',
                    'lavoisier',
                    'json'
                );
                $query->setMethod('POST');
                $query->setPostFields(array_merge(array("date" => $dates . "-01"), $POST_ARRAY));

                $xml = $query->execute();
                $array = json_decode($xml, true);
                ///////////////////////////////////////////////////////////////////////////////////////////////////
                //compose the result
                foreach ($array["disciplines"] as $discipline) {
                    if (array_key_exists('discipline', $discipline)) {
                        $voMetricsraw[$discipline["id"]]["label"] = str_replace(",", "", $discipline["value"]);
                        $voMetricsraw[$discipline["id"]]["level"] = $discipline["level"];
                        $voMetricsraw[$discipline["id"]]["dates"][$dates] = $discipline["TotalSumNbUsers"];
                    }
                }
                ///////////////////////////////////////////////////////////////////////////////////////////////////

            }
            ///////////////////////////////////////////////////////////////////////////////////////////////////
            // compose diff
            foreach ($voMetricsraw as $dicsiplineId => $discipline) {
                $oldValue = null;
                if (isset($discipline["dates"])) {
                    foreach ($discipline["dates"] as $date => $nbUsers) {
                        if (isset($oldValue)) {
                            $style2 = "color:darkred;font-size:smaller;padding-right:20px";
                            $diff = $nbUsers - $oldValue;

                            if ($diff >= 0) {
                                $style2 = "color:darkgreen;font-size:smaller;padding-right:20px";
                                $diff = "+" . $diff;
                            }


                        } else {

                            $diff = "N.A";
                            $style2 = "color:gray;font-size:smaller;padding-right:20px";
                        }

                        $voMetricsraw["nbUsersStyle"][$discipline['label']][$date] = $style2;
                        $voMetricsraw["nbUsersDiff"][$discipline['label']][$date] = $diff;


                        $oldValue = $nbUsers;


                    }

                }
            }

            ///////////////////////////////////////////////////////////////////////////////////////////////////


            ///////////////////////////////////////////////////////////////////////////////////////////////////
            // construction of data used in chart and for csv export
            $csv = "";
            $cpt = 30;
            foreach ($tabdates as $month => $ddate) {
                $csv .= "\n" . $ddate;
                foreach ($voMetricsraw as $dicsiplineId => $discipline) {
                    if (isset($discipline["label"])) {
                        $csv .= "," . $discipline["label"];
                        if (isset($discipline["dates"][$ddate])) {
                            $csv .= "," . $discipline["dates"][$ddate];

                        }

                        $cpt--;
                    }
                }

            }

            $csv = trim($csv, "\n");

            $tabArea = array();
            foreach ($voMetricsraw as $dicsiplineId => $discipline) {
                if (isset($discipline["label"])) {
                    if (isset($discipline["dates"])) {
                        foreach ($discipline["dates"] as $date => $nb) {
                            $tabArea[$discipline["label"]][$date] = $nb;
                        }


                    }
                }
            }

            ///////////////////////////////////////////////////////////////////////////////////////////////////
            //construct breadcrumb
            $breadCrumbs = array(
                "basePage" => array("title" => "EGI Reports", "route" => "metricsReports"),
                "currentPage" => array(
                    "title" => "User metrics per Disciplines",
                    "route" => "disciplineMetricsReports",
                    "entity" => $entity,
                    "date" => $date));
            ///////////////////////////////////////////////////////////////////////////////////////////////////


            return $this->render(":Metrics:DisciplinesMetricsReportsList.html.twig", array(
                "breadcrumbs" => $breadCrumbs,
                "tabdates" => $tabdates,
                "csv" => $csv,
                "tabArea" => $tabArea,
                "voMetricsraw" => $voMetricsraw,
                "entity" => $entity));

            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getCode() . " " . $e;

            $this->addFlash("danger", $error);

            return $this->redirect($this->generateUrl("metricsReports"));

        }
        //@codeCoverageIgnoreEnd
    }


    /**
     * @Route("/internationalMetricsReportsTable/{entity}/{date}", name="internationalMetricsReportsTable")
     */
    public function internationalMetricsReportsTableAction($entity, $date)
    {
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //construct a tab of dates
        $date = strtotime($date);

        $dateCurrent = strtotime('-1 year', $date);
        $dateCurrent = date('Y-m-d', strtotime('-6 month', $dateCurrent));
        $tabdates = array(0 => $dateCurrent);

        $dateCurrent = date('Y-m-d', strtotime('-1 year', $date));
        $tabdates[] = $dateCurrent;

        $dateCurrent = date('Y-m-d', strtotime('-6 month', $date));
        $tabdates[] = $dateCurrent;

        $dateCurrent = date('Y-m-d', strtotime('+0 day', $date));
        $tabdates[] = $dateCurrent;
        ///////////////////////////////////////////////////////////////////////////////////////////////////

        $nb = 0;


        $voMetricsraw = array();
        $modelVO = new ModelVO($this->container, null);
        foreach ($tabdates as $date) {
            if ($nb < 3)
                $voMetricsraw[$date . " / " . $tabdates[$nb + 1]] = $modelVO->getCountVo($tabdates[$nb + 1], $date);
            $nb++;
        }


        $breadCrumbs = array(
            "basePage" => array("title" => "EGI Reports", "route" => "metricsReports"),
            "currentPage" => array(
                "title" => "VO creations",
                "route" => "internationalMetricsReportsTable",
                "entity" => $entity,
                "date" => $date));


        return $this->render(":Metrics:internationalMetricsReportsTable.html.twig", array(
            "breadcrumbs" => $breadCrumbs,
            "tabdates" => $tabdates,
            "voMetricsraw" => $voMetricsraw,
            "entity" => $entity));
    }


    /**
     * @Route("/voActivitiesReportsTable/{entity}/{start}/{end}", name="voActivitiesReportsTable")
     */
    public function voActivitiesReportsTableAction($entity, $start, $end)
    {

        try {

            $lavoisierUrl = $this->container->getParameter("lavoisierUrl");

            $entriesHydrator = new EntriesHydrator();

            $query = new \Lavoisier\Query($lavoisierUrl, 'VoActivities', 'lavoisier');
            $query->setMethod('POST');
            $query->setPostFields(array("dateStart" => $start, "dateEnd" => $end));
            $query->setHydrator($entriesHydrator);

            $result = $query->execute();

            $array = $result->getArrayCopy();

            $voActivitiesRaw = array();

            foreach ($array as $key => $val) {
                foreach ($val as $k => $v) {
                    if ($k == "numberStart") {
                        if (!is_numeric($val["numberStart"]) || !is_numeric($val["numberEnd"])) {
                            $diff = "N.A";
                            $diffCss = "color : red";

                        } else {
                            $calc = $val["numberEnd"] - $val["numberStart"];
                            $diff = $calc > 0 ? "+" . $calc : $calc;
                            $diffCss = ($calc > 0 ? "color:green" : ($calc == 0 ? "color: grey" : "color:red"));
                        }

                        $voActivitiesRaw[$key]["voUsersDiff"]["val"] = $diff;
                        $voActivitiesRaw[$key]["voUsersDiff"]["css"] = $diffCss;
                    }
                    $voActivitiesRaw[$key][$k] = $v;

                }

            }


            $breadCrumbs = array(
                "basePage" => array("title" => "EGI Reports", "route" => "metricsReports"),
                "currentPage" => array(
                    "title" => "VO activities",
                    "route" => "voActivitiesReportsTable",
                    "entity" => $entity,
                    "start" => $start,
                    "end" => $end));

            return $this->render(":Metrics:voActivitiesReportsTable.html.twig", array(
                "breadcrumbs" => $breadCrumbs,
                "start" => $start,
                "end" => $end,
                "voActivititiesRaw" => $voActivitiesRaw));


            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getCode() . " " . $e;

            $this->addFlash("danger", $error);

            return $this->redirect($this->generateUrl("metricsReports"));

        }
        //@codeCoverageIgnoreEnd
    }


    /**
     * get details of created vo for international metrics reports
     * @param Request $request
     * @Route("/voCreationDetailsAjax", name="voCreationDetailsAjax")
     */
    public function voCreationDetailsAjaxAction(Request $request)
    {
        $dateStart = $request->get("date_start");
        $dateEnd = $request->get("date_end");

        $modelVO = new ModelVO($this->container, null);

        $tabVoDetails = $modelVO->getVoCreated($dateStart, $dateEnd);

        return $this->render(":Metrics/templates:template_tab_voCreationDetails.html.twig",
            array("tabVoDetails" => $tabVoDetails, "dateStart" => $dateStart, "dateEnd" => $dateEnd));

    }


    /**
     * Metrics History for a VO in one year
     * @Route("/oneYearVoCaMetricsReports/{entity}/{name}/{date}", name="oneYearVoCaMetricsReports", requirements={"name"=".+"})
     */
    public function oneYearVoCaMetricsReportsAction($entity, $name, $date)
    {


        $name = urldecode($name);
        $dateStart = date('Y-m', strtotime('-1 year', strtotime($date)));

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        // start to compose lavoisier parameters
        $array_POST = array(
            "date_end" => $date,
            "date_start" => $dateStart);
        ///////////////////////////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        // get the lavoisier view name
        $lavoisierView = 'VoUsersMetrics';

        if ($entity == "vo") {
            $array_POST["volist"] = $name;
        } else if ($entity == "ca") {
            $lavoisierView = 'CaUsersMetrics';
            $array_POST["calist"] = $name;
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////

        try {
            ///////////////////////////////////////////////////////////////////////////////////////////////////
            // call lavoisier view
            $lavoisierUrl = $this->container->getParameter("lavoisierUrl");

            $lquery = new \Lavoisier\Query($lavoisierUrl, $lavoisierView, 'lavoisier');

            $lquery->setMethod('POST');
            $lquery->setPostFields($array_POST);

            $xml = $lquery->execute();
            ///////////////////////////////////////////////////////////////////////////////////////////////////


            ///////////////////////////////////////////////////////////////////////////////////////////////////
            // recompose result
            $obj = simplexml_load_string($xml); // Parse XML
            $arrayResult = array();
            $voMetricsRaw = array();

            if ($entity == "vo") {
                $voMetricsraw = json_decode(json_encode($obj), true);

                if (isset ($voMetricsraw["row"])) {
                    $voMetricsraw = $voMetricsraw["row"];
                    foreach ($voMetricsraw as $vo) {
                        if (isset($vo["yearMonth"])) {
                            $voMetricsRaw[$vo["yearMonth"]] = $vo;
                        }
                    }
                } else {
                    $tabDate = array();
                    for ($i = 0; $i <= 12; $i++) {
                        $tabDate[$i] = date('Y-m', strtotime('+' . $i . ' month', strtotime($dateStart)));
                    }
                    foreach ($tabDate as $date) {
                        $voMetricsRaw[$date] = array(
                            "yearMonth" => $date,
                            "vo" => $name,
                            "total" => "0");

                    }
                }
                ksort($voMetricsRaw);

                ///////////////////////////////////////////////////////////////////////////////////////////////////
                // compose diff tab
                $diff = "N.A.";
                $value1 = 0;
                $value2 = "N.A.";

                $tabdiff = array();
                foreach ($voMetricsRaw as $key => $vo) {

                    if (isset($vo["total"])) {
                        $nbuser = $vo["total"];
                    } else {
                        $nbuser = "N.A.";
                    }

                    if (isset($nbuser) && $nbuser != "N.A.") {
                        $value1 = $nbuser;

                        if (isset($value2) && $value2 != "N.A.") {
                            $diff = $value1 - $value2;

                        }
                    } else {
                        $diff = "N.A.";

                    }

                    if ($diff === "N.A.") {
                        $styleColor = "#08000";
                    } elseif ($diff >= 0) {
                        $diff = "+" . $diff;
                        $styleColor = "darkgreen";
                    } else {
                        $styleColor = "darkred";
                    }


                    $tabdiff[$key]["diff"] = $diff;
                    $tabdiff[$key]["style"] = $styleColor;


                    $value2 = $value1;

                }
                ///////////////////////////////////////////////////////////////////////////////////////////////////

                ///////////////////////////////////////////////////////////////////////////////////////////////////
                // compose csv for chart
                $csv = "";
                foreach ($voMetricsRaw as $key => $vo) {

                    if ($vo["total"] != "N.A.") {
                        $csv .= $vo["yearMonth"] . "," . $vo["total"] . '\n';
                    } else {
                        $csv .= $vo["yearMonth"] . ",00000" . '\n';
                    }

                }
                ///////////////////////////////////////////////////////////////////////////////////////////////////
                //Add tab to array result
                $arrayResult["tabVOCA"] = $voMetricsRaw;
                ///////////////////////////////////////////////////////////////////////////////////////////////////

            } else if ($entity == "ca") {

                ///////////////////////////////////////////////////////////////////////////////////////////////////
                // recompose result
                $obj = simplexml_load_string($xml); // Parse XML
                $caMetricsraw = json_decode(json_encode($obj), true)["row"];

                $tabCA = array();


                foreach ($caMetricsraw as $key => $ca) {
                    $tabCA[$ca['yearMonth']]["total"] = $ca["total"];
                }

                ksort($tabCA);

                ///////////////////////////////////////////////////////////////////////////////////////////////////
                // compose diff tab
                $diff = "N.A.";
                $value1 = 0;
                $value2 = "N.A.";
                $tabdiff = array();
                foreach ($tabCA as $key => $ca) {

                    if (isset($ca) && $ca != null) {
                        $nbuser = $ca["total"];
                    } else {
                        $nbuser = "N.A.";
                    }

                    if (isset($nbuser) && $nbuser != "N.A.") {
                        $value1 = $nbuser;
                        if (isset($value2) && $value2 != "N.A.") {
                            $diff = $value1 - $value2;

                        }
                    } else {
                        $diff = "N.A.";

                    }


                    if ($diff === "N.A.") {
                        $styleColor = "#08000";
                    } elseif ($diff >= 0) {
                        $diff = "+" . $diff;
                        $styleColor = "darkgreen";
                    } else {
                        $styleColor = "darkred";
                    }

                    $tabdiff[$key]["diff"] = $diff;
                    $tabdiff[$key]["style"] = $styleColor;

                    $value2 = $value1;


                }

                ///////////////////////////////////////////////////////////////////////////////////////////////////

                ///////////////////////////////////////////////////////////////////////////////////////////////////
                // compose csv for chart
                $csv = "";
                foreach ($caMetricsraw as $key => $ca) {

                    if ($ca["total"] != "N.A.") {
                        $csv .= $ca["yearMonth"] . "," . $ca["total"] . '\n';
                    } else {
                        $csv .= $ca["yearMonth"] . ",00000" . '\n';
                    }

                }
                ///////////////////////////////////////////////////////////////////////////////////////////////////
                //Add tab to array result
                $arrayResult["tabVOCA"] = $tabCA;
                ///////////////////////////////////////////////////////////////////////////////////////////////////

            }


            ///////////////////////////////////////////////////////////////////////////////////////////////////
            //construction of breadcrumb
            $breadCrumbs = array(
                "basePage" => array(
                    "title" => "EGI Reports",
                    "route" => "metricsReports"),
                "previsousPage" => array(
                    "title" => "User Number per " . $entity,
                    "route" => "metricsReportsList",
                    "entity" => $entity,
                    "date" => $date),
                "currentPage" => array(
                    "title" => "User Number per " . $entity . " : History for " . $name,
                    "route" => "oneYearVoCaMetricsReports",
                    "name" => $name,
                    "entity" => $entity,
                    "date" => $date,
                ));
            ///////////////////////////////////////////////////////////////////////////////////////////////////


            ///////////////////////////////////////////////////////////////////////////////////////////////////
            // others info to array result
            $arrayResult["csv"] = $csv;
            $arrayResult["tabDiff"] = $tabdiff;
            $arrayResult["breadcrumbs"] = $breadCrumbs;
            $arrayResult["name"] = $name;
            $arrayResult["entity"] = $entity;

            ///////////////////////////////////////////////////////////////////////////////////////////////////


            return $this->render(":Metrics:oneYearVoCaMetricsReports.html.twig", $arrayResult);

            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getCode() . " " . $e;

            $this->addFlash("danger", $error);

            return $this->redirect($this->generateUrl("metricsReports"));

        }
        //@codeCoverageIgnoreEnd
    }

    /**
     * get history for a discipline
     * @Route("/oneYearDisciplinesMetricsReports/{entity}/{name}/{date}", name="oneYearDisciplinesMetricsReports")
     */
    public function oneYearDisciplinesMetricsReportsAction($entity, $name, $date)
    {

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        // compose a tab of dates
        $dateCurrent = strtotime($date);

        $tabdates = array(0 => date('Y-m', $dateCurrent));
        $cpt = 1;
        while ($cpt < 12) {
            $date = date('Y-m', strtotime("-$cpt month", $dateCurrent));
            $tabdates[] = $date;
            $cpt++;
        }
        sort($tabdates);
        ///////////////////////////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //compose history tab
        $tabdiscipline = array();

        $modelMetrics = new ModelMetrics($this->container);
        $tabUsers = $modelMetrics->getNbUsersbyDiscipline($name)[0];


        foreach ($tabUsers as $key2 => $nbusers) {
            $tabdiscipline["discipline"] = $key2;
            foreach ($nbusers as $key1 => $history) {


                if ($history["u_month"] > 9)
                    $yearmonth = $history["u_year"] . "-" . $history["u_month"];
                else
                    $yearmonth = $history["u_year"] . "-0" . $history["u_month"];

                if (in_array($yearmonth, $tabdates)) {
                    if (isset($tabdiscipline["dates"][$yearmonth]))
                        $tabdiscipline["dates"][$yearmonth] += $history["nbtotal"];
                    else
                        $tabdiscipline["dates"][$yearmonth] = $history["nbtotal"];
                }
            }
        }
        ksort($tabdiscipline["dates"]);
        ///////////////////////////////////////////////////////////////////////////////////////////////////


        ///////////////////////////////////////////////////////////////////////////////////////////////////
        // construct tab of diff
        $tabdiff = array();

        $diff = "N.A.";
        $value1 = 0;
        $value2 = "N.A.";

        foreach ($tabdates as $date) {

            if (isset($tabdiscipline["dates"][$date])) {
                $nbuser = $tabdiscipline["dates"][$date];
            } else {
                $nbuser = "N.A.";
            }
            if (isset($nbuser) && $nbuser != "N.A.") {
                $value1 = $nbuser;

                if (isset($value2) && $value2 != "N.A.") {
                    $diff = $value1 - $value2;

                }
            } else {
                $diff = "N.A.";

            }

            if ($diff === "N.A.") {
                $styleColor = "#08000";
            } elseif ($diff >= 0) {
                $diff = "+" . $diff;
                $styleColor = "darkgreen";
            } else {
                $styleColor = "darkred";
            }

            $tabdiff[$date][$tabdiscipline['discipline']]["diff"] = $diff;
            $tabdiff[$date][$tabdiscipline['discipline']]["style"] = $styleColor;


            $value2 = $value1;
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        // csv for export and chart
        $csv = "";
        foreach ($tabdates as $date) {
            if (isset($tabdiscipline["dates"][$date]) && $tabdiscipline["dates"][$date] != "N.A.") {
                $csv .= $date . "," . $tabdiscipline["dates"][$date] . '\n';

            } else {
                $csv .= $date . ",00000" . '\n';

            }

        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //construction of breadcrumb
        $breadCrumbs = array(
            "basePage" => array("title" => "EGI Reports", "route" => "metricsReports"),
            "previsousPage" => array(
                "title" => "User metrics per Disciplines",
                "route" => "disciplineMetricsReports",
                "entity" => $entity,
                "date" => $date),
            "currentPage" => array(
                "title" => "User Number per discipline : History for " . $name,
                "route" => "oneYearDisciplinesMetricsReports",
                "entity" => $entity,
                "name" => $name,
                "date" => $date,
            )
        );


        ///////////////////////////////////////////////////////////////////////////////////////////////////


        return $this->render(":Metrics:oneYearsDisciplineMetricsReports.html.twig",
            array("tabdiscipline" => $tabdiscipline,
                "tabdates" => $tabdates,
                "breadcrumbs" => $breadCrumbs,
                "csv" => $csv,
                "name" => $name,
                "tabdiff" => $tabdiff));
    }


    /**
     * @Route("/a/usersSummary/{disciplineId}", defaults={"disciplineId" = null}, name="usersSummary")
     */
    public function usersSummaryAction($disciplineId)
    {

        $lavoisierUrl = $this->container->getParameter("lavoisierUrl");

        try {

            //get data for global tab
            $query = new Query($lavoisierUrl, 'VoDisciplinesTree_Metrics', 'lavoisier', 'json');

            //if there is a discipline id
            // add it to the lavoisier query parameters
            if ($disciplineId != null) {
                $query->setMethod("POST");
                $query->setPostFields(array("DisciplineId" => $disciplineId));

                //get csv url with parameter
                $base_query = new Query($lavoisierUrl, 'VoDisciplinesTree_Metrics?DisciplineId=' . $disciplineId);


            } else {
                //get csv url without parameter
                $base_query = new Query($lavoisierUrl, 'VoDisciplinesTree_Metrics');

            }

            //compose results
            $tabUsers = json_decode($query->execute(), true);
            $base_url = $base_query->dump();


            //get array for pie charts
            $chartNbVo = array("discipline" => "number of VO");
            $chartNbUsers = array("discipline" => "number of Users");

            $title = $tabUsers["disciplineName"];

            foreach ($tabUsers["disciplines"] as $key2 => $item) {
                if (isset($item["NbVo"])) {
                    $chartNbVo[$item["value"]] = (int)$item["NbVo"];
                    $chartNbUsers[$item["value"]] = (int)$item["TotalSumNbUsers"];
                }

            }

            return $this->render(":Metrics:usersSummary.html.twig",
                array("tabusers" => $tabUsers,
                    "charttitle" => $title,
                    "chartNbVo" => $chartNbVo,
                    "chartNbUsers" => $chartNbUsers,
                    "baseUrl" => str_replace("accept=xml", "accept=csv", $base_url["url"])));

            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getCode() . " " . $e;

            $this->addFlash("danger", $error);

            return $this->render(":Metrics:usersSummary.html.twig");

        }
        //@codeCoverageIgnoreEnd
    }

    /**
     * get embed part with metrics dump
     */
    public function metricsDumpAction()
    {
        return $this->render(":Metrics/templates:template_embed_metricsDump.html.Twig");
    }

    /**
     * generate route to download metrics dump
     * @Route("/downloadVoUsers/{type}", name="downloadVoUsers")
     */
    public function downloadVoUsersAction($type = null)
    {


        $lavoisierUrl = $this->container->getParameter("lavoisierUrl");
        $base_urlLavoisier = "http://" . $lavoisierUrl . ":8080/lavoisier";


        if ($type == "history") {
            $BaseUrl = $base_urlLavoisier . "/OPSCORE_vo_users_history?accept=csv";
            $fileName = "vo_users_history.csv";

        } else {
            $BaseUrl = $base_urlLavoisier . "/OPSCORE_vo_users_raw?accept=csv";
            $fileName = "vo_users_raw.csv";

        }

        $handle = fopen($BaseUrl, "rb");
        $contents = stream_get_contents($handle);
        fclose($handle);

        $response = new Response();

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $fileName . '');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Connection', 'close');
        $response->setContent($contents);


        return $response;
    }


    /**
     * get a csv file to download
     * @Route("/toCsv", name="toCsv")
     */
    public function toCsvAction(Request $request)
    {
        $csv = $request->get("csv");
        $response = new Response();

        if ($csv != null) {
            $response->headers->set('Content-Type', 'application/csv; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment; filename=UsersMetrics.csv');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Connection', 'close');
            $response->setContent($csv);
        } else {
            $response->setContent("Error, no csv file...");

        }
        return $response;
    }


    /**
     * @Route("/rbCert", name="rbCert")
     */
    public function robotCertificateAction(Request $request)
    {

        $modelVO = new ModelVO($this->container, null);

        $arrayReturn = array();

        $lavoisierUrl = $this->container->getParameter("lavoisierUrl");


        try {
            $lquery = new Query($lavoisierUrl, 'OPSCORE_vo_users_robots', 'lavoisier');

            $addRbCertForm = null;


            $result = $lquery->execute();

            $listrbCert = json_decode(json_encode(simplexml_load_string($result)), true);

            $arrayRbCert = array();
            if (array_key_exists('row', $listrbCert)) {
                foreach ($listrbCert["row"] as $key => $rbCert) {

                    $temp_array = $modelVO->getVoRobot($rbCert["VO"], $rbCert["CERTDN"]);
                    if ($temp_array != null && count($temp_array) != 0) {
                        $arrayRbCert[] = array('id' => $temp_array["id"],
                            'vo_name' => $rbCert["VO"],
                            'service_name' => $temp_array["service_name"],
                            'service_url' => $temp_array["service_url"],
                            'email' => $rbCert["EMAIL"],
                            'robot_dn' => $rbCert["CERTDN"],
                            'use_sub_proxies' => $temp_array["use_sub_proxies"],
                            'validation_date' => $temp_array["validation_date"]);
                    } else {
                        $arrayRbCert[] = array('id' => null,
                            'vo_name' => $rbCert["VO"],
                            'service_name' => null,
                            'service_url' => null,
                            'email' => $rbCert["EMAIL"],
                            'robot_dn' => $rbCert["CERTDN"],
                            'use_sub_proxies' => 0,
                            'validation_date' => null);
                    }
                }
            }


            if ($this->getUser()->isSuUser()) {
                $arrayReturn["isSuUser"] = true;
                try {
                    $arrayReturn["voList"] = $modelVO->findAllVONames();
                } catch (\Exception $e) {
                    $arrayReturn["error"] = $e->getCode() . " - " . $e->getMessage();
                }
                } else {


                $arrayReturn["isSuUser"] = false;
                try {
                    $arrayReturn["voList"] = $this->getUser()->getMyVo();
                } catch (\Exception $e) {
                    $message = "You are not related to any VO. Consequently you are not authorized to access to this URL.";
                    return $this->render("@Twig/Exception/errorAuthenticationFailed.html.twig", array(
                            "message" => $message)
                    );


                }
            }

        //@codeCoverageIgnoreStart
        } catch (\Exception $e) {

            $this->addFlash("danger", "Robot Certificate - Error on Lavoisier call - " . $e->getMessage());
            return $this->redirect($this->generateUrl("rbCert"));

        }
        //@codeCoverageIgnoreEnd

        $arrayReturn["rbList"] = array();

        if (isset($arrayReturn["voList"])) {
            foreach ($arrayRbCert as $rbCert) {

                if (in_array($rbCert["vo_name"], $arrayReturn["voList"])) {
                    $arrayReturn["rbList"][$rbCert["vo_name"]][$rbCert["robot_dn"]] = $rbCert;
                }
            }


            $voRbCert = new VoRobotCertificate();

            $addRbCertForm = $this->createForm('AppBundle\Form\VO\VoRobotCertificateType', $voRbCert, array("voList" => $arrayReturn["voList"]));

            $arrayReturn["rbCertForm"] = $addRbCertForm->createView();
        }


        if ($request->isMethod("POST")) {
            $addRbCertForm->handleRequest($request);

            $message = "The new robot certificate had been saved successfully !";


            if ($addRbCertForm->isSubmitted()) {
                //find the vo by the serial
                /** @var  $vo \AppBundle\Entity\VO\Vo */
                $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => $addRbCertForm->get("vo_name")->getData()));

                // the "edit" form had been submited
                if ($addRbCertForm->get("id")->getData() != null || $addRbCertForm->get("id")->getData() != "") {
                    /** @var  $voRbCert \AppBundle\Entity\VO\VoRobotCertificate */
                    $voRbCert = $this->getDoctrine()->getRepository("AppBundle:VO\VoRobotCertificate")->findOneBy(array("id" => $addRbCertForm->get("id")->getData()));

                    //find the original robot certificate to modify
                    $voRbCert->setServiceName($addRbCertForm->get("service_name")->getData());
                    $voRbCert->setServiceUrl($addRbCertForm->get("service_url")->getData());
                    $voRbCert->setEmail($addRbCertForm->get("email")->getData());
                    $voRbCert->setRobotDn($addRbCertForm->get("robot_dn")->getData());
                    $voRbCert->setUseSubProxies($addRbCertForm->get("use_sub_proxies")->getData());
                    $voRbCert->setVoName($vo->getName());

                    try {
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($voRbCert);
                        $em->flush();
                        $em->refresh($voRbCert);

                    } catch (\Exception $e) {
                        $this->addFlash('danger', 'Create new Robot Certificate form - ' . $e->getMessage());
                    }

                    $this->addFlash('success', $message);

                }

            } else {
                $this->addFlash("danger", "The robot certificate form is not valid...");

            }


            return $this->redirect($this->generateUrl("rbCert"));
        }

        return $this->render(":Metrics:robotCertificate.html.twig", $arrayReturn);

    }

    /**
     * @Route("/removeRbCert", name="removeRbCert")
     */
    public function removeRbCertificateAction(Request $request)
    {

        $rbCertId = $request->get("rbCertId");

        if ($rbCertId != null || $rbCertId != "") {
            $voRbCert = $this->getDoctrine()->getRepository("AppBundle:VO\VoRobotCertificate")->findOneBy(array("id" => $rbCertId));

            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($voRbCert);
                $em->flush();
                $this->addFlash('success', "Robot Certificate has been removed successfully !");
            //@codeCoverageIgnoreStart
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Remove Robot Certificate form - ' . $e->getMessage());
                return new Response('Remove Robot Certificate form - ' . $e->getMessage(), 500);
            }
            //@codeCoverageIgnoreEnd
        } else {
            $this->addFlash("danger", "The robot certificate could not been remove... Please <a title='contact us' href='{{path('contact')}}'>Contact us</a>");
            return new Response("The robot certificate could not been remove... Please <a title='contact us' href='{{path('contact')}}'>Contact us</a>", 500);

        }

        return new Response("OK");
    }
}