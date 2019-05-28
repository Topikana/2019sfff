<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 05/02/16
 * Time: 15:02
 */

namespace AppBundle\Model\Broadcast;
use Doctrine\ORM\Repository;
use Doctrine\ORM\EntityManager;


use Lavoisier\Hydrators\EntriesHydrator;
use Lavoisier\Query;
use Symfony\Component\DependencyInjection\ContainerInterface;

use AppBundle\Entity\Broadcast\BroadcastMailingLists;
use AppBundle\Entity\Broadcast\BroadcastMessage;
use AppBundle\Entity\Broadcast\MailMessage;
use AppBundle\Services\JSTree\Renderer\CheckboxRenderer;

class ModelBroadcast
{

    const PUBLISH_NEW_ARCH_BC = "1";
    const PUBLISH_ARCH_BC  = "0";
    const PUBLISH_BC  = "2";
    const PUBLISH_MODEL  = "3";

    /**
     * @var ContainerInterface from when we get the doctrine config
     */
    protected $container;


    /**
     * @var $repositoryVO : repository for VO entity
     */
    protected $repositoryBroadcastMailingLists;



    /**
     * @var $repositoryVO : repository for VO entity
     */
    protected $repositoryBroadcastMessage;



    /**
     * @var $repositoryVO : repository for VO entity
     */
    protected $repositoryMailMessage;



    /**
     * @var $broadcastMailingLists BroadcastMailingLists: the Broadcast to get info from
     */
    protected $broadcastMailingList;


    /**
     * @var $broadcastMessage BroadcastMessage: the BroadcastMessage to get info from
     */
    protected $broadcastMessage;

    /**
     * @var $mailMessage MailMessage: the MailMessage to get info from
     */
    protected $mailMessage;


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

        $this->repositoryBroadcastMailingLists = $this->container->get('doctrine')->getRepository('AppBundle:Broadcast\BroadcastMailingLists');
        $this->repositoryBroadcastMessage = $this->container->get('doctrine')->getRepository('AppBundle:Broadcast\BroadcastMessage');
        $this->repositoryMailMessage = $this->container->get('doctrine')->getRepository('AppBundle:Broadcast\MailMessage');

        $this->em = $this->container->get("doctrine")->getManager();
        $this->lavoisierUrl = $this->container->getParameter("lavoisierurl");
    }


    public function getLastBroadcasts($limit) {

        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select("b")
            ->from("AppBundle:Broadcast\BroadcastMessage",  "b")
            ->where("b.publication_type <> :publishbc")
            ->andWhere("b.publication_type <> :publishmodel")
            ->setParameter("publishbc", static::PUBLISH_BC)
            ->setParameter("publishmodel", static::PUBLISH_MODEL)
            ->orderBy("b.created_at", "DESC");


        $qb->setMaxResults($limit);

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;

    }


    public function getMyBroadcast()
    {

        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select("b")
            ->from("AppBundle:Broadcast\BroadcastMessage",  "b")
            ->where("b.author_cn = :cn")
            ->setParameter('cn', $this->container->get('security.token_storage')->getToken()->getUser()->getUsername())
            ->orderBy("b.created_at","DESC");

        $query = $qb->getQuery();
        $result = $query->getResult();


        return $result;
    }


    public function getModels() {

        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('b')
            ->from('AppBundle:Broadcast\BroadcastMessage', 'b')
            ->where('b.publication_type = :publishbc')
            ->setParameter("publishbc", static::PUBLISH_MODEL)
            ->orderBy("b.publication_type","ASC");

        $query = $qb->getQuery();
        $result = $query->getResult();



        return $result;
    }


    public function getJSONCheckBoxes(BroadcastMessage $broadcast) {
        $unserialized_targets = unserialize($broadcast->getTargetsId());


        $targets_as_checkbox = new CheckboxRenderer($unserialized_targets);
        // return $targets_as_checkbox;



        return  json_encode($targets_as_checkbox->render());
    }

    public function search(array $searchCriteria) {

        // begin and end date will be ALWAYS filled
        $begin_date = $searchCriteria["begin_date"]->format("Y").'-'.$searchCriteria["begin_date"]->format("m").'-'.$searchCriteria["begin_date"]->format("d");
        $end_date = $searchCriteria["end_date"]->format("Y").'-'.$searchCriteria["end_date"]->format("m").'-'.$searchCriteria["end_date"]->format("d")." 23:59";

        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('b')
            ->from('AppBundle:Broadcast\BroadcastMessage', 'b')
            ->where('b.created_at BETWEEN :begin AND :end')
            ->setParameter("begin", $begin_date)
            ->setParameter("end", $end_date);


        if(!empty($searchCriteria["author"])) {
            $qb->andWhere("b.author_cn LIKE :author")
                ->setParameter("author", "%" . $searchCriteria["author"] . "%");
        }

        if(!empty($searchCriteria["subject"])) {
            $qb->andWhere("b.subject LIKE :subject")
                ->setParameter("subject", "%" .  $searchCriteria["subject"] . "%");
        }

        if(!empty($searchCriteria["body"])) {
            $qb->andWhere("b.body LIKE  :body")
                ->setParameter("body", "%" . $searchCriteria["body"] . "%");
        }

        if(!empty($searchCriteria["email"])) {
            $qb->andWhere("b.targets_mail LIKE :email")
                ->setParameter("email", "%" . $searchCriteria["email"] . "%");
        }

        $qb->orderBy('b.created_at','DESC');


        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;

    }


}