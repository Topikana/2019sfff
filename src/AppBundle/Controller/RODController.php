<?php

namespace AppBundle\Controller;


use AppBundle\Entity\HistoryNotepad;
use AppBundle\Entity\NotepadsAlarms;
use AppBundle\Entity\ROD\TicketAlarm;
use AppBundle\Entity\RodNagiosProblem;
use AppBundle\Entity\Settings;
use AppBundle\Entity\User;
use AppBundle\Form\NotepadType;
use AppBundle\Form\ROD\CloseTicketType;
use AppBundle\Form\ROD\TicketType;
use AppBundle\Form\SettingsType;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Lavoisier\Hydrators\EntriesHydrator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


use AppBundle\Services\TicketingSystem\Workflow\Loader;
use Symfony\Component\HttpFoundation\Response;


class RODController extends Controller
{


    /**
     * @var $container \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @Route("/ROD", name="rod" )
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
       $dn=$this->getUser()->getDn();
        $user= $this->getDoctrine()->getRepository(User::class)->findOneBy(['dn'=>$dn]);

        if (!isset($user))
            $user=new User($dn, 'ROD', null, array('ROLE_USER'));

        $setting = $user->getSetting();

        if(empty($setting)){
            $setting = new Settings();

            $user->setSetting($setting);
            $setting->setUser($user);
            $em->persist($setting);
            $em->persist($user);
            $em->flush();
        }

        $formSettings = $this->createForm(SettingsType::class,$setting);
        $formNotepad = $this->createForm(NotepadType::class);
        $formTicket = $this->createForm(TicketType::class);
        $formCloseTicket = $this->createForm(CloseTicketType::class);

        $formSettings->handleRequest($request);
        if ($formSettings->isSubmitted() && $formSettings->isValid()) {
            $data = $formSettings->getData();

            $setting->setAlarmStatus($data->getAlarmStatus());
            $setting->setProfileAlarm($data->getProfileAlarm());
            $setting->setUser($user);

            $em->persist($setting);
            $em->flush();

            $this->addFlash('success','Settings are update successfully !');

            return $this->redirectToRoute('rod');
        }

        $lavoisierUrl = $this->getParameter('lavoisierUrl');



        if (!$user->isSuUser()) {
//            $ngi = $user->getNgiToString(); // ngi like NGIFRANCE,NGI_ITALIE
//            $listSiteByNGI = (!empty($ngi)) ? $this->getListSiteByNGI($ngi) : [];
            $listSiteByNGI = $user->getListSiteByNGI($lavoisierUrl);

            if (isset($user->getOpRoles()['site'])) {
                $sitesList = array_merge($listSiteByNGI, $user->getOpRoles()['site']);
                $sitesList = implode(',', array_keys($sitesList));
            } else {
                $sitesList = implode(',', array_keys($listSiteByNGI));
            }

        }
        else {
            $sitesList='egi';
        }

        $dataSites = ($sitesList)? $this->getDashboardViewBySite($sitesList) : [];

        $sitesSecurityList = $user->getSiteSecurityOfficer($lavoisierUrl);
        $dataSitesSecurity = $this->getDashboardViewBySite($sitesSecurityList,true);

//        $dataSites2 = $this->getDashboardViewBySite($sitesList);


//            dump($dataSites['KR-KISTI-GSDC-01']);
//        dump($dataSitesSecurity['TU-Kosice']);
//        dump($dataSites2);


        return $this->render('AppBundle:ROD:index.html.twig',[
            'sites' => $dataSites,
            'sitesSecurity' => $dataSitesSecurity,
            'form_settings' => $formSettings->createView(),
            'form_notepad' => $formNotepad->createView(),
            'form_ticket' => $formTicket->createView(),
            'form_close_ticket' => $formCloseTicket->createView(),
        ]);

    }

    /**
     * get details of site by post parameters
     * @Route("/ROD/details", name="ROD_details")
     */
    public function detailsSiteAction(Request $request)
    {
        $site = $request->request->get('site');
        $detailsType = $request->request->get('detailsType');

        $details = $this->getDetailsSite($site, $detailsType);


        switch ($detailsType) {
            case 'alarms':

                return $this->render(':ROD:details_alarms.html.twig', [
                    'details' => $details,
                    'site' => $site
                ]);
                break;
            case 'alarmsSecurity':
                return $this->render(':ROD:details_alarms_security.html.twig', [
                    'details' => $details,
                    'site' => $site
                ]);
                break;
            case 'avre':
                return $this->render(':ROD:details_avre.html.twig', [
                    'details' => $details,
                    'site' => $site
                ]);
                break;
            case 'downtimes':
//                dump($details['ENDPOINTS']);die;

//                foreach ($details as $value){
//                    dump($value);die;
//                }

                return $this->render(':ROD:details_downtimes.html.twig', [
                    'details' => $details,
                    'site' => $site
                ]);
                break;
            case 'tickets':
                $helpdesk = $this->get('ggus_helpdesk_ops');

                $alarms = $this->getDetailsSite($site,'alarms', false);
                $ticketAlarm= $this->getDoctrine()->getRepository(TicketAlarm::class)->findAll();

                return $this->render(':ROD:details_tickets.html.twig', [
                    'details' => $details,
                    'site' => $site,
                    'alarms' => $alarms,
                    'ticketAlarm' => $ticketAlarm,
                    'helpdesk' => $helpdesk
                ]);
                break;
            case 'notepads':

                $alarms = $this->getDetailsSite($site,'alarms', false);
                $notepadAlarm = $this->getDoctrine()->getRepository(NotepadsAlarms::class)->findAll();

                return $this->render(':ROD:details_notepads.html.twig', [
                    'details' => $details,
                    'site' => $site,
                    'alarms' => $alarms,
                    'notepadAlarm' => $notepadAlarm,
                ]);
                break;
        }

        return 0;
    }

