<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 04/07/18
 * Time: 17:29
 */

namespace AppBundle\Controller\PlugSla;

use Symfony\Component\Config\FileLocator;
use AppBundle\Entity\PlugSla\GroupeTicket;
use AppBundle\Entity\PlugSla\InternalComment;
use AppBundle\Entity\PlugSla\SOProvideur;
use AppBundle\Entity\PlugSla\TicketStatus;
use AppBundle\Entity\PlugSla\TypeTicket;
use AppBundle\Form\PlugSla\CommentType;
use AppBundle\Form\PlugSla\GroupeTicketType;
use AppBundle\Form\PlugSla\InternalCommentType;
use AppBundle\Form\PlugSla\ServiceType;
use AppBundle\Form\PlugSla\TicketType;
use Dompdf\Dompdf;
use Dompdf\Options;
use JiraClient\JiraClient;
use JiraClient\Resource\Field;
use JiraClient\Resource\Issue;
use JiraClient\Exception;
use Lavoisier\Exceptions\HTTPStatusException;
use Lavoisier\Hydrators\EntriesHydrator;
use Lavoisier\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use AppBundle\Services\op\Message;



class PlugslaController extends Controller
{
    /**
     * @Route("plugsla/tickets", name="tickets")
     */
    public function ticketsListAction()
    {
        $lavoisierUrl = $this->getParameter('lavoisierUrl');
        try{

            $lquery = new \Lavoisier\Query($lavoisierUrl, 'JiraTicketList', 'lavoisier');
            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();
            $tickets = $result->getArrayCopy();

        }catch (\Exception $e) {
            $this->addFlash(
                "danger",
                "Plug SLA - Can't get List of ticket  .. Lavoisier call failed - ".$e->getMessage()
            );
        }

        $userGroupe= $this->getDoctrine()->getRepository(GroupeTicket::class)->findBy(['dNAuthorized' => $this->getUser()->getDn()]);

        $arrayGroupe = [];
        foreach ($userGroupe as $groupe){
            array_push($arrayGroupe, strtoupper($groupe->getType()->getType()));
        }



        $types = [];

        foreach ($tickets as $value){
            foreach ($value as $ticket){

                $types[$ticket['id']]['allow'] = false;
                $groupExists=0;

                for ($i=1;$i<6;$i++){
                    if ($ticket['SO-'.$i]!=null && $ticket['SO-'.$i]!='null'){

                        $so[$i]=$ticket['SO-'.$i];
                        $data = explode('?', $so[$i]);
                        $type = explode('/',$data[0]);
                        if (isset($type[1])) {
                            $types[$ticket['id']]['type'][] = $type[1];
                            $groupExists++;
                        }

                        foreach ($type as $item) {
                            if(in_array(strtoupper($item),$arrayGroupe) == true){
                                $types[$ticket['id']]['allow'] = true;
                                $types[$ticket['id']]['typeAllow'] = $item;


                            }
                        }
                    }
                }
                if ($groupExists==0)
                    $types[$ticket['id']]['type'][]='NA';
            }
        }




        $nbrNew = (array_key_exists('New',$tickets))? count($tickets["New"]): 0;
        $nbrInProgress = (array_key_exists('In progress',$tickets))? count($tickets["In progress"]): 0;
        $nbrWaitingForRespond = (array_key_exists('Waiting for respond',$tickets))? count($tickets["Waiting for respond"]): 0;
        $nbrApproved = (array_key_exists('Approved',$tickets))? count($tickets["Approved"]): 0;
        $nbrRejected = (array_key_exists('Rejected',$tickets))? count($tickets["Rejected"]): 0;



        $nombreTotal = [
            'incoming' => $nbrNew + $nbrInProgress + $nbrWaitingForRespond,
            'accepted' => $nbrApproved,
            'rejected' => $nbrRejected,
        ];

        return $this->render('plugsla/tickets.html.twig', array(
            'tickets' => $tickets,
            'nombreTotal' => $nombreTotal,
            'types' => $types,
        ));
    }


