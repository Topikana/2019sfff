<?php

namespace AppBundle\Helper\Downtime;


use AppBundle\Entity\Downtime\Communication;
use AppBundle\Entity\Downtime\Subscription;
use AppBundle\Entity\Downtime\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Eko\FeedBundle\Feed\Feed;
use Eko\FeedBundle\Item\Writer\ItemInterface;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;


class DowntimeHelper implements ItemInterface
{

    private $ngi;
    private $site;
    private $node;
    private $start_date;
    private $end_date;
    private $pub_date;
    private $link;
    private $description;
    private $severity;
    private $start_date_t;
    private $end_date_t;
    private $endpoints;
    private $classification;
    private $vo;

    /**
     * DowntimeHelper constructor.
     * @param $result
     */
    public function __construct($result)
    {
        $this->setNgi($result['NGI']);
        $this->setSite($result['SITE']);
        $this->setNode($result['entities']);
        $this->setStartDate($result['FORMATED_START_DATE']);
        $this->setEndDate($result['FORMATED_END_DATE']);
        $this->setStartDateT($result['START_DATE']);
        $this->setEndDateT($result['END_DATE']);
        $this->setPubDate($result['INSERT_DATE']);
        $this->setLink($result['GOCDB_PORTAL_URL']);
        $this->setDescription($result['DESCRIPTION']);
        $this->setSeverity($result['SEVERITY']);
        $this->setEndpoints($result['Endpoints']);
        $this->setClassification($result['CLASSIFICATION']);
        if (array_key_exists('vos', $result)) {
            $this->setVo($result['vos']);
        } else {
            $this->setVo(null);
        }
    }


    /**
     * @return mixed
     */
    public function getNgi()
    {
        return $this->ngi;
    }