    /**
     * @Route("/ROD/notepadAlarm/{site}/{notepadId}", name="notepadAlarmGroupe")
     * @param $site
     * @param $notepadId
     * @return Response
     */
    public function notepadAlarmsGroupeAction($site,$notepadId){

        $alarms = $this->getDetailsSite($site,'alarms',false);
        $notepadAlarm = $this->getDoctrine()->getRepository(NotepadsAlarms::class)->findBy(['idNotepad' => $notepadId]);


        return $this->render(':ROD:notepadAlarmGroup.html.twig', [
            'site' => $site,
            'notepadId' => $notepadId,
            'alarms' => $alarms,
            'notepadAlarms' => $notepadAlarm
        ]);
    }


    /**
     * @Route("/ROD/removeNotepadAlarm/", name="remove_alarm_to_notepad")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Lavoisier\Exceptions\HTTPStatusException
     */
    public function removeAlarmsToNotepadAction(Request $request){

        $site = $request->request->get('site');
        $alarms = $request->request->get('alarms');
        $notepad = $request->request->get('notepad');

        $em = $this->getDoctrine()->getManager();
        $history_alarms = $alarms;
        $alarms = explode(',', $alarms);
        $date = date_create('now');

        $notepad_history = new HistoryNotepad();
        $notepad_history->setNotepadId($notepad);
        $notepad_history->setAlarmId($history_alarms);
        $notepad_history->setCreationDate($date);
        $notepad_history->setStatus(3);

        $em->persist($notepad_history);
        $em->flush();

        foreach ($alarms as $alarmId){
            $alarm= $this->getDoctrine()->getRepository(RodNagiosProblem::class)->find($alarmId);
            if (isset($alarm)) {
                $alarm->setOpsFlags(0);
                $em->persist($alarm);
            }
            $notepadAlarm = $this->getDoctrine()->getRepository(NotepadsAlarms::class)->findOneBy(['idAlarm' =>$alarmId]);
            $em->remove($notepadAlarm);
        }

        $em->flush();

        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarms', 'notify');
        $lquery->execute();
        sleep(5);

        return $this->redirectToRoute('notepadAlarmGroupe',['site' => $site, 'notepadId' =>$notepad]);
    }