    public function getProviderList($serviceType) {

        $lavoisierUrl = $this->container->getParameter("lavoisierurl");
        $tabProviders=array();



$group1=['Cloud Compute',' High Throughput Compute',' Cloud Container Compute',' Online Storage',' Archive Storage'];
$group2=['CheckIn'];
$group3=['Attribute Management'];
$group4=['Training Infrastructure'];
$group5=['FitSM'];
$group6=['Applications On Demand'];
$group7=['B2DROP',' B2STAGE',' B2SAFE',' B2HANDLE',' B2NOTE',' B2SHARE',' B2FIND'];
$group8=['Component Metadata Infrastructure'];
$group9=['DARIAH'];
$group10=['Dynamin on Demand Analysis Service'];
$group11=[' ENES Climate Analytics Service'];
$group12=[' Lifewatch Eric Plants Identification App'];
$group13=['OPENCoastS'];
$group14=['WeNMR Suite For Structural Biology'];


                                              $serviceTypeGroups=[
            "HighThroughputCompute"=>"EGI","OnlineStorage"=>"EGI","EGI Cloud container compute BETA"=>"EGI",
            "CloudCompute"=>"Cloud","EGI Cloud compute"=>"Cloud",
            "B2DROP"=>"EUDAT","B2SAFE"=>"EUDAT","B2FIND"=>"EUDAT","B2HANDLE"=>"EUDAT","B2NOTE"=>"EUDAT"
        ];

        if (isset($serviceTypeGroups[$serviceType]))
            $sg=$serviceTypeGroups[$serviceType];
        else
            $sg="Other";

        switch ($sg) {

            case "EGI":
                $lavQuery= new Query($lavoisierUrl,"OPSCORE_SITE");
                $lavQuery->setPath("/e:entries/e:entries[e:entry[@key='PRODUCTION_INFRASTRUCTURE']/text()='Production'][e:entry[@key='CERTIFICATION_STATUS']/text()='Certified']");
                $hydrator = new EntriesHydrator();
                $lavQuery->setHydrator($hydrator);
                $result = $lavQuery->execute();
                $tabProviders = $result->getArrayCopy();
                break;

            case "Cloud":
                $lavQuery= new Query($lavoisierUrl,"OPSCORE_SITE_Fedcloud");
                $lavQuery->setPath("/e:entries/e:entries[e:entry[@key='PRODUCTION_INFRASTRUCTURE']/text()='Production'][e:entry[@key='CERTIFICATION_STATUS']/text()='Certified']");
                $hydrator = new EntriesHydrator();
                $lavQuery->setHydrator($hydrator);
                $result = $lavQuery->execute();
                $tabProviders = $result->getArrayCopy();
                break;

            case "EUDAT":
                $tabProviders=array("EUDAT"=>["CONTACT_EMAIL"=>"eudat@eudat.org","NAME"=>"EUDAT"]);
                break;

            case "Other":
                $tabProviders=array("OTHER"=>["NAME"=> 'No Provider(s) Identified',"CONTACT_EMAIL"=>"marketplace@eosc-hub.eu"]);
                break;
        }


        return $tabProviders;
    }
    /**
     * @param Request $request
     * @param $id
     * @param array
     * @return Response
     * @Route ("plugsla/tickets/edit/{id}", name = "modify_ticket" )
     * @throws HTTPStatusException
     */
    public function ticketShowAction(Request $request, $id)
    {
        $lavoisierUrl = $this->container->getParameter("lavoisierurl");

       $userName=$this->getParameter('jira.username');
       $pwd=$this->getParameter('jira.password');
        $api = new JiraClient('https://jira.eosc-hub.eu/', $userName,$pwd );

        try {
            $ticket = $api->issue()->get($id);

        } catch (\JiraClient\Exception\JiraException $e) {
            // exception processing
            echo $e->getMessage();
        }
        $this->checkPermissionAccessTicket($ticket);
        $confJira=$this->getParameter('jiraItems');


        $ticketData = [
            'dateCreated' => $ticket->getCreated(),
            'dateUpdated' => $ticket->getUpdated(),
            'subject' => $ticket->getSummary(),
            'description' => $ticket->getDescription(),
            'id' => $id];

            $status=$ticket->getStatus();

        foreach ($confJira as $key => $item) {
            if ($item['visible'] == true) {
                $field = (int)$item["field"];
                $ticketData[$key] = $ticket->getCustomField($field);
            }
        }


        $comments = $ticket->getComments()->getList();
        $formTicket = $this->createForm(TicketType::class, $ticketData);
        $formTicket->handleRequest($request);

        if ($formTicket->isSubmitted() && $formTicket->isValid()) {

            $data = $formTicket->getData();

            $ticket->update()
                ->field(Field::SUMMARY, $data['subject'])
                ->field(Field::DESCRIPTION, $data['description'])
                ->execute();


                foreach ($confJira as $key => $item) {
                    try {
                        $ticket->update()->customField($item["field"], $data[$key])->execute();
                    } catch (Exception\JiraException $e) {
                        echo "problem with Field :" . $item['field'] . " : " . $e->getMessage();
                    }
                }




            $lquery = new Query($lavoisierUrl, 'JiraTicketList', 'notify');
            $lquery->execute();

            return $this->redirectToRoute('modify_ticket',[
                'id' => $id
            ]);
        }

        $formComment = $this->createForm(CommentType::class);
        $formInternalComment = $this->createForm(InternalCommentType::class);

        $internalComments= $this->getDoctrine()->getRepository(InternalComment::class)->findBy(['idTicket' => $id], ['dateCreate' => 'desc']);
        $statusAllow = $this->getStatusAllow($ticket);

        return $this->render('plugsla/modify.html.twig', array(
            'formTicket' => $formTicket->createView(),
            'formComment' => $formComment->createView(),
            'formInternalComment' => $formInternalComment->createView(),
            'comments' => $comments,
            'internalComments' => $internalComments,
            'statusAllow' => $statusAllow,
            'ticket' => $ticket,
            'status'=>$status->getName(),
            'id' => $id

        ));

    }


