<?php

namespace AppBundle\Controller\Downtime;

use AppBundle\Entity\Downtime\Communication;
use AppBundle\Entity\Downtime\EmailStatus;
use AppBundle\Entity\Downtime\Subscription;
use AppBundle\Entity\Downtime\User;
use AppBundle\Helper\Downtime\DowntimeHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Lavoisier\Hydrators\EntriesHydrator;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\CalendarExport;


class DowntimeController extends Controller
{
    /**
     * @Route("/downtimes/a/overview/{idSub}/{idES}/{pk}")
     */
    public function overviewAction($idSub, $idES, $pk){
        $em = $this->getDoctrine()->getManager();

        $commRepo = $em->getRepository('AppBundle:Downtime\EmailStatus');
        $emailstatus = $commRepo->find($idES);

        if(!$emailstatus){
            throw $this->createNotFoundException('Overview not found');
        }

        $subscription = $em->getRepository('AppBundle:Downtime\Subscription')->find($emailstatus->getSubscriptionId());


        if ($emailstatus->getSubscriptionId() == $idSub
            && $emailstatus->getPrimaryKey() == $pk){
            return $this->render('Downtime/overview.html.twig', array('emailstatus' => $emailstatus, 'subscription' => $subscription));
        }else{
            throw $this->createNotFoundException('Overview not found');
        }

    }

    /**
     * @Route("/downtimes/admin")
     */
    public function adminAction(Request $request){

        /**
         * @var $user \AppBundle\Entity\User\User
         */
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if(!$user->isSuUser()){
            throw $this->createAccessDeniedException();
        }

        $subscriptions = $em->getRepository('AppBundle:Downtime\Subscription')->findAll();

        return $this->render('Downtime/admin.html.twig', array('subscriptions' => $subscriptions));

    }


    /**
     * @Route("/downtimes/subscription", name="homepage")
     * @Route("/downtimes/")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $userRepo = $em->getRepository('AppBundle:Downtime\User');
        $dn = $this->getUser()->getDn();
        $cn = $this->getUser()->getUsername();
        $user = $userRepo->findOneBy(array('dn' => $dn));


        if(!$user) {
            $user = new User($dn, array('ROLE_USER'));
            $user->setName($cn);
            $em->persist($user);
            $em->flush();
        }

        $delimiters = array(",", ";", "/");
        $ready = str_replace($delimiters, $delimiters[0], $dn);
        $launch = explode($delimiters[0], $ready);

        for ($i = count($launch) - 1; $i >= 0; $i--) {
            //Parse CN
            $start = 'emailAddress=';
            $ini = strpos($launch[$i], $start);

            if (isset($ini) && $ini !== false) {
                $user->setEmail(substr($launch[$i], $ini + 13));
            }

        }

        /**
         * @var Subscription $subscription
         */
        $originalSubscriptions = new ArrayCollection();
        $originalCommunications = new ArrayCollection();

        if($subscriptions = $user->getSubscriptions()){
            foreach ($subscriptions as $subscription) {
                $originalSubscriptions->add($subscription);

                if($communications = $subscription->getCommunications()){
                    foreach ($communications as $communication) {
                        $originalCommunications->add($communication);
                    }
                }

            }
        }

        $user_form = $this->createForm('AppBundle\Form\Downtime\UserType', $user, array(
            'action' => $this->generateUrl('homepage'),
            'method' => 'POST'
        ));

        $user_form->handleRequest($request);

        if ($user_form->isSubmitted()) {

            $currentSub = new ArrayCollection();
            $currentComm = new ArrayCollection();

            if($subscriptions = $user->getSubscriptions()){
                foreach ($subscriptions  as $subscription) {
                    $currentSub->add($subscription);

                    if($communications = $subscription->getCommunications()){
                        foreach ($communications as $communication) {
                            $currentComm->add($communication);
                        }
                    }

                $subscription->setIsActive(true);
                }
            }


            foreach ($originalCommunications as $originalCommunication) {

                if (false === $currentComm->contains($originalCommunication)) {

                    $em->remove($originalCommunication);
                }
            }

            foreach ($originalSubscriptions as $originalSubscription) {

                if (false === $currentSub->contains($originalSubscription)) {

                    $em->remove($originalSubscription);
                }
            }


            $em->persist($user);
            $em->flush();

            $lavoisierUrl = $this->getParameter('lavoisierUrl');
            $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_DOWNTIME_SUBSCRIPTION', 'notify');
            $results = $lquery->execute();

            $lavoisierUrl = $this->getParameter('lavoisierUrl');
            $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_downtime_new', 'notify');
            $results = $lquery->execute();

            return $this->redirectToRoute('homepage');

        }