    /**
     * @Route("/ROD/addNotepadAlarm/", name="add_alarm_to_notepad")
     * @param Request $request
     * @param $alarms
     * @param $site
     * @param $notepad
     */
    public function addAlarmsToNotepadAction(Request $request){

        $site = $request->request->get('site');
        $alarms = $request->request->get('alarms');
        $notepad = $request->request->get('notepad');

        $em = $this->getDoctrine()->getManager();
        $history_alarms = $alarms;
        $alarms = explode(',', $alarms);
        $date = date_create('now');

        $notepad_history = new HistoryNotepad();
        $notepad_history->setNotepadId($notepad);
        $notepad_history->setAlarmId($history_alarms);
        $notepad_history->setCreationDate($date);
        $notepad_history->setStatus(2);

        $em->persist($notepad_history);
        $em->flush();


        foreach ($alarms as $alarmId){

            $notepadAlarm = new NotepadsAlarms();
            $notepadAlarm->setIdNotepad($notepad);
            $notepadAlarm->setIdAlarm($alarmId);
            $em->persist($notepadAlarm);

                $alarm = $this->getDoctrine()->getRepository(RodNagiosProblem::class)->find($alarmId);
            if (isset($alarm)) {
                $alarm->setOpsFlags(2);
                $em->persist($alarm);
            }
        }

        $em->flush();



        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarms', 'notify');
        $lquery->execute();
        sleep(5);


        return $this->redirectToRoute('notepadAlarmGroupe',['site' => $site, 'notepadId' =>$notepad]);
    }


    /**
     * get the history of an alarm by id
     * @Route("/ROD/history/alarms/{id}", name="rod_history_alarm")
     * @param $id
     * @return Response
     */
    public function getHistoryAlarms($id){

        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $array_POST = [
            "id" => $id,
        ];

        try{
            $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarm_history', 'lavoisier');
            $lquery->setMethod('POST');
            $lquery->setPostFields($array_POST);

            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);

            $result = $lquery->execute();
            $history = $result->getArrayCopy();

        }catch (\Exception $e) {
            $this->addFlash(
                "danger",
                "ROD Dashboard- Can't get List of history alarms's   .. Lavoisier call failed - ".$e->getMessage()
            );
        }