    /**
     * ajax
     * @Route("/plugsla/add-internal-comment", name="add_internal_comment")
     * @param Request $request
     * @param InternalComment $internalComment
     * @return Response
     * @throws \Exception
     */
    public function addInternalComment(Request $request){

        $data = $request->get('appbundle_plugsla_internalcomment');
        $idTicket = $request->get('idTicket');
        $internalComment = new InternalComment();
        $internalComment->setBody($data['body']);
        $internalComment->setDatecreate(new \DateTime('now'));
        $internalComment->setIdTicket($idTicket);

        $em = $this->getDoctrine()->getManager();
        $em->persist($internalComment);
        $em->flush();

        $internalComments= $this->getDoctrine()->getRepository(InternalComment::class)->findBy(['idTicket' => $idTicket ], ['dateCreate' => 'desc']);

        return $this->render('plugsla/internalComments.html.twig',[
            'internalComments' => $internalComments,
        ]);
    }

    /**
     * ajax
     * @Route("/plugsla/add-comment", name="add_comment_jira")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addComment(Request $request){

        $data = $request->get('comment');
        $idTicket = $request->get('idTicket');

        $api = new JiraClient('https://jira.eosc-hub.eu/', $this->getParameter('jira.username'), $this->getParameter('jira.password'));
        try {
            $ticket = $api->issue()->get($idTicket);
            $ticket->addComment($data['body']);

        } catch (\JiraClient\Exception\JiraException $e) {
            // exception processing
            echo $e->getMessage();
        }

        $ticketUpdate =  $api->issue()->get($idTicket);
        $comments = $ticketUpdate->getComments()->getList();

        return $this->render('plugsla/Comments.html.twig',[
            'comments' => $comments,
        ]);
    }


    /**
     * get the PDF of ticket by id
     * @Route("/ticketTOPDF/{id}/", name="tickettoPDF")
     * @param $id
     *
     */
    public function getPDFTicketAction($id){

        $api = new JiraClient('https://jira.eosc-hub.eu/', $this->getParameter('jira.username'), $this->getParameter('jira.password'));

        try {
            $ticket = $api->issue()->get($id);
        } catch (\JiraClient\Exception\JiraException $e) {
            echo $e->getMessage();
        }

        $soSla = $this->getTabsoSla();
        $result = [];
        foreach ($soSla as $clef => $so){
            $so_x = $ticket->getCustomField($so);
            if(!empty($so_x)){
                $soProvideur = $this->getDoctrine()->getRepository(SOProvideur::class)->findBy(['idTicket' => $id, 'sO' => $clef ]);

                $data = explode('?', $so_x);
                $data1 = explode('&',$data[1]);
                $type = explode('/',$data[0]);

                $attributeSla = [];
                foreach ($data1 as $item){
                    $temp = explode('=',$item);
                    $result[$clef]['data'][$temp[0]]['value'] = $temp[1];
                    array_push($attributeSla,$temp[0]);

                    preg_match('/^\d+$/',$temp[1],$temp[2]);
                    $result[$clef]['data'][$temp[0]]['type'] = (empty($temp[2][0]))? 'text' : 'number';

                    $result[$clef]['data'][$temp[0]]['percentage'] = 0;
                    if($result[$clef]['data'][$temp[0]]['type'] === 'number' && $temp[0] != 'NumberOfDays' ){
                        foreach ($soProvideur as $provideur){
                            $result[$clef]['data'][$temp[0]]['percentage']+= $provideur->getData()[$temp[0]];
                        }
                    }
                }
                $result[$clef]['soProvideur'] = $soProvideur;
                $result[$clef]['serviceType'] = $type;
                $result[$clef]['attributeSla'] = $attributeSla;
            }
        }

        $html = $this->renderView( ":plugsla:pdf.html.twig",[
            'ticket' => $ticket,
            'result' => $result
        ]);



//        $footer = $this->renderView(':plugsla:footer.html.twig');

        $option = new Options();
        $option->set('isRemoteEnabled', TRUE);
        $option->setIsHtml5ParserEnabled(true);
        $option->setChroot('/app/Resources/views/plugsla/');
        $option->setDefaultPaperSize('a4');
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed'=> TRUE
            ]
        ]);

        $pdf = new Dompdf($option);

        $pdf->setHttpContext($context);
        $pdf->loadHtml($html);
        $pdf->render();

        // Output the generated PDF to Browser
        return new Response($pdf->stream($ticket->getKey()));


    }

    /**
     * use api JIRA directly
     * @Route("/api/JIRA/", name="apiJira")
     */
    public function apiJiraAction(){

        $api = new JiraClient('https://jira.eosc-hub.eu/', $this->getParameter('jira.username'), $this->getParameter('jira.password'));

        try {
            // Get an existing issue
//            var_dump($api->issue()->get('EOSCSO-2')->getLabels());

            $ticket = $api->issue()->get('EOSCSOSTAGING-13');
            var_dump($ticket);

        } catch (\JiraClient\Exception\JiraException $e) {
            // exception processing
            echo $e->getMessage();
        }


//        $lavoisierUrl = $this->getParameter('lavoisierUrl');
//        try{
//
//            $lquery = new \Lavoisier\Query($lavoisierUrl, 'JiraTicketList', 'lavoisier');
//            $hydrator = new EntriesHydrator();
//            $lquery->setHydrator($hydrator);
//            $result = $lquery->execute();
//            $tickets = $result->getArrayCopy();
//
//        }catch (\Exception $e) {
//            $this->addFlash(
//                "danger",
//                "ROD Dashboard- Can't get List of Site by NGI  .. Lavoisier call failed - ".$e->getMessage()
//            );
//        }
//
//        var_dump($tickets);



    }

    /**
     * @param $id
     * @param array
     * @return Response
     * @Route ("plugsla/tickets/status/{id}/{status}", name = "status_ticket" )
     * @throws HTTPStatusException
     */
    public function ticketStatusAction($id, $status)
    {
        $lavoisierUrl = $this->container->getParameter("lavoisierurl");
        $api = new JiraClient('https://jira.eosc-hub.eu/', $this->getParameter('jira.username'), $this->getParameter('jira.password'));

        try {
            $ticket = $api->issue()->get($id);

        } catch (\JiraClient\Exception\JiraException $e) {
            // exception processing
            echo $e->getMessage();
        }
        $ticket->transition()->execute($status);

        $lquery = new Query($lavoisierUrl, 'JiraTicketList', 'notify');
        $lquery->execute();

        return $this->redirectToRoute('modify_ticket', array(
            'id' => $id
        ));

    }

    public function getAllTeamsSortedByDescBudget()
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.type', 'DESC');

        return $qb->getQuery()->getResult();
    }


    /**
     *
     * @Route("/plugsla/UserGroupe/", name="userGroupe")
     */
    public function UserGroupeAction(Request $request){

        if ($this->getUser()->isSuUser() == true){
            $groupeTicket= $this->getDoctrine()->getRepository(GroupeTicket::class)->findAll();


            $formGroupe = $this->createForm(GroupeTicketType::class);

            $formGroupe->handleRequest($request);

            $formService = $this->createForm(ServiceType::class);
            $formService->handleRequest($request);

            if ($formService->isSubmitted() && $formService->isValid()){
                $service = new TypeTicket();

                $info = $formService->getData();
                $value = $info->getType();
                $test = $this->getDoctrine()->getRepository(TypeTicket::class)->findOneBy(['type' => $value]);

                if($test != $value){

                    $service->setType($info->getType());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($service);
                    $em->flush();

                    return $this->redirectToRoute('userGroupe');

                }else{
                    return $this->redirectToRoute('userGroupe');

                }

            }

            if ($formGroupe->isSubmitted() && $formGroupe->isValid()) {

                $groupUser = new GroupeTicket();

                $groupe = $formGroupe->getData();
                $groupUser->setIdUser($groupe->getdNAuthorized()->getId());
                $groupUser->setDNAuthorized($groupe->getdNAuthorized()->getDn());
                ($groupUser->setType($groupe->getType()));

                $em = $this->getDoctrine()->getManager();
                $em->persist($groupUser);
                $em->flush();

                return $this->redirectToRoute('userGroupe');
            }

            return $this->render('plugsla/userGroupe.html.twig',[
                'groupe' => $groupeTicket,
                'formGroupe' =>$formGroupe->createView(),
                'formService' =>$formService->createView(),
            ]);

        }else{
            throw new AccessDeniedHttpException();
        }
    }

    /**
     *
     * @Route("/plugsla/UserGroupe/{id}", name="delete_user_group")
     */
    public function deleteUserGroupAction(Request $request){

        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $groupUser = $this->getDoctrine()->getRepository(GroupeTicket::class)->findOneBy(['id' => $id]);
        $em->remove($groupUser);
        $em->flush();


        return $this->redirectToRoute('userGroupe');
    }

    /**
     *
     * @Route("/plugsla/detail/{idTicket}", name="detail_ticket")
     */
    public function detailTicketAction(Request $request, $idTicket){

        $api = new JiraClient('https://jira.eosc-hub.eu/', $this->getParameter('jira.username'), $this->getParameter('jira.password'));

        try {
            $ticket = $api->issue()->get($idTicket);
        } catch (\JiraClient\Exception\JiraException $e) {
            echo $e->getMessage();
        }

        $this->checkPermissionAccessTicket($ticket);
        $soSla = $this->getTabsoSla();

        $result = [];
        foreach ($soSla as $clef => $so){

            $so_x = $ticket->getCustomField($so);
//            var_dump($so_x);

            if(!empty($so_x)){
                $soProvideur = $this->getDoctrine()->getRepository(SOProvideur::class)->findBy(['idTicket' => $idTicket, 'sO' => $clef ]);

                $data = explode('?', $so_x);
                $data1 = explode('&',$data[1]);
                $type = explode('/',$data[0]);


                $attributeSla = [];
                foreach ($data1 as $item){
                    $temp = explode('=',$item);
                    $result[$clef]['data'][$temp[0]]['value'] = $temp[1];
                    array_push($attributeSla,$temp[0]);
                    preg_match('/^\d+$/',$temp[1],$temp[2]);

                    $result[$clef]['data'][$temp[0]]['type'] = (empty($temp[2][0]))? 'text' : 'number';

                    $result[$clef]['data'][$temp[0]]['percentage'] = 0;
                    if($result[$clef]['data'][$temp[0]]['type'] === 'number' && $temp[0] != 'NumberOfDays' ){
                        foreach ($soProvideur as $provideur){
                            $result[$clef]['data'][$temp[0]]['percentage']+= $provideur->getData()[$temp[0]];
                        }
                    }
                }



                $result[$clef]['soProvideur'] = $soProvideur;
                $result[$clef]['serviceType'] = $type;
                $result[$clef]['attributeSla'] = implode("&",$attributeSla);
                $result[$clef]['provideur'] = (isset($type[1]))? $this->getProviderList($type[1]): null;

                $result[$clef]['data']['start']['value'] = date_create_from_format('d/m/Y', $result[$clef]['data']['start']['value']);
            }

        }



        $statusAllow = $this->getStatusAllow($ticket);

        return $this->render('plugsla/detailTicket.html.twig',[
            'ticket' => $ticket,
            'result' => $result,
            'statusAllow' => $statusAllow
        ]);
    }

    /**
     *
     * @Route("/plugsla/refresh", name="refresh")
     */
    public function refreshListTicketAction(){

        $lavoisierUrl = $this->container->getParameter("lavoisierurl");
        $lquery = new Query($lavoisierUrl, 'JiraTicketList', 'notify');
        $lquery->execute();
        sleep(3);

        return $this->redirectToRoute('tickets');
    }

    /**
     *
     * @Route("/plugsla/add-provideur", name="add_provideur")
     * @Method("POST")
     * @param Request $request
     */
    public function addProvideurAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $soProvideur = new SOProvideur();

        $idTicket = $request->get('idTicket');
        $so = $request->get('SO');
        $mail = $request->get('mail');
        $provideur = $request->get('provideur');
        $attributeSla = $request->get('attributeSla');
        $ava = $request->get('ava');
        $rel = $request->get('rel');
        $end = $request->get('end');

        $soProvideur->setIdTicket($idTicket);
        $soProvideur->setSO($so);
        $soProvideur->setMail($mail);
        $soProvideur->setProvideur($provideur);

        $result = explode('&',$attributeSla);
        $data = [];
        foreach ($result as $key){
            if( $key != "NumberOfDays" ){
                $value = $request->get(str_replace(' ','_', $key));
                $data[$key]=$value;
            }
        }
        $data['ava'] = $ava;
        $data['rel'] = $rel;
        $data['end'] = $end;

        $soProvideur->setData($data);

        $em->persist($soProvideur);
        $em->flush();

        return $this->redirectToRoute('detail_ticket',[
            'idTicket' => $idTicket
        ]);
    }

    /**
     *
     * @Route("/plugsla/add-comment", name="add_comment")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addCommentAction(Request $request){

        $idTicket = $request->get('idTicket');
        $commentaire = $request->get('commentaire');

        $api = new JiraClient('https://jira.eosc-hub.eu/', $this->getParameter('jira.username'), $this->getParameter('jira.password'));
        try {
            $api->issue()->addComment($idTicket,$commentaire);
        } catch (\JiraClient\Exception\JiraException $e) {
            echo $e->getMessage();
        }

        return $this->redirectToRoute('detail_ticket',[
            'idTicket' => $idTicket
        ]);
    }

    /**
     *
     * @Route("/plugsla/contact-provideur", name="contact_provideur")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function contactProvideurAction(Request $request){

        $idTicket = $request->get('idTicket');
        $mailContact = $request->get('mail');
        $subject = $request->get('subject');
        $message = $request->get('message');

        $mail = new Message($this->container, $subject, $message, $mailContact);
        $this->get('mailer')->send($mail->getMail());

        return $this->redirectToRoute('detail_ticket',[
            'idTicket' => $idTicket
        ]);
    }

    public function getStatusAllow($ticket){
        $api = new JiraClient('https://jira.eosc-hub.eu/', $this->getParameter('jira.username'), $this->getParameter('jira.password'));

        $transitions = $api->issue()->getTransitions($ticket->getKey());

        $status = [
            'In progress' => [
                'btn' => 'btn-primary',
                'icon' => 'fa-spinner',
                'title' => '',
                'label' => 'In progress'
            ],
            'Waiting for respond' => [
                'btn' => 'btn-secondary',
                'icon' => 'fa-pause',
                'id' => 21,
                'title' => '',
                'label' => 'Waiting for respond'
            ],
            'Done' => [
                'btn' => 'btn-success',
                'icon' => 'fa-check-circle',
                'id' => 61,
                'title' => 'Accept the Service Order',
                'label' => 'Approved'
            ],
            'Approved' => [
                'btn' => 'btn-success',
                'icon' => 'fa-check-circle',
                'id' => 41,
                'title' => 'Accept the Service Order',
                'label' => 'Approved'
            ],
            'Rejected' => [
                'btn' => 'btn-danger',
                'icon' => 'fa-times-circle',
                'id' => 51,
                'title' => '',
                'label' => 'Rejected'
            ],

        ];

        if ($ticket->getStatus()->getName() == 'Waiting for respond'){
            $status['In progress']['id'] = 31;
        }else{
            $status['In progress']['id'] = 11;
        }

        $statusAllow =[];
        foreach ($transitions as $transition){
            $statusAllow[$transition->getName()] = $status[$transition->getName()];
        }

        return $statusAllow;
    }

    public function checkPermissionAccessTicket(Issue $ticket){

        $userGroupe= $this->getDoctrine()->getRepository(GroupeTicket::class)->findBy(['dNAuthorized' => $this->getUser()->getDn()]);

        $arrayGroupe = [];
        foreach ($userGroupe as $groupe){
            array_push($arrayGroupe, strtoupper($groupe->getType()->getType()));
        }

        $soSla = $this->getTabsoSla();
        $allow = false;
        foreach ($soSla as $clef => $so){

            $so_x = $ticket->getCustomField($so);
            if(!empty($so_x)){

                $data = explode('?', $so_x);
                $type = explode('/',$data[0]);

                foreach ($type as $item) {
                    if(in_array(strtoupper($item),$arrayGroupe)){
                        $allow = true;
                    }
                }
            }
        }

        if ($allow == false){
            throw new AccessDeniedHttpException();
        }

        return true;
    }

    public function getTabsoSla(){
        return [
            'SO-1' => 10400,
            'SO-2' => 10401,
            'SO-3' => 10403,
            'SO-4' => 10404,
            'SO-5' => 10405
        ];
    }


    /**
     * @Route("plugsla/admin")
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

        $alarm1 = $admin[0];
        $alarm2 = $admin[1];
//        dump($alarm1);
//        dump($alarm2);die;
//
//        foreach ($alarm1 as $key){
//            dump($key);die;
//        }

//        $alarmRodTicket = $this->getDoctrine()->getRepository(TicketAlarm::class)->findBy(['id' => $admin[0]);




        return $this->render('plugsla/admin.html.twig', array(
            'admin' => $admin,

        ));



    }

    /**
     * @param Request $request
     * @Route("plugsla/admin/deleteAlarm", name="plugsla_alarm")
     */
    public function deleteAlarmAction(Request $request){

        dump($request);die;

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


        return $this->render('plugsla/admin.html.twig', array(
            'admin' => $admin,
        ));

    }

}