    /**
     * @param mixed $ngi
     */
    public function setNgi($ngi)
    {
        $this->ngi = $ngi;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param mixed $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $start_date
     */
    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param mixed $end_date
     */
    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     * @return mixed
     */
    public function getPubDate()
    {
        return $this->pub_date;
    }

    /**
     * @param mixed $pub_date
     */
    public function setPubDate($pub_date)
    {
        $this->pub_date = $pub_date;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param mixed $severity
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @return mixed
     */
    public function getStartDateT()
    {
        return $this->start_date_t;
    }

    /**
     * @param mixed $start_date_t
     */
    public function setStartDateT($start_date_t)
    {
        $this->start_date_t = $start_date_t;
    }

    /**
     * @return mixed
     */
    public function getEndDateT()
    {
        return $this->end_date_t;
    }

    /**
     * @param mixed $end_date_t
     */
    public function setEndDateT($end_date_t)
    {
        $this->end_date_t = $end_date_t;
    }

    /**
     * @return mixed
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * @param mixed $endpoints
     */
    public function setEndpoints($endpoints)
    {
        $this->endpoints = $endpoints;
    }

    /**
     * @return mixed
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * @param mixed $classification
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;
    }

    /**
     * @return mixed
     */
    public function getVo()
    {
        return $this->vo;
    }

    /**
     * @param mixed $vo
     */
    public function setVo($vo)
    {
        $this->vo = $vo;
    }

    public function getVoToString(){
        $str = '';

        if($this->getVo() != null){
            $str = implode(",", $this->getVo()->getArrayCopy());
        }
        return $str;
    }

    public static function removeUnwantedDowntimes(SubscriptionRepository $subscriptionRepository, $downtimes, $type, $user){
        /**
         * @var $subscription Subscription
         * @var $downtime DowntimeHelper
         * @var $downtimes ArrayCollection
         */
        // REMOVE ALL ENTRIES WITH "I DONT WANT"
        foreach($subscriptionRepository->getSubscription($user,$type) as $subscription){

            if($subscription->getVo() != 'ALL' && $subscription->getRule() == Subscription::I_DONT_WANT){
                foreach($downtimes as $downtime){
                    if($downtime->getVo() != null && in_array($subscription->getVo(),$downtime->getVo()->getArrayCopy()) ){
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            if($subscription->getRegion() == 'ALL' && $subscription->getRule() == Subscription::I_DONT_WANT){
                foreach($downtimes as $downtime){
                    $downtimes->removeElement($downtime);
                }
                continue;
            }

            if($subscription->getSite() == 'ALL' && $subscription->getRule() == Subscription::I_DONT_WANT){
                foreach($downtimes as $downtime){
                    if($downtime->getNgi() == $subscription->getRegion()){
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            if($subscription->getNode() == 'ALL' && $subscription->getRule() == Subscription::I_DONT_WANT){
                foreach($downtimes as $downtime){
                    if($downtime->getSite() == $subscription->getSite()){
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            if($subscription->getRule() == Subscription::I_DONT_WANT){
                foreach($downtimes as $downtime){
                    if(in_array(' '.$subscription->getNode().' ',$downtime->getNode()->getArrayCopy()) ){
                        $downtimes->removeElement($downtime);
                    }
                }
            }
        }
    }

    public static function getFeedTypeRSS(SubscriptionRepository $subscriptionRepository, $downtimes, $user, $feed){

        /**
         * @var $subscription Subscription
         * @var $downtime DowntimeHelper
         * @var $downtimes ArrayCollection
         * @var $feed Feed
         */

        foreach($subscriptionRepository->getSubscription($user,Communication::TYPE_RSS) as $subscription){
            if($subscription->getVo() != 'ALL' && $subscription->getRule() == Subscription::I_WANT){
                foreach($downtimes as $downtime){
                    if($downtime->getVo() != null && in_array($subscription->getVo(),$downtime->getVo()->getArrayCopy()) ){
                        $feed->add($downtime);
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            if($subscription->getRegion() == 'ALL'){
                foreach($downtimes as $downtime){
                    $feed->add($downtime);
                    $downtimes->removeElement($downtime);
                }
                continue;
            }

            if($subscription->getSite() == 'ALL'){
                foreach($downtimes as $downtime){
                    if($downtime->getNgi() == $subscription->getRegion()){
                        $feed->add($downtime);
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            if($subscription->getNode() == 'ALL'){
                foreach($downtimes as $downtime){
                    if($downtime->getSite() == $subscription->getSite()){
                        $feed->add($downtime);
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            foreach($downtimes as $downtime){
                if(in_array(' '.$subscription->getNode().' ',$downtime->getNode()->getArrayCopy()) ){
                    $feed->add($downtime);
                    $downtimes->removeElement($downtime);
                }
            }
        }

        return $feed;
    }

    public static function getFeedTypeICAL(SubscriptionRepository $subscriptionRepository, $downtimes, $user, $calendar){

        /**
         * @var $subscription Subscription
         * @var $downtime DowntimeHelper
         * @var $downtimes ArrayCollection
         * @var $calendar Calendar
         */

        foreach($subscriptionRepository->getSubscription($user,Communication::TYPE_ICAL) as $subscription){
            if($subscription->getVo() != 'ALL' && $subscription->getRule() == Subscription::I_WANT){
                foreach($downtimes as $downtime){
                    if($downtime->getVo() != null && in_array($subscription->getVo(),$downtime->getVo()->getArrayCopy()) ){
                        $calendar->addEvent($downtime->createCalendarEvent());
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            if($subscription->getRegion() == 'ALL'){
                foreach($downtimes as $downtime){
                    if($downtime->getStartDateT()<= $downtime->getEndDateT()){
                        $calendar->addEvent($downtime->createCalendarEvent());
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            if($subscription->getSite() == 'ALL'){
                foreach($downtimes as $downtime){
                    if($downtime->getNgi() == $subscription->getRegion() && $downtime->getStartDateT()<= $downtime->getEndDateT()){
                        $calendar->addEvent($downtime->createCalendarEvent());
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            if($subscription->getNode() == 'ALL'){
                foreach($downtimes as $downtime){
                    if($downtime->getSite() == $subscription->getSite() && $downtime->getStartDateT()<= $downtime->getEndDateT()){
                        $calendar->addEvent($downtime->createCalendarEvent());
                        $downtimes->removeElement($downtime);
                    }
                }
                continue;
            }

            foreach($downtimes as $downtime){
                if(in_array(' '.$subscription->getNode().' ',$downtime->getNode()->getArrayCopy()) && $downtime->getStartDateT()<= $downtime->getEndDateT()){
                    $calendar->addEvent($downtime->createCalendarEvent());
                    $downtimes->removeElement($downtime);
                }
            }
        }

        return $calendar;
    }

    public function createCalendarEvent(){
        $event = new CalendarEvent();
        $dateS = new \DateTime();
        $dateE = new \DateTime();
        $event->setStart($dateS->setTimestamp($this->getStartDateT()))
            ->setEnd($dateE->setTimestamp($this->getEndDateT()))
            ->setSummary('[DOWNTIME - '.$this->getClassification().' '.$this->getSeverity().'] '.$this->getNgi().' - '.$this->getSite())
            ->setDescription($this->getDescription().' - Endpoints : '.$this->getEndpoints().' - VOs : '.$this->getVoToString())
            ->setUid($this->getLink());
        return $event;
    }


    public function getFeedItemTitle()
    {
        return '[DOWNTIME - '.htmlentities($this->classification).' '.htmlentities($this->severity).'] '.htmlentities($this->ngi).' - '.htmlentities($this->site);
    }

    public function getFeedItemDescription()
    {
        return  htmlentities($this->start_date).' - '.htmlentities($this->end_date).' : '.htmlentities($this->description).' - Endpoints : '.htmlentities($this->endpoints).' - VOs : '.htmlentities($this->getVoToString());
    }

    public function getFeedItemLink()
    {
        return htmlentities($this->link);
    }

    public function getFeedItemPubDate()
    {
        return (new \DateTime())->createFromFormat('Y-m-d G:i',$this->pub_date);
    }


}