        return $this->render(':ROD:history_alarm.html.twig', [
            'data_history' => $history
        ]);
    }

    /**
     * get issues by site with POST param
     *
     * @param $sitesList
     * @param bool $sitesSecList
     * @return array
     */
    public function getDashboardViewBySite($sitesList = null, $sitesSecList = false) : array
    {

        $user= $this->getDoctrine()->getRepository(User::class)->findOneBy(['dn'=>$this->getUser()->getDn()]);

        if(!empty($user->getSetting())){
            $profilesList = implode(',',$user->getSetting()->getProfileAlarm());
            $statusList = implode(',',$user->getSetting()->getAlarmStatus());
            $FilterAssigned = !in_array(4,$user->getSetting()->getAlarmStatus());
        }else{
            // if the user don't have setting
            $statusList = "0,1,2,3";
            $profilesList = "ARGO_MON_OPERATORS,OPS_MONITOR,ARGO_MON_CRITICAL";
            $FilterAssigned = true;
        }

        $array_POST = [
            "SitesList" => $sitesList,
            "statusList" => $statusList,
            "profilesList" => $profilesList,
            "FilterAssigned" => $FilterAssigned
        ];

        if(!empty($sitesList)){
            $lavoisierUrl = $this->getParameter('lavoisierUrl');

            try{
                if ($sitesSecList == false){
                    $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_VIEW', 'lavoisier');
                }else{
                    $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_VIEW_SEC', 'lavoisier');
                }

                $lquery->setMethod('POST');
                $lquery->setPostFields($array_POST);

                $hydrator = new EntriesHydrator();
                $lquery->setHydrator($hydrator);
                $result = $lquery->execute();
                return $result->getArrayCopy();

            }catch (\Exception $e) {
                $this->addFlash(
                    "danger",
                    "ROD Dashboard- Can't get data of Issues by Site  .. Lavoisier call failed - ".$e->getMessage()
                );
            }
        }

        return [];


    }

    public function getDetailsSite($site, $detailsType = null, $assigned = null) : array
    {

//        $statusList="0,1,2";
//        $profilesList="ARGO_MON_OPERATORS,OPS_MONITOR,ARGO_MON_CRITICAL";
//        $FilterAssigned=true;

//        $array_POST = [
//            "SitesList" => $site,
//            "statusList" => $statusList,
//            "profilesList" => $profilesList,
//            "FilterAssigned" => $FilterAssigned

        $user= $this->getDoctrine()->getRepository(User::class)->findOneBy(['dn'=>$this->getUser()->getDn()]);
        $profilesList = implode(',',$user->getSetting()->getProfileAlarm());
        $statusList = implode(',',$user->getSetting()->getAlarmStatus());
        $FilterAssigned = (isset($assigned))? $assigned : !in_array(4, $user->getSetting()->getAlarmStatus());
        $data_details = [];

        if($detailsType == 'alarmsSecurity'){
            $array_POST = [
                "FilterAssigned" => $FilterAssigned,
                "SitesListFormatted" => ",{$site},",
                "statusListFormatted" => ",{$statusList},",
                "profilesListFormatted" => $profilesList
            ];
        }else{
            $array_POST = [
                "SitesList" => $site,
                "statusList" => $statusList,
                "profilesList" => $profilesList,
                "FilterAssigned" => $FilterAssigned,
                "summary" => 1
            ];
        }


        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        try{
            if($detailsType == 'alarmsSecurity'){
                $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarms_sec_filter', 'lavoisier');
            }else{
                $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_VIEW', 'lavoisier');
            }


            if($site != null && $detailsType != null && $detailsType != 'alarmsSecurity'){
                $lquery->setPath("/e:entries/e:entries[@key='$site']/e:entries[@key='$detailsType']/*");
            }else if($site != null && $detailsType == null){
                $lquery->setPath("/e:entries/e:entries[@key='$site']/*");
            }
            $lquery->setMethod('POST');
            $lquery->setPostFields($array_POST);

            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();
            if (isset($result) && $result!=null){
                if(isset($detailsType)){
                    $data_details[$detailsType] = $result->getArrayCopy();
                }else{
                    $data_details = $result->getArrayCopy();
                }
//                $data_details = $result->getArrayCopy();
            }

        }catch (\Exception $e) {
            $this->addFlash(
                "danger",
                "ROD Dashboard- Can't get details of Site  .. Lavoisier call failed - ".$e->getMessage()
            );
        }

        return $data_details;
    }

    /**
     * @Route("/ROD/debug/", name="rod_get_site")
     *
     */
    public function getSiteRODAction(){

        $lavoisierUrl = $this->getParameter('lavoisierUrl');


//        $site = 'IN2P3-IRES';
//        $cookie = new Cookie('showDetailsTicket',$site,strtotime('now + 5 minutes'),'/',null,true,false);
//        $res = new Response();
//        $res->headers->setCookie( $cookie );
//        $res->send();
//        dump($cookie);
//        Die();


        $user= $this->getDoctrine()->getRepository(User::class)->findOneBy(['dn'=>$this->getUser()->getDn()]);
        $setting = $user->getSetting();
        $setting->setAlarmStatus(explode(',',$setting->getAlarmStatus()));
        $setting->setProfileAlarm(explode(',',$setting->getProfileAlarm()));

        $listSiteByNgi = $user->getListSiteByNGI($lavoisierUrl, []);
        $sitesList = implode(',', array_keys($listSiteByNgi));
        $dataSites =  $this->getDashboardViewBySite("");

//        dump($listSiteByNgi);
//        dump($sitesList);
        dump($dataSites);
        Die;

//            $sitesList='egi';
//            $ngi = 'NGI_IL';
//            $sitesSecList = (!empty($ngi)) ? $this->getListSiteByNGI($ngi) : [];
//            $sitesSecList = implode(',', array_keys($sitesSecList));
//
//
////        dump($sitesSecList);
//
////        dump($user->isSecurityOfficer('NGI Security Officer'));
////        var_dump($user);
//            $dataSites = $this->getDashboardViewBySite($sitesSecList, true);

//        $detail = $this->getDetailsSite('TECHNION-LCG2,NGI_IL,IL_COMP,IL-IUCC,IL-BGU,IL-BGU-PPS,WEIZMANN-LCG2,TAU-LCG2,IL-TAU-HEP,IL-TAU-CS,LCG-IL-OU,TECHNION-HEP,HRL_KZ,IL_IUCC_IG,NGI_IL_MED1,IUCC_Fed_Cloud','alarmsSecurity');


        return $this->render(':ROD:details_downtimes.html.twig', [
            'details' => $data
        ]);
    }

    /**
     * @Route("/ROD/details/{site}/{isSecurity}", name="rod_all_details_site")
     * @param $site
     * @param $isSecurity
     * @return Response
     */
    public function getAllDetailsSiteAction($site, $isSecurity = 'all' ){

        $details = $this->getDetailsSite($site);

        $alarmsSecurity['alarmsSecurity'] = [];
        if ($isSecurity == 'security' && $this->getUser()->isSecuOfficer()){
            $alarmsSecurity = $this->getDetailsSite($site, "alarmsSecurity");
        }else{

            $formCloseTicket = $this->createForm(CloseTicketType::class);
            $formNotepad = $this->createForm(NotepadType::class);
            $formTicket = $this->createForm(TicketType::class);

            return $this->render(':ROD:all_details_site.html.twig', [
                'details' => $details,
                'site' => $site,
                'form_close_ticket' => $formCloseTicket->createView(),
                'form_notepad' => $formNotepad->createView(),
                'form_ticket' => $formTicket->createView(),
                'isSecurity' => $isSecurity,
                'alarmsSecurity' => $alarmsSecurity


            ]);
        }

        return $this->render(':ROD:all_details_site.html.twig', [
            'details' => $details,
            'site' => $site,
            'isSecurity' => $isSecurity,
            'alarmsSecurity' => $alarmsSecurity
        ]);
    }

    /**
     * @Route("/ROD/ticket/{TicketId}", name="ticket_history")
     *
     */
    public function getTicketHistoryAction(Request $request) {

        $id = $request->get('TicketId');

        $details = $request->get('details');

        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/Rod');
        $loader = new Loader($wf_paths);
        $containerBd = new ContainerBuilder();
        $loader->load(array(),  $containerBd);
        $workflow =  $containerBd->get("workflow_rod");

        $helpdesk = $this->get('ggus_helpdesk_ops');
        $helpdesk->setLavoisierNotification(false);

        $history=$helpdesk->getTicketHistory($id);

        $infoTicket=$helpdesk->getTicket($id);


//        dump($infoTicket);die;
//        $alarms = $this->getDetailsSite($site,'alarms', false);


        return $this->render(':ROD:history_ticket.html.twig',[
            'data_history' => $history,
            'details' => $details,
            'ticketId' => $id,
            'infoTicket' => $infoTicket,
        ]);



    }

    /**
     * @Route("/ROD/new-ticket/", name="new_ticket")
     *
     */
    public function newTicketAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $data = $request->get('appbundle_rod_ticket');

        $alarm = explode(',',$data['alarms']);

        //1) get the directory to find service.xml
        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/Rod');

        //2) load yml files that service is depending on (vomshelprequest and closeregistration)
        $loader = new Loader($wf_paths);
        $containerBd = new ContainerBuilder();
        $loader->load(array(),  $containerBd);

        //3) get service
        $workflow =  $containerBd->get("workflow_rod");
        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                                      III : GET THE GGUS SERVICE

        $helpdesk = $this->get('ggus_helpdesk_ops');
        $helpdesk->setLavoisierNotification(false);

        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                                      IV : GET THE TICKET STRUCTURE

        $ticketROD = $workflow->ticketFromStepId(
            'new',
            $helpdesk->getTicketInstance(),[
                'site_name' => $data['site'],
                'ngi_name' => $data['ngi'],
                'pb_number' => $data['pbNumber'],
                'group' => $data['description'],
                'ticket_link' => sprintf("https://%s%s",
                    $_SERVER['HTTP_HOST'],
                    $this->generateUrl('rod_all_details_site', [
                        'site' => $data['site'],
                        'isSecurity' => 'all'
                    ])),
                'user' =>$this->getUser()->getUsername(),
            ]
        );

        $ticketROD->setWorkflow("workflow_rod");
        $ticketROD->setHelpdesk("ggus_helpdesk_ops");
        $ticketROD->setResponsibleUnit('EGI Operations');

        $ggus_id=0;

        try{
            $ggus_id = $helpdesk->createTicket($ticketROD);
        }catch (\Exception $exception){
            echo $exception;
        }

        if(!empty($alarm[0])){

            foreach ($alarm as $item){
                $ticketAlarm = new TicketAlarm();
                $ticketAlarm->setIdTicket($ggus_id);
                $ticketAlarm->setIdAlarm($item);
                $em->persist($ticketAlarm);

                    $alarm = $this->getDoctrine()->getRepository(RodNagiosProblem::class)->find($item);
                if (isset($alarm)) {
                    $alarm->setOpsFlags(2);
                    $em->persist($alarm);
                }

            }
            $em->flush();
        }


        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarms', 'notify');
        $lquery->execute();

        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_TICKETS', 'notify');
        $lquery->execute();
        sleep(3);

        return $this->redirectToRoute('rod',['ggus_id'=>$ggus_id]);
    }

    /**
     * @Route("/ROD/delete-ticket/", name="delete_ticket")
     *
     */
    public function deleteTicketAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $data = $request->get('appbundle_rod_close_ticket');
