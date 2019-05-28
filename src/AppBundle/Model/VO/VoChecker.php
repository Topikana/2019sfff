<?php

namespace AppBundle\Model\VO;

use Symfony\Component\DependencyInjection\ContainerInterface;

class VoChecker
{

    public $vo = array();
    public $voFields = array();


    const REGEXPURL = '^(http|https)://.*^';
    const REGEXPVONAME = '^([a-z0-9\-]{1,255}\.)+[a-z]{2,4}$^';

    /**
     * @var ContainerInterface from when we get the doctrine config
     */
    protected $container;

    /**
     * @var $lavoisier : the lavoisier url
     */
    protected $lavoisier;


    function __construct(ContainerInterface $container, $votab = NULL, $serial = NULL)
    {

        $this->container = $container;
        $this->lavoisierUrl = $this->container->getParameter("lavoisierurl");

        $this->em = $this->container->get("doctrine")->getManager();


        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();


        $qb->select("v, vh ,vs, vml")
            ->addSelect('(SELECT count(vu.dn)  FROM AppBundle:VO\VoUsers vu WHERE vu.vo = v.name and DATE_DIFF(CURRENT_DATE(),vu.last_update)<5) as Nbusers')
            ->from("AppBundle:VO\Vo", "v")
            ->innerJoin("AppBundle:VO\VoHeader", 'vh', "WITH", "vh.serial= v.serial")
            ->innerJoin("AppBundle:VO\VoStatus", 'vs', "WITH", "vs.id= vh.status_id")
            ->innerJoin("AppBundle:VO\VoMailingList", "vml", "WITH", "vml.id= v.mailing_list_id")
            ->where('vh.status_id= :status')
            ->setParameter("status", '2');


        if ($serial) {
            $qb->andWhere("v.serial=?", $serial);
        }
        if ($votab) {
            $qb->andWhere("v.name=?", $votab);
        }
        $query = $qb->getQuery();

        $this->vo = $query->getScalarResult();


    }


    // return the fields of the VO required and the associated regexp

    public function getVoFields($id)
    {

        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select("vml.admins_mailing_list, vh.aup, vh.description,vh.homepage_url,  vml.users_mailing_list")
            ->from("AppBundle:VO\VoHeader", "vh")
            ->innerJoin("AppBundle:VO\VoMailingList", "vml", "WITH", "vml.serial= vh.serial")
            ->where("vh.id = :id")
            ->setParameter("id", $id);

        $query = $qb->getQuery();

        //TODO pass to getSingleResult
//        $vofields = $query->getSingleResult();

        if (isset($query->getResult()[0]))
            $vofields = $query->getResult()[0];
        else $vofields=array(null);

        return $vofields;
    }

    public function returnStatus($vo, $field)
    {

        $status = "ok";
        $voField = "";


        if (isset($vo["vh_" . $field]) and !empty($vo["vh_" . $field])) {
            $voField = $vo["vh_" . $field];
        } elseif (isset($vo['vml_' . $field]) and !empty($vo['vml_' . $field])) {
            $voField = $vo['vml_' . $field];
        }

        if (!empty($voField)) {

            if ($field == "homepage_url" and !(preg_match(self::REGEXPURL, $voField))) {
                $status = "missing";
            }
        } else {
            $status = "missing";
        }


        return $status;
    }

    public function checkVoFields()
    {

        $tab_fields = null;
        $nb_fields = 0;
        foreach ($this->vo as $vo) {


            $voname = $vo['v_name'];
            $tab_fields[$voname]["serial"] = $vo['v_serial'];
            $nb_fields = 0;
            $score = 0;

            $Vofields = $this->getVoFields($vo['v_header_id']);

            foreach ($Vofields as $key => $field) {

                $nb_fields++;

                $status = $this->returnStatus($vo, $key);

                if ($status === 'ok') {
                    $score++;

                }

                $tab_fields[$voname][$key] = $status;
            }


            $modelVO = new ModelVO($this->container, $vo['v_serial']);

            $nbVoms = $modelVO->getVOMSList(null, true);

            if ($nbVoms) {
                $tab_fields[$voname]["VomsServer"] = $nbVoms;
                $score++;
            } else {
                $tab_fields[$voname]["VomsServer"] = "missing";
            }

            $tab_fields[$voname]["VomsUsers"] = "missing";

            if ($vo['Nbusers'] != '0') {
                $score++;
                $tab_fields[$voname]["VomsUsers"] = "ok";
            }

            $tab_fields[$voname]["score"] = round(($score / ($nb_fields + 2)) * 100);


        }



        $lquery = new \Lavoisier\Query($this->lavoisierUrl, 'urls_checker_all', 'lavoisier');
        $urlsList = simplexml_load_string($lquery->execute());

        foreach ($urlsList as $VoUrls) {
            $vo = (string)$VoUrls->VO;
            $code2 = (string)$VoUrls->HomepageUrl->code;

            if (isset($tab_fields[$vo]["homepage_url"]) and $tab_fields[$vo]["homepage_url"] <> "missing" and $tab_fields[$vo]["homepage_url"] <> "incorrect" and $code2 > "399") {
                $tab_fields[$vo]["homepage_url"] = "broken";
                $tab_fields[$vo]["score"] = abs(round($tab_fields[$vo]["score"] - (100 / ($nb_fields + 2))));
            }
        }

        return $tab_fields;

    }

    public function getLastReport()
    {
        $last_report = null;

        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select("max(vr.updated_at) as MaxTime, vr.serial")
            ->from("AppBundle:VO\VoReport", "vr")
            ->GroupBy("vr.serial");


        $query = $qb->getQuery();

        $reports = $query->getArrayResult();

        foreach ($reports as $report) {
            $last_report[$report["serial"]] = $report["MaxTime"];
        }

        return $last_report;
    }


}

