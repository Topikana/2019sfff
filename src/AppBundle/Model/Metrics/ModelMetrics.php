<?php

namespace AppBundle\Model\Metrics;

use Doctrine\ORM\Repository;
use Doctrine\ORM\EntityManager;


use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class VOModel
 * @package AppBundle\Model
 */
class ModelMetrics
{

    /**
     * @var ContainerInterface from when we get the doctrine config
     */
    protected $container;

    /**
     * @var $em EntityManager : the entity manager
     */
    protected $em;

    /**
     * @var $lavoisier : the lavoisier url
     */
    protected $lavoisier;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->em = $this->container->get("doctrine")->getManager();
        $this->lavoisierUrl = $this->container->getParameter("lavoisierurl");
    }


    public function getNbUsersbyDiscipline($discipline = NULL)
    {


        $tab_vos = $this->getVosByDiscipline($discipline);


        $countVo = array();
        $tabvonames = array();

        $listvo = array();
        $tabusers = array();
        $tabtotalusers = array();

        foreach ($tab_vos as $key => $vo) {

            if (!in_array($vo["name"], $tabvonames)) {
                array_push($tabvonames, $vo["name"]);
                $discipline = $vo["discipline_label"];
                $listvo[$discipline][] = $vo["name"];
                if (isset($countVo[$discipline]))
                    $countVo[$discipline]++;
                else
                    $countVo[$discipline] = 1;
            }

        }


        foreach ($listvo as $key => $value) {


            $results = self::getNbUsersMonth(NULL, NULL, $value);

            $tabusers[$key] = $results[0];
            $tabtotalusers[$key] = $results[1];

        }

        return (array($tabusers, $countVo, $tabtotalusers));
    }


    public function getVosByDiscipline($discipline = NULL, $oldDiscipline = false)
    {
        /** @var  $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();


        if ($oldDiscipline) {
            $qb->select('v.name,vd.discipline,vh.id')
                ->from("AppBundle:VO\Vo", "v")
                ->innerJoin("AppBundle:VO\VoHeader", "vh", "WITH", "v.header_id = vh.id")
                ->innerJoin("AppBundle:VO\VoDiscipline", "vd", "WITH", "vh.discipline_id = vd.id")
                ->where('vh.status_id= 2');

            if (isset($discipline)) {
                $qb->andWhere("vd.discipline='" . $discipline . "'");
            }

        } else {
            $qb->select('v.serial,v.name, d.discipline_label , vd.discipline_id')
                ->from("AppBundle:VO\VoDisciplines", "vd")
                ->leftJoin("AppBundle:VO\Vo", "v", "WITH", "vd.vo_id = v.serial")
                ->leftJoin("AppBundle:VO\Disciplines", "d", "WITH", "vd.discipline_id = d.discipline_id");

            if (isset($discipline)) {
                $qb->where("d.discipline_label='" . $discipline . "'");
            }
        }

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }

    public function getNbUsersMonth($month = NULL, $year = NULL, array $voList = NULL)
    {
        /** @var  $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select("vh")
            ->from("AppBundle:VO\VoUsersHistory", "vh")
            ->where("vh.u_year<>" . 2010)
            ->orderBy("vh.u_year desc,vh.u_month desc,vh.vo");

        if (isset($month) && isset($year)) {
            $qb->andWhere("vh.u_month='" . $month. "'");
            $qb->andWhere("vh.u_year='" . $year. "'");
        }

        $nbTotalbyVO = 0;

        if (isset($voList)) {
            $qb->andWhere("vh.vo IN ('" . implode("', '", $voList) . "')");
            $nbTotalbyVO = self::getUsersNumber($voList);

        }


        $query = $qb->getQuery();

        $arrayReturn = $query->getArrayResult();


        return (array($arrayReturn, $nbTotalbyVO));
    }


    public function getUsersNumber(array $voList, $datefixe = false)
    {

        /** @var  $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();
        $qb->select('COUNT(DISTINCT v.uservo), v.vo')
            ->from('AppBundle:VO\VoUsers', 'v')
            ->groupBy('v.vo');
        $qb->andWhere("v.vo IN ('" . implode("', '", $voList) . "')");

        if ($datefixe) {
            $qb->andWhere("v.last_update>='".$datefixe."'");
            $qb->andWhere("v.first_update<='".$datefixe."'");
        } else {
            $qb->andWhere('DATE_DIFF(CURRENT_TIMESTAMP(),v.last_update)<5');
        }
        $query = $qb->getQuery();

        return $query->getArrayResult();
    }
}