//        dump($data);

//        $alarm = explode(',',$data['alarms']);
        //--------------------------------------------------------------------------------------------------------//
        //                                      I : GET THE WORKFLOW VO SERVICE

        //1) get the directory to find service.xml
        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/Rod');

        //2) load yml files that service is depending on (vomshelprequest and closeregistration)
        $loader = new Loader($wf_paths);
        $containerBd = new ContainerBuilder();
        $loader->load(array(),  $containerBd);

        //3) get service
        $workflow =  $containerBd->get("workflow_rod");
        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                                      II : GET THE GGUS SERVICE

        $helpdesk = $this->get('ggus_helpdesk_ops');
        $helpdesk->setLavoisierNotification(false);


        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                                      III : GET THE TICKET STRUCTURE

        $ticketROD = $workflow->ticketFromStepId(
            'close',
            $helpdesk->getTicket($data['ticketId']),
            [
                'user' =>$this->getUser()->getUsername(),
            ]
        );

        try{
            $ggus_id = $helpdesk->updateTicket($ticketROD);
            $permaLink = $helpdesk->getTicketPermalink($ticketROD->getId());

        }catch (\Exception $exception){
            echo $exception;
        }

        if($data['addVerifyStatusInGGUS'] == 1 ){
            $ticketROD2 = $workflow->ticketFromStepId(
                'verify',
                $helpdesk->getTicket($data['ticketId']),
                [
                    'user' =>$this->getUser()->getUsername(),
                ]
            );

            try{
                $ggus_id = $helpdesk->updateTicket($ticketROD2);
            }catch (\Exception $exception){
                echo $exception;
            }

        }

        $ticketAlarms= $this->getDoctrine()->getRepository(TicketAlarm::class)->findBy(['idTicket' => $data['ticketId']]);

        foreach ($ticketAlarms as $ticketAlarm){

                $alarm = $this->getDoctrine()->getRepository(RodNagiosProblem::class)->find($ticketAlarm->getIdAlarm());
            if (isset($alarm)) {
                $alarm->setOpsFlags(0);
                $em->persist($alarm);
            }

            $em->remove($ticketAlarm);

        }
        $em->flush();

        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_TICKETS', 'notify');
        $lquery->execute();
        sleep(5);

        if($data['site'] !== null){
        $site = $data['site'];
        $cookieSite = new Cookie('showDetailsSite',$site,strtotime('now + 5 minutes'),'/',null,true,false);
        $cookieType = new Cookie('showDetailsType','ticket',strtotime('now + 5 minutes'),'/',null,true,false);
        $res = new Response();
        $res->headers->setCookie($cookieSite);
        $res->headers->setCookie($cookieType);
        $res->send();
        return $this->redirectToRoute('rod');
        }
    }

    /**
     * @Route("/ROD/ticketAlarm/{site}/{ticketId}", name="ticketAlarmGroupe")
     * @param $site
     * @param $ticketId
     * @return Response
     */
    public function ticketAlarmsGroupeAction($site,$ticketId){

//        $alarms = $this->getDoctrine()->getRepository(RodNagiosProblem::class)->findBy(['site' =>$site]);

        $alarms = $this->getDetailsSite($site,'alarms',false);
        $ticketAlarm= $this->getDoctrine()->getRepository(TicketAlarm::class)->findBy(['idTicket' => $ticketId]);

        //
        return $this->render(':ROD:ticketAlarmGroupe.html.twig', [
            'site' => $site,
            'ticketId' => $ticketId,
            'alarms' => $alarms,
            'ticketAlarms' => $ticketAlarm
        ]);
    }

    /**
     * @Route("/ROD/removeTicketAlarm/", name="remove_alarm_to_ticket")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Lavoisier\Exceptions\HTTPStatusException
     */
    public function removeAlarmsToTicketAction(Request $request){

        $site = $request->request->get('site');
        $alarms = $request->request->get('alarms');
        $ticket = $request->request->get('ticket');

        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/Rod');
        $loader = new Loader($wf_paths);
        $containerBd = new ContainerBuilder();
        $loader->load(array(),  $containerBd);
        $workflow =  $containerBd->get("workflow_rod");
        $helpdesk = $this->get('ggus_helpdesk_ops');
        $helpdesk->setLavoisierNotification(false);

        $em = $this->getDoctrine()->getManager();
        $alarms = explode(',', $alarms);

        $textUpdate = "";
        foreach ($alarms as $alarmId){
            $alarm= $this->getDoctrine()->getRepository(RodNagiosProblem::class)->find($alarmId);
            if (isset($alarm)) {
                $hostName = $alarm->getHostName();
                $testName = $alarm->getTestName();
                $textUpdate .= "{$testName} @ {$hostName}  <br>";
                $alarm->setOpsFlags(0);
                $em->persist($alarm);
            }
            $ticketAlarm = $this->getDoctrine()->getRepository(TicketAlarm::class)->findOneBy(['idAlarm' =>$alarmId]);
            $em->remove($ticketAlarm);
        }

        $ticketROD = $workflow->ticketFromStepId(
            'update',
            $helpdesk->getTicket($ticket),
            [
                'user' =>$this->getUser()->getUsername(),
                'action' => 'removed',
                'alarms' => $textUpdate
            ]
        );

        try{

//            dump($ticketROD);die;
            $helpdesk->
            $ggus_id = $helpdesk->updateTicket($ticketROD);

        }catch (\Exception $exception){
            echo $exception;
        }

        $em->flush();

        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarms', 'notify');
        $lquery->execute();
        sleep(7);

        return $this->redirectToRoute('ticketAlarmGroupe',['site' => $site, 'ticketId' =>$ticket]);
    }


    /**
     * @Route("/ROD/addTicketAlarm/", name="add_alarm_to_ticket")
     * @param $alarms
     * @param $site
     * @param $ticket
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Lavoisier\Exceptions\HTTPStatusException
     */
    public function addAlarmsToTicketAction(Request $request){

        $site = $request->request->get('site');
        $alarms = $request->request->get('alarms');
        $ticket = $request->request->get('ticket');

        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/Rod');
        $loader = new Loader($wf_paths);
        $containerBd = new ContainerBuilder();
        $loader->load(array(),  $containerBd);
        $workflow =  $containerBd->get("workflow_rod");
        $helpdesk = $this->get('ggus_helpdesk_ops');
        $helpdesk->setLavoisierNotification(false);

        $em = $this->getDoctrine()->getManager();
        $alarms = explode(',', $alarms);

        $textUpdate = "";
        foreach ($alarms as $alarmId){

            $ticketAlarm = new TicketAlarm();
            $ticketAlarm->setIdTicket($ticket);
            $ticketAlarm->setIdAlarm($alarmId);
            $em->persist($ticketAlarm);

            $alarm= $this->getDoctrine()->getRepository(RodNagiosProblem::class)->find($alarmId);
            if (isset($alarm)) {
                $hostName = $alarm->getHostName();
                $testName = $alarm->getTestName();
                $textUpdate .= "{$testName} @ {$hostName}  <br>";
                $alarm->setOpsFlags(2);
                $em->persist($alarm);
            }
        }
        $em->flush();

        $ticketROD = $workflow->ticketFromStepId(
            'update',
            $helpdesk->getTicket($ticket),
            [
                'user' =>$this->getUser()->getUsername(),
                'action' => 'added',
                'alarms' => $textUpdate
            ]
        );

        try{
            $ggus_id = $helpdesk->updateTicket($ticketROD);

        }catch (\Exception $exception){
            echo $exception;
        }

        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarms', 'notify');
        $lquery->execute();
        sleep(7);
        return $this->redirectToRoute('ticketAlarmGroupe',['site' => $site, 'ticketId' =>$ticket]);
    }

    /**
     * @Route("/ROD/close-alarm/", name="close-alarm")
     * @param Request $request
     * @return Response
     * @throws \Lavoisier\Exceptions\HTTPStatusException
     */
    public function closeAlarmAction(Request $request){

        $alarmId = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();

        $ticketAlarm= $this->getDoctrine()->getRepository(TicketAlarm::class)->findOneBy([ 'idAlarm' =>$alarmId]);
        if(isset($ticketAlarm)){
            $em->remove($ticketAlarm);
        }

        $alarm= $this->getDoctrine()->getRepository(RodNagiosProblem::class)->find($alarmId);
        $site = $alarm->getSite();
        $name = $alarm->getTestName();
        $em->remove($alarm);
        $em->flush();

        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarms', 'notify');
        $lquery->execute();
        sleep(4);

        $cookieSite = new Cookie('showDetailsSite', $site, strtotime('now + 5 minutes'),'/',null,true,false);
        $cookieType = new Cookie('showDetailsType','alarm',strtotime('now + 5 minutes'),'/',null,true,false);
        $res = new Response();
        $res->headers->setCookie( $cookieSite );
        $res->headers->setCookie( $cookieType );
        $res->send();
        $this->addFlash('success', "<p>Alarm {$name} was successfully close !");

        return $res;
    }