        return $this->render('Downtime/index.html.twig', array('form'=>$user_form->createView(), 'user'=>$user));
    }

    /**
     * @Route("/downtimes/admin/update/{id}", name="admin_update")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function adminUpdateAction(Request $request, $id)
    {
        /**
         * @var $admin \AppBundle\Entity\User\User
         */
        $em = $this->getDoctrine()->getManager();
        $admin = $this->getUser();

        if(!$admin->isSuUser()){
            throw $this->createAccessDeniedException();
        }

        $user = $em->getRepository('AppBundle:Downtime\User')->find($id);

        if(!$user) {
            throw $this->createNotFoundException('User not found');
        }

        /**
         * @var Subscription $subscription
         */
        $originalSubscriptions = new ArrayCollection();
        $originalCommunications = new ArrayCollection();

        if($subscriptions = $user->getSubscriptions()){
            foreach ($subscriptions as $subscription) {
                $originalSubscriptions->add($subscription);

                if($communications = $subscription->getCommunications()){
                    foreach ($communications as $communication) {
                        $originalCommunications->add($communication);
                    }
                }

            }
        }

        $user_form = $this->createForm('AppBundle\Form\Downtime\UserType', $user, array(
            'action' => $this->generateUrl('admin_update', array('id' => $user->getId())),
            'method' => 'POST'
        ));

        $user_form->handleRequest($request);

        if ($user_form->isSubmitted()) {

            $currentSub = new ArrayCollection();
            $currentComm = new ArrayCollection();

            if($subscriptions = $user->getSubscriptions()){
                foreach ($subscriptions  as $subscription) {
                    $currentSub->add($subscription);

                    if($communications = $subscription->getCommunications()){
                        foreach ($communications as $communication) {
                            $currentComm->add($communication);
                        }
                    }
                }
            }


            foreach ($originalCommunications as $originalCommunication) {

                if (false === $currentComm->contains($originalCommunication)) {

                    $em->remove($originalCommunication);
                }
            }

            foreach ($originalSubscriptions as $originalSubscription) {

                if (false === $currentSub->contains($originalSubscription)) {

                    $em->remove($originalSubscription);
                }
            }


            $em->persist($user);
            $em->flush();

            $lavoisierUrl = $this->getParameter('lavoisierUrl');
            $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_DOWNTIME_SUBSCRIPTION', 'notify');
            $results = $lquery->execute();

            $lavoisierUrl = $this->getParameter('lavoisierUrl');
            $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_downtime_new', 'notify');
            $results = $lquery->execute();

            return $this->redirectToRoute('admin_update', array('id' => $user->getId()));

        }

        return $this->render('Downtime/index.html.twig', array('form'=>$user_form->createView(), 'user'=>$user));
    }

    /**
     * @Route("/downtimes/a/ngi", name="listngi")
     * @return JsonResponse
     */
    public function ngiAction()
    {
        /**
         * @var $result \ArrayObject
         */
        $lavoisierUrl = $this->getParameter('lavoisierUrl');

        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_NGI', 'lavoisier');
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);
        $result = $lquery->execute();
        $NgiList = $result->getArrayCopy();

        return new JsonResponse($NgiList);
    }

    /**
     * @Route("/downtimes/a/tier", name="listtier")
     * @return JsonResponse
     */
    public function tierAction()
    {
        $array = array(
            1,
            2,
            3
        );

        return new JsonResponse($array);
    }

    /**
     * @Route("/downtimes/a/vo", name="listvo")
     * @return JsonResponse
     */
    public function voAction()
    {
        /**
         * @var $result \ArrayObject
         */
        $lavoisierUrl = $this->getParameter('lavoisierUrl');

        $lquery = new \Lavoisier\Query($lavoisierUrl, 'VAPOR_VO', 'lavoisier');
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);
        $result = $lquery->execute();
        $voList = $result->getArrayCopy();

        return new JsonResponse($voList);
    }

    /**
     * @Route("/downtimes/a/sites/{ngi}", name="listsite")
     * @param $ngi
     * @return JsonResponse
     */
    public function sitesAction($ngi = null)
    {
        /**
         * @var $result Object
         */

        $lavoisierUrl = $this->getParameter('lavoisierUrl');

        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_SITE', 'lavoisier');
        if($ngi != null){
            $lquery->setPath("/e:entries/e:entries[e:entry[@key='NGI']/text()='".$ngi."']");
        }
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);
        $result = $lquery->execute();
        $siteList = $result->getArrayCopy();

        return new JsonResponse($siteList);
    }

    /**
     * @Route("/downtimes/a/nodesservices/{site}", name="listns")
     * @param $site
     * @return JsonResponse
     */
    public function nsAction($site = null)
    {
        /**
         * @var $result Object
         */

        $lavoisierUrl = $this->getParameter('lavoisierUrl');

        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_ENDPOINTS_Indexed', 'lavoisier');
        if($site != null){
            $lquery->setPath("/e:entries/e:entries/e:entries[@key='".$site."']/e:entries");
        }
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);
        $result = $lquery->execute();
        $nsList = $result->getArrayCopy();

        return new JsonResponse($nsList);
    }

    /**
     * @Route("/downtimes/subscriptionsjson", name="subscriptionsjson")
     * @return JsonResponse
     */
    public function subscriptionsJsonAction()
    {
        $array = array();

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:Downtime\User');
        $dn = $this->getUser()->getDn();
        $user = $userRepo->findOneBy(array('dn' => $dn));

        foreach($user->getSubscriptions() as $key => $subscription){
            $array[$key]['region'] = $subscription->getRegion();
            $array[$key]['site'] = $subscription->getSite();
            $array[$key]['node'] = $subscription->getNode();
        }


        return new JsonResponse($array);
    }

    /**
     * @Route("/downtimes/feedinfos", name="feedinfos")
     * @return JsonResponse
     */
    public function feedInfosAction()
    {
        $array = array();

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:Downtime\User');
        $dn = $this->getUser()->getDn();
        $user = $userRepo->findOneBy(array('dn' => $dn));

        /**
         * @var Subscription $subscription
         * @var Communication $communication
         */
        foreach($user->getSubscriptions() as $key => $subscription){
            $array[$key]['rss']['value'] = false;
            $array[$key]['ical']['value'] = false;
            $array[$key]['mail']['value'] = false;
            $array[$key]['none']['value'] = false;
            foreach($subscription->getCommunications() as $communication){
                if($communication->getType() == Communication::TYPE_RSS){
                    $array[$key]['rss']['value'] = $communication->getValue();
                }elseif($communication->getType() == Communication::TYPE_ICAL){
                    $array[$key]['ical']['value'] = $communication->getValue();
                }elseif($communication->getType() == Communication::TYPE_EMAIL_HTML
                    || $communication->getType() == Communication::TYPE_EMAIL_TEXT){
                    $array[$key]['email']['value'] = 1;
                }
            }
            if($subscription->getCommunications()->count() == 0){
                $array[$key]['none']['value'] = 1;
            }
        }

        return new JsonResponse($array);
    }

    /**
     * @Route("/downtimes/a/timeline", name="timeline")
     * @param Request $request
     * @return Response
     */
    public function timelineAction(Request $request)
    {

        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $period = $request->query->get('period');
        $query = $request->query->get('query');
        $field = $request->query->get('field');

        $ngi = "";
        $tier = "";
        $site = "";
        $vo = "";

        switch($field){
            case 'ngi':
                $ngi = $query;
                break;
            case 'site':
                $site = $query;
                break;
            case 'tier':
                $tier = $query;
                break;
            case 'vo':
                $vo = $query;
                break;
            default:
                break;
        }


        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_DOWNTIME_FILTER', 'lavoisier');
        $array_POST = array('period'=>$period,'ngi'=>$ngi,'site'=>$site,'tier'=>$tier,'vo'=>$vo);
        $lquery->setMethod('POST');

        $lquery->setPostFields($array_POST);
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);

        $results = $lquery->execute();

        return $this->render('Downtime/timeline.html.twig', array('results' => $results, 'ngi' => $ngi, 'tier' => $tier, 'site' => $site, 'vo'=>$vo, 'period' => $period));
    }

    /**
     * @Route("/downtimes/a/timelinedata/json", name="timelinedata_json")
     * @param Request $request
     * @return JsonResponse
     */
    public function timelineDataJSONAction(Request $request)
    {
        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $period = $request->query->get('period');
        $ngi = $request->query->get('ngi');
        $site = $request->query->get('site');
        $tier = $request->query->get('tier');

        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_DOWNTIME_FILTER', 'lavoisier');
        $array_POST = array('period'=>$period,'ngi'=>$ngi,'site'=>$site,'tier'=>$tier);
        $lquery->setMethod('POST');
        $lquery->setPostFields($array_POST);
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);

        $result = $lquery->execute();

        return new JsonResponse($result);
    }

    /**
     * @Route("/downtimes/a/timelinedata/csv", name="timelinedata_csv")
     * @param Request $request
     * @return Response
     */
    public function timelineDataCSVAction(Request $request)
    {
        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $period = $request->query->get('period');
        $ngi = $request->query->get('ngi');
        $site = $request->query->get('site');
        $tier = $request->query->get('tier');

        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_DOWNTIME_FILTER', 'lavoisier', 'csv');
        $array_POST = array('period'=>$period,'ngi'=>$ngi,'site'=>$site,'tier'=>$tier);
        $lquery->setMethod('POST');
        $lquery->setPostFields($array_POST);

        $result = $lquery->execute();

        return new Response($result, 200, array(
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export.csv"'
        ));
    }

    /**
     * @Route("/downtimes/a/feed/{id}",defaults={"_format"="xml"}, name="feedrss")
     * @param $id
     * @return Response
     */
    public function feedRSSAction($id)
    {

        // Lavoisier query
        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_DOWNTIME_FILTER', 'lavoisier');
        $lquery->setMethod('POST');
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);
        $results = $lquery->execute();


        // Init feed
        $feed = $this->get('eko_feed.feed.manager')->get('article');
        // Create downtimes collection
        $downtimes = new ArrayCollection();
        foreach($results as $result){
            $downtime = new DowntimeHelper($result);
            $downtimes->add($downtime);
        }

        // Get User
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Downtime\User');

        $user = $repository->findOneBy(array('id'=> $id));

        if (!$user) {
            throw $this->createNotFoundException(
                'No feed found for id '.$id
            );
        }

        $subscriptionRepository = $this->getDoctrine()->getRepository('AppBundle:Downtime\Subscription');


        /**
         * @var $subscription Subscription
         * @var $downtime DowntimeHelper
         * @var $downtimes ArrayCollection
         */

        // REMOVE ALL ENTRIES WITH "I DONT WANT"
        DowntimeHelper::removeUnwantedDowntimes($subscriptionRepository,$downtimes,Communication::TYPE_RSS, $user);

        DowntimeHelper::getFeedTypeRSS($subscriptionRepository, $downtimes, $user, $feed);

        return new Response($feed->render('rss'));
    }

    /**
     * @Route("/downtimes/a/feed/ical/{id}", name="feedical")
     * @param $id
     * @return Response
     */
    public function feedICALAction($id)
    {
        // Lavoisier query
        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_DOWNTIME_FILTER', 'lavoisier');
        $lquery->setMethod('POST');
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);
        $results = $lquery->execute();

        // Create downtimes collection
        $downtimes = new ArrayCollection();
        foreach($results as $result){
            $downtime = new DowntimeHelper($result);
            $downtimes->add($downtime);
        }

        // Get User
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Downtime\User');

        $user = $repository->findOneBy(array('id'=> $id));

        if (!$user) {
            throw $this->createNotFoundException(
                'No feed found for id '.$id
            );
        }

        $subscriptionRepository = $this->getDoctrine()->getRepository('AppBundle:Downtime\Subscription');

        $calendar = new Calendar();
        $calendar->setProdId('Downtimes calendar');

        /**
         * @var $subscription Subscription
         * @var $downtime DowntimeHelper
         * @var $downtimes ArrayCollection
         */
        DowntimeHelper::removeUnwantedDowntimes($subscriptionRepository,$downtimes,Communication::TYPE_ICAL, $user);

        DowntimeHelper::getFeedTypeICAL($subscriptionRepository,$downtimes,$user,$calendar);


        $calendarExport = new CalendarExport(new CalendarStream, new Formatter());
        $calendarExport->addCalendar($calendar);

        $response = new Response($calendarExport->getStream());
        $response->headers->set('Content-Type', 'text/calendar');

        return $response;
    }

    /**
     * @Route("/downtimes/a/email", name="email")
     * @return Response
     */
    public function emailAction()
    {
        $emailService = $this->get('downtime.email');

        $em = $this->getDoctrine()->getManager();

        // Lavoisier query
        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_downtime_new', 'lavoisier');
        $lquery->setMethod('POST');
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);
        $results = $lquery->execute();


        $nb_mail_sent = 0;

        foreach($results as $downtime){

            $downtime["ENDPOINTS"]["vos"] = array_unique(explode(',', $downtime["ENDPOINTS"]["vos"]));
            $downtime["ENDPOINTS"]["hosts"] = array_unique(explode(',', $downtime["ENDPOINTS"]["hosts"]));


            $users =  $downtime["targets"];
            if($users != ""){
                foreach($users as $user){
                    $repository = $this->getDoctrine()
                        ->getRepository('AppBundle:Downtime\EmailStatus');
                    $es = $repository->findOneBy(
                        array(
                            'email'=> $user["value"],
                            'subscriptionId' => $user["id"],
                            'primaryKey' => $downtime["PRIMARY_KEY"]
                        )
                    );

                    if(!$es){
                        $es = new EmailStatus();
                        $es->setEmail($user["value"]);
                        $es->setPrimaryKey($downtime["PRIMARY_KEY"]);
                        $es->setSubscriptionId($user["id"]);
                        $es->setAddingSent(false);
                        $es->setBeginningSent(false);
                        $es->setEndingSent(false);
                        $em->persist($es);
                        $em->flush();
                    }


                    if($downtime['INSERT_DATE'] <= (new \DateTime())->getTimestamp() + (60*60*24*2) && $user['adding'] && !$es->getAddingSent()){
                        $emailService->sendEmailNotification($downtime,'ANNOUNCEMENT', $user["value"], $es, $user["type"]);
                        $es->setAddingSent(true);
                        $es->setAddingSentDate(new \DateTime());
                        $nb_mail_sent++;
                    }

                    if($downtime['START_DATE'] <= (new \DateTime())->getTimestamp() + (60*20) && $user['beginning'] && !$es->getBeginningSent()){
                        $emailService->sendEmailNotification($downtime,'START', $user["value"], $es, $user["type"]);
                        $es->setBeginningSent(true);
                        $es->setBeginningSentDate(new \DateTime());
                        $nb_mail_sent++;
                    }

                    if($downtime['END_DATE'] <= (new \DateTime())->getTimestamp() + (60*12) && $user['ending'] && !$es->getEndingSent()){
                        $emailService->sendEmailNotification($downtime,'END', $user["value"], $es, $user["type"]);
                        $es->setEndingSent(true);
                        $es->setEndingSentDate(new \DateTime());
                        $nb_mail_sent++;
                    }

                    $em->persist($es);
                    $em->flush();
                }
            }
        }


        return new JsonResponse(array("email_sent" => $nb_mail_sent));
    }
}