//    /**
//     * @param Request $request
//     * @Route("/ROD/admin/close-alarm", name="admin_close_alarms")
//     * @return Response
//     * @throws \Lavoisier\Exceptions\HTTPStatusException
//     */
//    public function closeAlarmAdminAction(Request $request){
////        dump($request->request->get('id'));
//
////        return $this->redirectToRoute('admin_alarm');
//
//        $alarmId = $request->request->get('id');
//        dump($request->request->get($alarmId));
////        var_dump($alarmInfo);die;
////        $alarmId = $alarmInfo[1];
//        $em = $this->getDoctrine()->getManager();
//
//        $ticketAlarm= $this->getDoctrine()->getRepository(TicketAlarm::class)->findOneBy([ 'idAlarm' =>$alarmId]);
//        if(isset($ticketAlarm)){
//            $em->remove($ticketAlarm);
//            $em->flush();
//        }
//
//        $alarm= $this->getDoctrine()->getRepository(RodNagiosProblem::class)->find($alarmId);
////        $site = $alarm->getSite();
////        $name = $alarm->getTestName();
//        if(isset($alarm)) {
//            $em->remove($alarm);
//            $em->flush();
//        }
//
//        $lavoisierUrl = $this->getParameter('lavoisierUrl');
//        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarms', 'notify');
//        $lquery->execute();
//        sleep(4);
//
//        $res = new Response();
//        $res->send();
//        return $res;
////        echo $alarmInfo;
//
//
//
//
//    }

    /**
     * @Route("ROD/admin", name="admin_alarm")
     */
    public function adminAlarmAction(){

        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        try{

            $lquery = new \Lavoisier\Query($lavoisierUrl, 'rod_admin', 'lavoisier');
            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();
            $admin = $result->getArrayCopy();

        }catch (\Exception $e) {
            $this->addFlash(
                "danger",
                "Plug SLA - Can't get List of ticket  .. Lavoisier call failed - ".$e->getMessage()
            );
        }
        $url = "https://test.ggus.eu/ggus/index.php?mode=ticket_info&ticket_id=";
//        $alarm1 = $admin[0];
//        $alarm2 = $admin[1];

        $helpdesk = $this->get('ggus_helpdesk_ops');


        return $this->render('ROD/admin.html.twig', array(
            'admin' => $admin,
            'url' => $url,
            'serviceHelpdesk' => $helpdesk

        ));



    }

}




