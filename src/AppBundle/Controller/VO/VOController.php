<?php

namespace AppBundle\Controller\VO;

use AppBundle\Entity\User;
use AppBundle\Entity\VO\Vo;
use AppBundle\Entity\VO\VoHeader;
use AppBundle\Entity\VO\VoAcknowledgmentStatements;
use AppBundle\Entity\VO\VoContactHasProfile;
use AppBundle\Entity\VO\VoContacts;
use AppBundle\Entity\VO\VoMailingList;
use AppBundle\Entity\VO\VoRessources;
use AppBundle\Entity\VO\VoVomsGroup;
use AppBundle\Entity\VO\VoVomsServer;
use AppBundle\Entity\VO\VoYearlyValidation;
use AppBundle\Form\VO\ManageAupFileType;
use AppBundle\Form\VO\UserTrackingType;
use AppBundle\Form\VO\VoType;
use AppBundle\Services\AUPFileHandler;
use AppBundle\Services\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Model\VO\ModelVO;
use AppBundle\Form\VO\VoHeaderType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use DateTime;

use Lavoisier\Query;
use Lavoisier\Hydrators\EntriesHydrator;


use AppBundle\Services\TicketingSystem\Workflow\Loader;
use AppBundle\Services\TicketingSystem\HelpDesk\OpsHelpdesk;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;


use AppBundle\Model\VO\VoChecker;
use AppBundle\Entity\VO\VoReport;

use AppBundle\Services\op\Message;
use AppBundle\Services\OpsLavoisier\Services\EntityService;


/**
 * Class VOController
 * @package AppBundle\Controller
 * @Route("/vo")
 */
class VOController extends Controller
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
     * get List of VO (Waiting VO/ My VO/ Other VO/ Leaving VO)
     * @Route("/", name="registerUpdate")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerUpdateAction()
    {


        $this->user = $this->getUser();

        //--------------------------------------------------------------------------------//
        //Waiting validation vonotify/VoWaitingList
        //--------------------------------------------------------------------------------//
        $isSuUser = $this->user->isSuUser();
        try {
            $lavoisierUrl = $this->container->getParameter("lavoisierurl");
            $lquery = new Query($lavoisierUrl, 'VoWaitingList', 'lavoisier');
            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();
            $voWaitingList = $result->getArrayCopy();
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $this->addFlash(
                "danger",
                "Register/Update - Can't get List of Incoming VO's... Lavoisier call failed - ".$e->getMessage()
            );

            return $this->render(":vo:registerUpdate.html.twig", array('isSuUser' => $isSuUser));
        }
        // @codeCoverageIgnoreEnd

        $voWaitingReal = array();

        $arrayDisiciplines = array();

        $doctrine = $this->getDoctrine();

        //get list of scopes
        $arrayscope = $doctrine->getRepository("AppBundle:VO\VoScope")->findAll();

        if (count($voWaitingList) != 0) {
            foreach ($voWaitingList as $voWaiting) {
                //get only the list of VO that user can modify
                if ($this->user->canModifyVO($voWaiting['name'])) {
                    $voWaitingReal[] = $voWaiting;
                    //get the disciplines by VO
                    try {
                        $lquery2 = new Query($lavoisierUrl, 'VoDisciplinesByID', 'lavoisier');
                        $lquery2->setMethod('POST');
                        $lquery2->setPostFields(array('voId' => $voWaiting['serial']));
                        $result = $lquery2->execute();

                        $arrayDisiciplines[$voWaiting['serial']] = json_decode(
                            json_encode(simplexml_load_string($result)),
                            true
                        );

                        //@codeCoverageIgnoreStart
                    } catch (\Exception $e) {
                        $this->addFlash(
                            "danger",
                            "Register/Update - Can't get List of Vo Disicplines .. Lavoisier call failed - ".$e->getMessage(
                            )
                        );

                        return $this->render(":vo:registerUpdate.html.twig", array('isSuUser' => $isSuUser));
                    }
                    //@codeCoverageIgnoreEnd
                }
            }
        }


        //--------------------------------------------------------------------------------//
        //My VO
        //--------------------------------------------------------------------------------//
        //lavoisier call
        try {
//            $lquery = new Query($lavoisierUrl, 'VoRoles', 'lavoisier');
//            $lquery->setPath('/e:entries/e:entries[e:entry[@key="dn"]="' . $this->user->getDn() . '"]');


            $lquery = new Query($lavoisierUrl, 'VoRolesDN', 'lavoisier');
            $lquery->setMethod('POST');
            $lquery->setPostFields(array("DN" => $this->user->getDn()));



            $hydrator = new EntriesHydrator();

            $lquery->setHydrator($hydrator);

            $result = $lquery->execute();
            $myVoList = $result->getArrayCopy();

            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $this->addFlash(
                "danger",
                "Register/Update - Can't get List of User Vo with dn .. Lavoisier call failed - ".$e->getMessage()
            );

            return $this->render(":vo:registerUpdate.html.twig", array('isSuUser' => $isSuUser));
        }
        //@codeCoverageIgnoreEnd

        //--------------------------------------------------------------------------------//
        //VO List Other
        //--------------------------------------------------------------------------------//
        if (!isset($this->getVoListOther()["error"])) {

            $voOtherList = $this->getVoListOther()["voListOther"];
            $linkVOListOther = $this->getVoListOther()["voListOtherLink"];
            $linkVOFull = $this->getVoListOther()["voFullLink"];
        } else {
            $this->addFlash(
                "danger",
                "Register/Update -  Can't get list of Vo in production ".$this->getVoListOther()["error"]
            );

            return $this->render(":vo:registerUpdate.html.twig", array('isSuUser' => $isSuUser));
        }

        //--------------------------------------------------------------------------------//
        //VO List Removed
        //--------------------------------------------------------------------------------//
        try {
            $lquery = new Query($lavoisierUrl, 'VoEntries', 'lavoisier');
            $lquery->setPath(
                "/e:entries/e:entries[e:entry[@key='status']/text()='Leaving' or e:entry[@key='status']/text()='Suspended' or e:entry[@key='status']/text()='Deleted' ]"
            );
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();
            $voListRemoved = $result->getArrayCopy();
            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $this->addFlash(
                "danger",
                "Register/Update - Can't get List of Removed Vo .. Lavoisier call failed - ".$e->getMessage()
            );

            return $this->render(":vo:registerUpdate.html.twig", array('isSuUser' => $isSuUser));
        }

        //@codeCoverageIgnoreEnd

        return $this->render(
            ':vo:registerUpdate.html.twig',
            array(
                "myVoList" => $myVoList,
                "voListOther" => $voOtherList,
                "voListRemoved" => $voListRemoved,
                "voListOtherLink" => $linkVOListOther,
                "voFullLink" => $linkVOFull,
                "waitingVo" => $voWaitingReal,
                "waitingdisciplines" => $arrayDisiciplines,
                "waitingscopes" => $arrayscope,
                'isSuUser' => $isSuUser,
            )
        );
    }

    /**
     * view with list of all vo in production
     * @Route("/search", name="voList")
     */
    public function voSearchAction()
    {
        //--------------------------------------------------------------------------------//
        //VO List Other
        //--------------------------------------------------------------------------------//
        if (!isset($this->getVoListOther()["error"])) {
            $voOtherList = $this->getVoListOther()["voListOther"];
            $linkVOListOther = $this->getVoListOther()["voListOtherLink"];
            $linkVOFull = $this->getVoListOther()["voFullLink"];

            return $this->render(
                ":vo:voList.html.twig",
                array(
                    "voListOther" => $voOtherList,
                    "voListOtherLink" => $linkVOListOther,
                    "voFullLink" => $linkVOFull,
                    'isSuUser' => false,
                )
            );
        } else {
            $this->addFlash(
                "danger",
                "Vo Search -  Can't get list of Vo in production ".$this->getVoListOther()["error"]
            );

            return $this->render(":vo:voList.html.twig");
        }
    }


    /**
     * Update vo waiting scope
     * @Route("/updateScope", name="updateScope")
     */
    public function updateScopeWaitingVOAction(Request $request)
    {

        //get vo serial and scope id
        $serial = $request->get("serial");
        $scopeId = $request->get("scopeId");


        if ($serial == "" || $serial == null) {
            $response = new Response(
                json_encode(array("status" => "Udpating Incoming VO scope failed -- No VO serial parameter...")), 500
            );

            return $response;
        }

        if ($scopeId == "" || $scopeId == null) {
            $response = new Response(
                json_encode(array("status" => "Udpating Incoming VO scope failed -- No scope id parameter...")), 500
            );

            return $response;
        }

        $voModel = new ModelVO($this->container, $serial);

        //update scope in vo header
        $status = $voModel->updateScope($scopeId);


        if ($status != "Scope updated") {
            $response = new Response(json_encode(array("status" => $status)), 500);
        } else {
            //if ok notify voWaitingList view
            try {
                $lavoisierUrl = $this->container->getParameter("lavoisierurl");
                $lquery = new Query($lavoisierUrl, 'VoWaitingList', 'notify');
                $lquery->execute();
                $lquery = new Query($lavoisierUrl, 'VoStatusScope', 'notify');
                $lquery->execute();
                //@codeCoverageIgnoreStart
            } catch (\Exception $e) {
                $response = new Response(json_encode(array("status" => $e->getMessage())), 500);

                return $response;
            }
            //@codeCoverageIgnoreEnd

            $response = new Response(json_encode(array("status" => $status)), 200);
        }

        return $response;
    }

    /**
     * reject vo waiting
     * @Route("/rejectVo", name="rejectVo")
     */
    public function rejectVoAction(Request $request)
    {
        $serial = $request->get("serial");
        $cause = $request->get("cause");

        if ($serial == null) {
            $this->addFlash("danger", "Can not execute rejection of VO : missing serial");

            return new Response("Rejection of VO failed : missing serial", 500);
        }

        if ($cause == null) {
            $this->addFlash("danger", "Can not execute rejection of VO : missing cause");

            return new Response("Rejection of VO failed : missing cause", 500);
        }

        //get vo and voHeader related to serial
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("serial" => $serial));
        /** @var  $voHeader \AppBundle\Entity\VO\VoHeader */
        $voHeader = $this->getDoctrine()->getRepository("AppBundle:VO\VoHeader")->findOneBy(
            array("id" => $vo->getHeaderId())
        );


        //get the previous status
        $oldStatusId = $voHeader->getStatusId();
        /** @var  $oldStatus \AppBundle\Entity\VO\VoStatus */
        $oldStatus = $this->getDoctrine()->getRepository("AppBundle:VO\VoStatus")->findOneBy(
            array("id" => $oldStatusId)
        );


        //update scope in vo header
        $voModel = new ModelVO($this->container, $serial);
        $status = $voModel->updateStatus(7);
        //get the new status
        /** @var  $newStatus \AppBundle\Entity\VO\VoStatus */
        $newStatus = $this->getDoctrine()->getRepository("AppBundle:VO\VoStatus")->findOneBy(
            array("id" => $voHeader->getStatusId())
        );

        //get ggus service

//        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/Vo/VOM');
//        $loader = new Loader($wf_paths);
//        $containerBd = new ContainerBuilder();
//        $loader->load(array(), $containerBd);
//
//        $workflow = $containerBd->get("workflow_vo");
//        $helpdesk = $this->get('ggus_helpdesk_ops');
//
//        $helpdesk->setLavoisierNotification(false);


        //if update failed, show danger flash message
        if ($status != "Status updated") {
            $this->addFlash('danger', '[VO '.$vo->getName().'] - '.$status);

            return new Response("Rejection of VO ".$vo->getName()." failed : ".$status, 500);

        } else {
            //if ok notify lavoisier related views
            $lavoisierUrl = $this->container->getParameter("lavoisierUrl");
            $lquery = new Query($lavoisierUrl, 'VoWaitingList', 'notify');
            $lquery->execute();
            $lquery = new Query($lavoisierUrl, 'VoStatusScope', 'notify');
            $lquery->execute();
            $lquery = new Query($lavoisierUrl, 'VoEntries', 'notify');
            $lquery->execute();

            //get the ggus ticket url

                $env = $this->get("kernel")->getEnvironment();
                if ($env == 'test') {
                    $permaLink = 'https://train.ggus.eu/mode=ticket_info&ticket_id='.$vo->getGgusTicketId();
                } else {
                    $permaLink = 'https://ggus.eu/?mode=ticket_info&ticket_id='.$vo->getGgusTicketId();
            }



            //send notification mail
            $mailer = new mailer();

            $mailer->notifyForStatusChange(
                $this->container,
                $vo->getSerial(),
                $oldStatus,
                $newStatus,
                true,
                $permaLink,
                $cause
            );
            //show success flash message
            $this->addFlash(
                'success',
                '[VO '.$vo->getName(
                ).'] - <strong>Registration of this VO has been rejected.</strong> <br> <strong>Explanation : </strong>'.$cause
            );
            //show notification changed VO flash message
            $this->addFlash($newStatus->getStatus().'VO', 'Rejected VO in tab');

        }

        return new Response("OK");

    }

    /**
     * Update vo waiting scope
     * @Route("/updateStatusVo/{serial}/{statusId}/{isRejected}/{cause}", name="updateStatusVo")
     * @Method({"GET", "POST"})
     */
    public function updateStatusVoAction($serial, $statusId, $isRejected = false, $cause = null)
    {

        //get vo and voHeader related to serial
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("serial" => $serial));
        /** @var  $voHeader \AppBundle\Entity\VO\VoHeader */
        $voHeader = $this->getDoctrine()->getRepository("AppBundle:VO\VoHeader")->findOneBy(
            array("id" => $vo->getHeaderId())
        );


        //get the previous status
        $oldStatusId = $voHeader->getStatusId();
        /** @var  $oldStatus \AppBundle\Entity\VO\VoStatus */
        $oldStatus = $this->getDoctrine()->getRepository("AppBundle:VO\VoStatus")->findOneBy(
            array("id" => $oldStatusId)
        );


        //update scope in vo header
        $voModel = new ModelVO($this->container, $serial);
        $status = $voModel->updateStatus($statusId);
        //get the new status
        /** @var  $newStatus \AppBundle\Entity\VO\VoStatus */
        $newStatus = $this->getDoctrine()->getRepository("AppBundle:VO\VoStatus")->findOneBy(
            array("id" => $voHeader->getStatusId())
        );



        //if update failed, show danger flash message
        if ($status != "Status updated") {
            $this->addFlash('danger', '[VO '.$vo->getName().'] - '.$status);

        } else {
            //if ok notify lavoisier related views
            $lavoisierUrl = $this->container->getParameter("lavoisierurl");
            $lquery = new Query($lavoisierUrl, 'VoWaitingList', 'notify');
            $lquery->execute();
            $lquery = new Query($lavoisierUrl, 'VoStatusScope', 'notify');
            $lquery->execute();
            $lquery = new Query($lavoisierUrl, 'VoEntries', 'notify');
            $lquery->execute();



            $env = $this->get("kernel")->getEnvironment();
            if ($env == 'test') {
                $permaLink = 'https://train.ggus.eu/mode=ticket_info&ticket_id='.$vo->getGgusTicketId();
            } else {
                $permaLink = 'https://ggus.eu/?mode=ticket_info&ticket_id='.$vo->getGgusTicketId();
            }

            //send notification mail
            $mailer = new mailer();

            if ($isRejected) {
                $mailer->notifyForStatusChange(
                    $this->container,
                    $vo->getSerial(),
                    $oldStatus,
                    $newStatus,
                    true,
                    $permaLink,
                    $cause
                );
                //show success flash message
                $this->addFlash(
                    'success',
                    '[VO '.$vo->getName().'] - <strong>Registration of this VO has been rejected.</strong>'
                );
                //show notification changed VO flash message
                $this->addFlash($newStatus->getStatus().'VO', 'Rejected VO in tab');

            } else {
                $mailer->notifyForStatusChange(
                    $this->container,
                    $vo->getSerial(),
                    $oldStatus,
                    $newStatus,
                    false,
                    $permaLink
                );
                //show success flash message
                $this->addFlash(
                    'success',
                    '[VO '.$vo->getName().'] - '.$status.' from <strong>'.$oldStatus->getStatus(
                    ).'</strong> to <strong>'.$newStatus->getStatus().'</strong>'
                );
                //show notification changed VO flash message
                $this->addFlash($newStatus->getStatus().'VO', 'New '.$newStatus->getStatus().' VO in tab');

            }
        }


        sleep(5);

        return $this->redirect($this->generateUrl("registerUpdate"));
    }


    /**
     * Update vo waiting scope to production
     * @Route("/setVoToProduction/{serial}/{statusId}", name="setVoToProduction")
     */
    public function setVoToProductionAction($serial, $statusId)
    {


        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("serial" => $serial));

        /** @var  $voHeader \AppBundle\Entity\VO\VoHeader */
        $voHeader = $this->getDoctrine()->getRepository("AppBundle:VO\VoHeader")->findOneBy(
            array("id" => $vo->getHeaderId())
        );

        $oldStatusId = $voHeader->getStatusId();

        /** @var  $oldStatus \AppBundle\Entity\VO\VoStatus */
        $oldStatus = $this->getDoctrine()->getRepository("AppBundle:VO\VoStatus")->findOneBy(
            array("id" => $oldStatusId)
        );

        $voModel = new ModelVO($this->container, $serial);


        // try to close follow-up GGUS ticket
        if ($statusId == 2) {

            try {
                // DEPENDING ON GGUS EXTERNAL METHODS... CAN'T BE TESTED NOWADAY
                if ($this->get("kernel")->getEnvironment() != "test") {
                    //@codeCoverageIgnoreStart

                    //--------------------------------------------------------------------------------------------------------//
                    //                                      I : GET THE WORKFLOW VO SERVICE

                    //1) get the directory to find service.xml
                    $wf_paths = $this->container->get('kernel')->locateResource(
                        '@AppBundle/Services/Workflow/VoAdmin/VOM'
                    );

                    //2) load yml files that service is depending on (vomshelprequest and closeregistration)
                    $loader = new Loader($wf_paths);
                    $containerBd = new ContainerBuilder();
                    $loader->load(array(), $containerBd);

                    //3) get service
                    $workflow = $containerBd->get("workflow_vo");
                    //--------------------------------------------------------------------------------------------------------//

                    //--------------------------------------------------------------------------------------------------------//
                    //                                      II : GET THE GGUS SERVICE

                    $helpdesk = $this->get('ggus_helpdesk_ops');
                    $helpdesk->setLavoisierNotification(false);

                    //get the ticket to close
                    $followUpGgusTicket = $vo->getGgusTicketId();

                    //--------------------------------------------------------------------------------------------------------//

                    //--------------------------------------------------------------------------------------------------------//
                    //                                      III : CLOSE TICKET


                    $ticketClose = $workflow->ticketFromStepId(
                        'close_registration',
                        $helpdesk->getTicket($followUpGgusTicket),
                        array('user' => $this->getUser()->getUsername())
                    );


                    $helpdesk->updateTicket($ticketClose);
                    $permaLink = $helpdesk->getTicketPermalink($ticketClose->getId());
                    //@codeCoverageIgnoreEnd
                }
                if ($this->get("kernel")->getEnvironment() != "test" && $ticketClose->getStatus() == 'solved') {
                    //@codeCoverageIgnoreStart

                    //--------------------------------------------------------------------------------------------------------//

                    //--------------------------------------------------------------------------------------------------------//
                    //                                      IV : UPDATE VO STATUS

                    $status = $voModel->updateVOToProd($statusId);

                    if ($status != "Status updated") {

                        $this->addFlash('danger', '[VO '.$vo->getName().'] - '.$status);

                    } else {
                        $newStatus = $this->getDoctrine()->getRepository("AppBundle:VO\VoStatus")->findOneBy(
                            array("id" => $voHeader->getStatusId())
                        );

                        //if ok notify voWaitingList view
                        $lavoisierUrl = $this->container->getParameter("lavoisierurl");

                        $lquery = new Query($lavoisierUrl, 'VoDisciplinesEntries', 'notify');
                        $lquery->execute();
                        $lquery = new Query($lavoisierUrl, 'VoStatusScope', 'notify');
                        $lquery->execute();
                        $lquery = new Query($lavoisierUrl, 'VoWaitingList', 'notify');
                        $lquery->execute();


                        //send notification mail
                        $mailer = new mailer();


                        // DEPENDING ON GGUS EXTERNAL METHODS... CAN'T BE TESTED NOWADAY
                        $mailer->notifyForStatusChange(
                            $this->container,
                            $vo->getSerial(),
                            $oldStatus,
                            $newStatus,
                            false,
                            $permaLink
                        );


                        //show success flash message
                        $this->addFlash(
                            'success',
                            '[VO '.$vo->getName().'] - '.$status.' from <strong>'.$oldStatus->getStatus(
                            ).'</strong> to <strong>'.$newStatus->getStatus(
                            ).'</strong> - Related ticket had been closed : <a href="'.$permaLink.'" target="_blank" title="related solved ticket">Related ticket</a>'
                        );

                        //show notification changed VO flash message
                        $this->addFlash('prodVO', 'New active VO in tab');

                    }
                    //@codeCoverageIgnoreEnd
                    // DEPENDING ON GGUS EXTERNAL METHODS... CAN'T BE TESTED NOWADAY
                    //NEED TO TEST STATUS UPDATE
                } else {
                    if ($this->get("kernel")->getEnvironment() == "test") {
                        $status = $voModel->updateVOToProd($statusId);

                        if ($status != "Status updated") {

                            $this->addFlash('danger', '[VO '.$vo->getName().'] - '.$status);

                        } else {
                            $newStatus = $this->getDoctrine()->getRepository("AppBundle:VO\VoStatus")->findOneBy(
                                array("id" => $voHeader->getStatusId())
                            );

                            //show success flash message
                            $this->addFlash(
                                'success',
                                '[VO '.$vo->getName().'] - '.$status.' from <strong>'.$oldStatus->getStatus(
                                ).'</strong> to <strong>'.$newStatus->getStatus().'</strong>'
                            );

                        }

                    } else {
                        $this->addFlash(
                            'danger',
                            '[VO '.$vo->getName().'] - '."Unable to close ticket and update VO status to production..."
                        );

                    }
                }

                // @codeCoverageIgnoreStart

            } catch (Exception $e) {
                $this->addFlash(
                    'danger',
                    '[VO '.$vo->getName().'] - '."Unable to close ticket and update VO status to production..."
                );

            }
            // @codeCoverageIgnoreEnd

        } else {
            $this->addFlash('danger', '[VO '.$vo->getName().'] - '."Wrong status to update to production...");
        }


        return $this->redirect($this->generateUrl("registerUpdate"));
    }

    /**
     * register new vo
     * @Route("/registration", name="registration")
     */
    public function registrationAction(Request $request)
    {

        //get vo model
        $voModel = new ModelVO($this->container, null);

        //create entity for form
        $vo = new Vo();

        /** @var $voHeader \AppBundle\Entity\VO\VoHeader */
        $voHeader = new VoHeader();
        $voHeader->setUser($this->getUser()->getUserName());
        $voHeader = $voModel->setTypageMiddleWare($voHeader, "bool");
        $voHeader = $voModel->setNotifySites($voHeader);
        //set default scope id to global
        $voHeader->setScopeId(2);


        /** @var $voAS \AppBundle\Entity\VO\VoAcknowledgmentStatements */
        $voAS = new VoAcknowledgmentStatements();

        /** @var $voRessources \AppBundle\Entity\VO\VoRessources */
        $voRessources = new VoRessources();
        $voRessources = $voModel->setNotifySites($voRessources);

        /** @var $voMailingList \AppBundle\Entity\VO\VoMailingList */
        $voMailingList = new VoMailingList();
        $voMailingList = $voModel->setNotifySites($voMailingList);

        /** @var $voContact \AppBundle\Entity\VO\VoContacts */
        $voContact = new VoContacts();


        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
        //                                                          CREATE LIST FOR DISCIPLINES
        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
        $listDiscipline = array();
        //get disciplines with lavoisier query
        $viewDiscipline = "";
        try {
            $query2 = new \Lavoisier\Query(
                $this->container->getParameter('lavoisierUrl'),
                'VoDisciplinesTree_Raw',
                'lavoisier',
                'json-deprecated'
            );
            $viewDiscipline = json_decode($query2->execute());
            $viewDiscipline = $this->parseDiscipline($listDiscipline, $viewDiscipline->disciplines[0]);
            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->addFlash("danger", "Error on VO registration - can't get list of disciplines - ".$message);

            return $this->redirect($this->generateUrl("registration"));
        }
        //@codeCoverageIgnoreEnd
        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------//


        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
        //                                                                  CREATE REGISTRATION FORM
        //------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

        $voForm = $this->createForm('AppBundle\Form\VO\VoType', $vo);


        // add voHeaderForm
        $voForm->add(
            'VoHeader',
            'AppBundle\Form\VO\VoHeaderType',
            array('data_class' => 'AppBundle\Entity\VO\VoHeader', 'data' => $voHeader, 'mapped' => false)
        );
        //add aup (type file) option form
        $voForm->get('VoHeader')->add(
            'aupFile',
            ManageAupFileType::class,
            array(
                'required' => false,
                'mapped' => false,
            )
        );

        //add other part of form (with other form type depending on VO entities)
        $voForm->add(
            'VoRessources',
            'AppBundle\Form\VO\VoRessourcesType',
            array('data_class' => 'AppBundle\Entity\VO\VoRessources', 'data' => $voRessources, 'mapped' => false)
        );
        $voForm->add(
            'VoMailingList',
            'AppBundle\Form\VO\VoMailingListType',
            array('data_class' => 'AppBundle\Entity\VO\VoMailingList', 'data' => $voMailingList, 'mapped' => false)
        );
        $voForm->add(
            'VoAcknowledgmentStatements',
            'AppBundle\Form\VO\VoAcknowledgmentStatementsType',
            array('data_class' => 'AppBundle\Entity\VO\VoAcknowledgmentStatements', 'data' => $voAS, 'mapped' => false)
        );
        $voForm->add(
            'VoContacts',
            'AppBundle\Form\VO\VoContactsType',
            array('data_class' => 'AppBundle\Entity\VO\VoContacts', 'data' => $voContact, 'mapped' => false)
        );


        //add voms server type
        $VoVomsServer = "";
        $voVomsServerForm = "";
        try {
            $lquery = new \Lavoisier\Query(
                $this->container->getParameter('lavoisierUrl'), 'voms-endpoint', 'lavoisier'
            );
            $lvomss = simplexml_load_string($lquery->execute());
            $choices = array();
            foreach ($lvomss as $lvoms) {
                $choices[(string)$lvoms->HOSTNAME] = (string)$lvoms->HOSTNAME;
            }
            $select = array('-- Please select --' => '');
            $list = array_merge($select, $choices);
            asort($list);

            $VoVomsServer = $voms = new VoVomsServer();
            $voVomsServerForm = $this->createForm('AppBundle\Form\VO\VoVomsServerType', $VoVomsServer);
            $voVomsServerForm->add(
                'hostname',
                ChoiceType::class,
                array(
                    'choices' => $list,
                    'attr' => array(
                        'class' => 'form-control input-sm',
                    ),
                )
            );
            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->addFlash("danger", "Error on VO registration - can't get voms-endpoint - ".$message);

            return $this->redirect($this->generateUrl("registration"));
        }
        //@codeCoverageIgnoreEnd


        if ($request->getMethod() == "POST") {


            //@codeCoverageIgnoreStart
            $voForm->handleRequest($request);


            if ($voForm->isSubmitted()) {

                //checked that vo name is not already used in db
                if ($this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(
                        array("name" => $voHeader->getName())
                    ) != null) {
                    $this->addFlash(
                        "danger",
                        "Error on VO registration - can't persist Vo - The vo name you entered is already used..."
                    );

                    return $this->redirect($this->generateUrl("registration"));
                }

                //get the username of current user
//                $userName = $this->getUser()->getUsername();

                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //                                                                                                                             //
                //                                                  PERSIST ENTITIES IN DATABASE                                               //
                //                                                                                                                             //
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                //---------------------------------------------------------------------------------------------------------------------------------------------//
                //1)    CREATE VOMAILINGLIST / VOHEADER / VORESSOURCES WITHOUT SERIAL (FUTURE 1/1 RELATION WITH VO)
                //------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

                // set voHeader Field with form data and default data
                $voHeader = $voModel->setVoHeader($voHeader);
//                $voHeader->setUser($userName);
                if ($voHeader->getAupType() === "File") {
                    $voHeader->setAup($request->get("vo")["VoHeader"]["aupFile"]["name"]);
                    $file = $request->files->get("vo")["VoHeader"]["aupFile"]["aupFile"];
                    $file->move($this->getParameter("aupurl"), $voHeader->getAup());
                }
                $voHeader->setValidationDate(new \DateTime());
                $voHeader->setValidated(1);
                $voHeader->setPerun(0);


                //save voHeader
                $headerId = "";
                try {
                    $em = $this->getDoctrine()->getManager();

                    $em->persist($voHeader);
                    $em->flush();
                    $em->refresh($voHeader);
                    $headerId = $voHeader->getId();
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $this->addFlash("danger", "Error on VO registration - can't persist voHeader data - ".$message);

                    return $this->redirect($this->generateUrl("registration"));
                }

                //Set vo Ressources Field
                $voRessources = $voModel->setRessources($voRessources);
                $voRessources->setUser($voHeader->getUser());

                //save voRessources
                $ressourcesId = "";
                try {
                    $em = $this->getDoctrine()->getManager();

                    $em->persist($voRessources);
                    $em->flush();
                    $em->refresh($voRessources);
                    $ressourcesId = $voRessources->getId();
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $this->addFlash("danger", "Error on VO registration - can't persist voResources data - ".$message);

                    return $this->redirect($this->generateUrl("registration"));
                }

                //Set vo Mailing List Field
                $voModel->setMailingList($voMailingList);
                $voMailingList->setUser($voHeader->getUser());

                // save VoMailingList
                $mailingListId = "";
                try {
                    $em = $this->getDoctrine()->getManager();

                    $em->persist($voMailingList);
                    $em->flush();
                    $em->refresh($voMailingList);
                    $mailingListId = $voMailingList->getId();
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $this->addFlash(
                        "danger",
                        "Error on VO registration - can't persist voMailingList data - ".$message
                    );

                    return $this->redirect($this->generateUrl("registration"));
                }
                //---------------------------------------------------------------------------------------------------------------------------------------------//


                //---------------------------------------------------------------------------------------------------------------------------------------------//
                //      2) SAVE VO WITH ID: VOMAILINGLIST / VOHEADER / VORESSOURCES
                //---------------------------------------------------------------------------------------------------------------------------------------------//

                //set vo data
                $vo->setName($voHeader->getName());
                $vo->setCreationDate(new \DateTime());
                $vo->setLastChange(new \DateTime());
                $vo->setValidationDate(new \DateTime());
                //set vo 1/1 relation
                if ($headerId != "") {
                    $vo->setHeaderId($headerId);
                } else {
                    $this->addFlash("danger", "Error on VO registration - headerId NULL ");

                    return $this->redirect($this->generateUrl("registration"));
                }

                if ($ressourcesId != "") {
                    $vo->setRessourcesId($ressourcesId);
                } else {
                    $this->addFlash("danger", "Error on VO registration - ressourcesId NULL ");

                    return $this->redirect($this->generateUrl("registration"));
                }
                if ($mailingListId != "") {
                    $vo->setMailingListId($mailingListId);
                } else {
                    $this->addFlash("danger", "Error on VO registration - mailingListId NULL ");

                    return $this->redirect($this->generateUrl("registration"));
                }
                //set tickets to 0 (not existing yet)
                $vo->setGgusTicketId(0);
                $vo->setNeedVomsHelp(0);
                $vo->setVomsTicketId(0);
                if (isset($request->get("vo")['VoHeader']['notify_sites'])) {
                    $vo->setNeedGgusSupport(1);
                } else {
                    $vo->setNeedGgusSupport(0);
                }
                $vo->setGgusTicketIdSuCreation(0);
                $vo->setMonitored(0);
                $vo->setEnableTeamTicket(0);

                $voSerial = "";
                //save vo
                try {
                    $em = $this->getDoctrine()->getManager();

                    $em->persist($vo);
                    $em->flush();
                    $em->refresh($vo);
                    $voSerial = $vo->getSerial();
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $this->addFlash("danger", "Error on VO registration - can't persist Vo data - ".$message);

                    return $this->redirect($this->generateUrl("registration"));
                }

                //---------------------------------------------------------------------------------------------------------------------------------------------//


                //---------------------------------------------------------------------------------------------------------------------------------------------//
                //      3) AFTER SAVING VO, SAVE SERIAL IN VOHEADER / VORESSOURCES / VOMAILINGLIST
                //---------------------------------------------------------------------------------------------------------------------------------------------//

                if ($voSerial != "") {
                    $voHeader->setSerial($voSerial);
                    $voHeader->setGridId($voSerial);
                    $voRessources->setSerial($voSerial);
                    $voMailingList->setSerial($voSerial);

                    if ($vo->getNeedVomsHelp() == 2) {
                        $voHeader->setPerun(1);



                    }


                    try {
                        $em = $this->getDoctrine()->getManager();

                        $em->persist($voHeader);
                        $em->persist($voRessources);
                        $em->persist($voMailingList);
                        $em->flush();
                        $em->refresh($voHeader);
                        $em->refresh($voRessources);
                        $em->refresh($voMailingList);
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                        $this->addFlash(
                            "danger",
                            "Error on VO registration - can't persist Vo serial for voHeader, voResources and voMailingList - ".$message
                        );

                        return $this->redirect($this->generateUrl("registration"));
                    }
                } else {
                    $this->addFlash(
                        "danger",
                        "Error on VO registration - can't persist Vo serial for voHeader, voResources and voMailingList - no Vo Serial"
                    );

                    return $this->redirect($this->generateUrl("registration"));
                }

                //---------------------------------------------------------------------------------------------------------------------------------------------//


                //---------------------------------------------------------------------------------------------------------------------------------------------//
                //      4) SAVE VOACKNOWLEDGMENTSTATEMENTS
                //---------------------------------------------------------------------------------------------------------------------------------------------//

                // if need
//                if ($request->get("vo")["VoAcknowledgmentStatements"]["as_need"]) {
                // if not relationship set empty
                if (!$request->get("vo")["VoAcknowledgmentStatements"]["relationShip"]) {
                    $voAS->setRelationShip("");
                }
                $voAS->setSerial($voSerial);

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($voAS);
                    $em->flush();
                    $em->refresh($voAS);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $this->addFlash(
                        "danger",
                        "Error on VO registration - can't persist VoAcknowledgmentStatements - ".$message
                    );

                    return $this->redirect($this->generateUrl("registration"));
                }

//                }
                //---------------------------------------------------------------------------------------------------------------------------------------------//


                //---------------------------------------------------------------------------------------------------------------------------------------------//
                //      5) SAVE VOYEARLYVALIDATION
                //---------------------------------------------------------------------------------------------------------------------------------------------//
                $voYearly = new VoYearlyValidation();
                $voYearly->setSerial($voSerial);
                // set validated by default (valid construction of vo)
                $voYearly->setDateValidation(new \DateTime());
                $voYearly->setDateLastEmailSending(new DateTime("0000-00-00 00:00:00"));
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($voYearly);
                    $em->flush();
                    $em->refresh($voYearly);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $this->addFlash(
                        "danger",
                        "Error on VO registration - can't persist VoAcknowledgmentStatements - ".$message
                    );

                    return $this->redirect($this->generateUrl("registration"));
                }
                //---------------------------------------------------------------------------------------------------------------------------------------------//


                //---------------------------------------------------------------------------------------------------------------------------------------------//
                //      6) SAVE VODISCIPLINE
                //---------------------------------------------------------------------------------------------------------------------------------------------//

                $voModel = new ModelVO($this->container, $voSerial);
                $message = $voModel->saveDiscipline($request->get("VoDiscipline"));

                if ($message != "OK") {
                    $this->addFlash("danger", "Error on VO registration - can't persist VoDiscipline - ".$message);

                    return $this->redirect($this->generateUrl("registration"));
                }
                //---------------------------------------------------------------------------------------------------------------------------------------------//


                //---------------------------------------------------------------------------------------------------------------------------------------------//
                //     7) SAVE VOCONTACTS AND VOCONTACTHASPROFILE
                //---------------------------------------------------------------------------------------------------------------------------------------------//

                $gridBody = 0;
                $contact = $this->getDoctrine()->getRepository("AppBundle:VO\VoContacts")->findOneBy(
                    array("dn" => $voContact->getDn())
                );
                if ($contact != null) {
                    $gridBody = $contact->getGridBody();
                }
                $voContact->setGridBody($gridBody);

                try {
                    $em = $this->getDoctrine()->getManager();

                    $em->persist($voContact);
                    $em->flush();
                    $em->refresh($voContact);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $this->addFlash("danger", "Error on VO registration - can't persist VoContacts - ".$message);

                    return $this->redirect($this->generateUrl("registration"));
                }


                // Set profile to manager
                $contactHP = new VoContactHasProfile();

                if ($contactHP->getComment() == null) {
                    $contactHP->setComment("");
                }

                $contactHP->setContactId($voContact->getId());
                $contactHP->setUserProfileId(1);
                $contactHP->setSerial($voSerial);

                try {
                    $em = $this->getDoctrine()->getManager();

                    $em->persist($contactHP);
                    $em->flush();
                    $em->refresh($contactHP);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $this->addFlash(
                        "danger",
                        "Error on VO registration - can't persist VoContactHasProfile - ".$message
                    );

                    return $this->redirect($this->generateUrl("registration"));
                }

                //---------------------------------------------------------------------------------------------------------------------------------------------//


                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //                                                                                                                             //
                //                                                  TICKETING SYSTEM                                                           //
                //                                                                                                                             //
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                //-----------------------------------------------------------------------------------------//
                //           CREATE TICKET FOR GGUS REGISTRATION (VO(2) IN INCOMING VO TAB)
                //----------------------------------------------------------------------------------------//

                if ($this->get("kernel")->getEnvironment() != "test") {
                    $ggusTicket = $this->createTicketGgusRegistration($voSerial, $voContact, $voHeader);



                    //catch ggus ticket error
                    if (!isset($ggusTicket["error"])) {

                        $vo->setGgusTicketId($ggusTicket["ggus_id"]);
                    } else {
                        $this->addFlash("danger", "Error on VO registration - ".$ggusTicket["error"]);

                        return $this->redirect($this->generateUrl("registration"));
                    }

                    try {
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($vo);
                        $em->flush();
                        $em->refresh($vo);
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                        $this->addFlash(
                            "danger",
                            "Error on VO registration - can't persist created GGUS TICKET for VO follow-up -".$message
                        );

                        return $this->redirect($this->generateUrl("registration"));
                    }
                }
                //-----------------------------------------------------------------------------------------//
                //           SAVE VOVOMSREGISTRATION
                //----------------------------------------------------------------------------------------//
                $vomsNeed = $request->get("voVomsRegistration")['voms_need'];

                // Send notification to gridbody and vo manager
                // if need
                if ($vomsNeed == 0) {
                    // test form
                    if ($voVomsServerForm != "") {
                        $voVomsServerForm->handleRequest($request);
                    } else {
                        $this->addFlash("danger", "Error on VO registration - voVomsServerForm NULL ");

                        return $this->redirect($this->generateUrl("registration"));
                    }

                    if ($VoVomsServer != "") {
                        $VoVomsServer->setSerial($voSerial);
                    } else {
                        $this->addFlash("danger", "Error on VO registration - VoVomsServer NULL ");

                        return $this->redirect($this->generateUrl("registration"));
                    }

                    try {
                        $em = $this->getDoctrine()->getManager();

                        if ($voVomsServerForm->isSubmitted()) {

                            $em->persist($VoVomsServer);
                        }
                        $em->flush();
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                        $this->addFlash(
                            "danger",
                            "Error on VO registration - can't persist VOVOMSREGISTRATION - ".$message
                        );

                        return $this->redirect($this->generateUrl("registration"));
                    }

                    //send mail to notify VOMS ticket creation
                    if ($this->get("kernel")->getEnvironment() != "test") {
                        mailer::notifyForRegistration(
                            $voSerial,
                            $ggusTicket["pLinkTicketCreated"],
                            $this->container,
                            $VoVomsServer
                        );
                    }
                } else {
                    if ($vomsNeed == 2) {
                        $voHeader->setPerun(1);
                        $VoVomsServer->setHostname('https://perun.cesnet.cz');
                        $VoVomsServer->setHttpsPort('8443');
                        $VoVomsServer->setVomsesPort('15002');
                        $VoVomsServer->setMembersListUrl('https://perun.cesnet.cz');
                        $VoVomsServer->setSerial($voSerial);
                        $VoVomsServer->setIsVomsadminServer('1');

                        $vo->setNeedVomsHelp(2);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($VoVomsServer);
                        $em->flush();
//                        dump('coucou');die;


//                                dump($voHeader);die;

                        //persist perun modification on voheader
                        try {
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($voHeader);
                            $em->flush();
                            $em->refresh($voHeader);

                        } catch (\Exception $e) {
                            $message = $e->getMessage();
                            $this->addFlash(
                                "danger",
                                "Error on VO registration - can't persist perun option for VO  - ".$message
                            );


                            return $this->redirect($this->generateUrl("registration"));
                        }

                        if ($this->get("kernel")->getEnvironment() != "test") {
                            mailer::notifyForPerun($voSerial, $this->container);
                        }
                    } else {
                        $vo->setNeedVomsHelp($vomsNeed);

                        try {
                            $em = $this->getDoctrine()->getManager();

                            $em->persist($vo);

                            //persist perun modification on voheader
                            $em->persist($voHeader);
                            $em->flush();
                            $em->refresh($vo);
                            $em->refresh($voHeader);

                        } catch (\Exception $e) {
                            $message = $e->getMessage();
                            $this->addFlash(
                                "danger",
                                "Error on VO registration - can't persist need voms support for VO  - ".$message
                            );

                            return $this->redirect($this->generateUrl("registration"));
                        }

                        //send mail to notify VOMS Help ticket creation
                        if ($this->get("kernel")->getEnvironment() != "test") {
                            mailer::notifyForRegistration(
                                $voSerial,
                                $ggusTicket["pLinkTicketCreated"],
                                $this->container
                            );
                        }
                    }
                }

                //-----------------------------------------------------------------------------------------
                //           CREATE TICKET TO ASK GGUS SU (VO SU IN INCOMING VO TAB)
                //----------------------------------------------------------------------------------------
                if ($this->get("kernel")->getEnvironment() != "test") {

                    $pLinkTicketSUCreation = null;
                    if ($vo->getNeedGgusSupport() == 1) {
                        $pLinkTicketSUCreation = $this->createTicketForGgusSUCreation(
                            $voSerial,
                            $voHeader,
                            $voContact->getEmail()
                        );

                        if (stristr($pLinkTicketSUCreation, "Error")) {
                            $this->addFlash("danger", "Error on VO registration - ".$pLinkTicketSUCreation);

                            return $this->redirect($this->generateUrl("registration"));
                        }
                    }

                    try {
                        $lavoisierUrl = $this->container->getParameter("lavoisierurl");
                        $lquery = new Query($lavoisierUrl, 'VoWaitingList', 'notify');
                        $lquery->execute();
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                        $this->addFlash("danger", "Error on VO registration - can't get voMailingList  - ".$message);

                        return $this->redirect($this->generateUrl("registration"));
                    }
                }


                /////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //                                                                                                         //
                //                                      RREGISTRATION SUCCESS                                              //
                //                                                                                                         //
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //if ok notify voWaitingList view
                $lavoisierUrl = $this->container->getParameter("lavoisierurl");

                $lquery = new Query($lavoisierUrl, 'VoDisciplinesEntries', 'notify');
                $lquery->execute();
                $lquery = new Query($lavoisierUrl, 'VoStatusScope', 'notify');
                $lquery->execute();
                $lquery = new Query($lavoisierUrl, 'VoWaitingList', 'notify');
                $lquery->execute();

                if ($this->get("kernel")->getEnvironment() != "test") {
                    return $this->render(
                        'vo/voManagement/voRegistration.html.twig',
                        array(
                            "registred" => true,
                            "pLinkTicketCreated" => $ggusTicket["pLinkTicketCreated"],
                            "pLinkTicketSUCreation" => $pLinkTicketSUCreation,
                        )
                    );
                } else {
//                    $voModel = new ModelVO($this->container, $vo->getSerial());
//                    $deleteMessage = $voModel->deleteVo();
                    return $this->render(
                        'vo/voManagement/voRegistration.html.twig',
                        array(
                            "registred" => true,
//                        "deleteMessage" => $deleteMessage
                        )
                    );
                }
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //                                                                                                         //
                //                                      RREGISTRATION FAILED ==> ERROR ON FORM SUBMIT                      //
                //                                                                                                         //
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////
            } else {
                $string = (string)$voForm->getErrors(true, false);

                $this->addFlash("danger", "Invalid vo registration form... Please contact us.");

                return $this->render(
                    'vo/voManagement/voRegistration.html.twig',
                    array(
                        "voForm" => $voForm->createView(),
                        "voVomsServerForm" => $voVomsServerForm->createView(),
                        "listDiscipline" => $listDiscipline,
                        "viewDiscipline" => $viewDiscipline,
                    )
                );
            }
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //                                                                                                         //
            //                                      DEFAULT PAGE WITH FORM                                             //
            //                                                                                                         //
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //@codeCoverageIgnoreEnd
        } else {


            return $this->render(
                'vo/voManagement/voRegistration.html.twig',
                array(
                    "voForm" => $voForm->createView(),
                    "voVomsServerForm" => $voVomsServerForm->createView(),
                    "listDiscipline" => $listDiscipline,
                    "viewDiscipline" => $viewDiscipline,
                )
            );
        }
    }


    /**
     * check if vo name already exist
     * @param Request $request
     * @return Response
     * @Route("/checkVoName", name="checkVoName")
     */
    public function checkExistingVoName(Request $request)
    {

        $name = $request->get("name");

        if ($name != "") {
            $modelVo = new ModelVO($this->container, null);
            $vo = $modelVo->getVoByName($name);
            if ($vo != null && $vo != "") {
                return new Response(json_encode(array("checkedName" => true)));
            } else {
                return new Response(json_encode(array("checkedName" => false)));
            }
        } else {
            return new Response(json_encode(array("error" => "No vo name entered...")), 500);
        }
    }

    /**
     * Update vo
     * @Route("/update/serial/{serial}", name="voUpdate")
     * @param integer $serial
     */
    public function voUpdateAction(Request $request)
    {

        $this->user = $this->getUser();
        $doctrine = $this->getDoctrine();


        // get vo
        $serial = $request->get("serial");
        $vo = $doctrine->getRepository("AppBundle:VO\Vo");

        if ($vo->findOneBy(array("serial" => $serial)) != null) {

        $voName = $vo->findOneBy(array("serial" => $serial))->getName();



            if ($this->user->canModifyVO($voName)) {

                // cration object
                $vo = $vo->findOneBy(array("serial" => $serial));

                $voModel = new ModelVO($this->container, $serial);
                // get current vo header
                /** @var  $voHeader \AppBundle\Entity\VO\VoHeader */
                $voHeader = $doctrine->getRepository("AppBundle:VO\VoHeader")->find($vo->getHeaderId());

                $isSubmitted = true;
                if ($voHeader->getStatusId() == 1 or $voHeader->getStatusId() == 8) {
                    $isSubmitted = false;
                }
                $voHeader = $voModel->setTypageMiddleWare($voHeader, "bool");
                $voHeader = $voModel->setNotifySites($voHeader);
                // if vo not validated
                $validationDate = $voHeader->getValidationDate();

                if ($validationDate->getTimestamp() == false) {
                    $voHeader->setValidationDate(new \DateTime());
                }

                //get current Acknowledgment Statement
                /** @var  $voAS \AppBundle\Entity\VO\VoAcknowledgmentStatements */
                $voAS = $doctrine->getRepository("AppBundle:VO\VoAcknowledgmentStatements")->findOneBy(
                    array("serial" => $vo->getSerial())
                );

                if ($voAS == null) {
                    $voAS = new VoAcknowledgmentStatements();
                    $voAS->setSerial($serial);
                    $voAS->setTypeAs("VO");
                }

                // get current ressources
                /** @var  $voRessources \AppBundle\Entity\VO\VoRessources */
                $voRessources = $doctrine->getRepository("AppBundle:VO\VoRessources")->find($vo->getRessourcesId());
                $voRessources = $voModel->setNotifySites($voRessources);

                // get current mailing list
                /** @var  $voMailingList \AppBundle\Entity\VO\VoMailingList */
                $voMailingList = $doctrine->getRepository("AppBundle:VO\VoMailingList")->find($vo->getMailingListId());
                $voMailingList = $voModel->setNotifySites($voMailingList);


                // get current disciplines and tree of discipline
                try {
                    $queryDiscipline = new \Lavoisier\Query(
                        $this->container->getParameter('lavoisierUrl'),
                        'VoDisciplinesByID',
                        'lavoisier',
                        'json'
                    );
                    $queryDiscipline->setMethod('POST');
                    $queryDiscipline->setPostFields(array('voId' => $serial));
                    $listDisciplinesTab = json_decode($queryDiscipline->execute(), true);

                    $listDiscipline = array();
                    if (isset($listDisciplinesTab['disciplines'])) {
                        $listDiscipline = array_values($listDisciplinesTab['disciplines']);
                    }
                    //@codeCoverageIgnoreStart
                } catch (\Exception $e) {
                    $this->addFlash(
                        "danger",
                        "Update VO - can't get vo disciplines - lavoisier call failed - ".$e->getMessage()
                    );

                    return $this->redirect($this->generateUrl("voUpdate"));
                }
                //@codeCoverageIgnoreEnd
                // get all discipline
                try {
                    $query2 = new \Lavoisier\Query(
                        $this->container->getParameter('lavoisierUrl'),
                        'VoDisciplinesTree_Raw',
                        'lavoisier',
                        'json-deprecated'
                    );
                    $viewDiscipline = json_decode($query2->execute());
                    //@codeCoverageIgnoreStart
                } catch (\Exception $e) {
                    $this->addFlash(
                        "danger",
                        "Update VO - can't get vo disciplines tree - lavoisier call failed - ".$e->getMessage()
                    );

                    return $this->redirect($this->generateUrl("voUpdate"));
                }
                //@codeCoverageIgnoreEnd

                // Create forms
                $voForm = $this->createForm('AppBundle\Form\VO\VoType', $vo);
                // add voHeaderForm
                $voForm->add(
                    'VoHeader',
                    'AppBundle\Form\VO\VoHeaderType',
                    array('data_class' => 'AppBundle\Entity\VO\VoHeader', 'data' => $voHeader, 'mapped' => false)
                );
                $voForm = $voModel->addAupFields($voHeader, $voForm);


                $voForm->add(
                    'VoRessources',
                    'AppBundle\Form\VO\VoRessourcesType',
                    array(
                        'data_class' => 'AppBundle\Entity\VO\VoRessources',
                        'data' => $voRessources,
                        'mapped' => false,
                    )
                );
                $voForm->add(
                    'VoMailingList',
                    'AppBundle\Form\VO\VoMailingListType',
                    array(
                        'data_class' => 'AppBundle\Entity\VO\VoMailingList',
                        'data' => $voMailingList,
                        'mapped' => false,
                    )
                );
                $voForm->add(
                    'VoAcknowledgmentStatements',
                    'AppBundle\Form\VO\VoAcknowledgmentStatementsType',
                    array(
                        'data_class' => 'AppBundle\Entity\VO\VoAcknowledgmentStatements',
                        'data' => $voAS,
                        'mapped' => false,
                    )
                );

                $viewDiscipline = $this->parseDiscipline($listDiscipline, $viewDiscipline->disciplines[0]);

                if ($request->getMethod() == "POST") {
                    //@codeCoverageIgnoreStart

                    $voForm->handleRequest($request);
                    if ($voForm->isSubmitted()) {

                        try {
                            $em = $this->getDoctrine()->getManager();
                            // cast midlleWare in integer
                            $voHeader = $voModel->setTypageMiddleWare($voHeader, "integer");

//                        //get the username of current user
//                        $voHeader->setUser($this->getUser()->getUsername());

                            $em->persist($voHeader);

                            $voOldAS = $doctrine->getRepository("AppBundle:VO\VoAcknowledgmentStatements")->findOneBy(
                                array("serial" => $vo->getSerial())
                            );

                            if (!$request->get("vo")["VoAcknowledgmentStatements"]["relationShip"]) {
                                $voAS->setRelationShip("");
                            }

                            $em->persist($voAS);

                            if ($voRessources->getCvmfs() != null && $voRessources->getCvmfs() != "") {
                                $tabEndpoints = explode(",", $voRessources->getCvmfs());
                                $voRessources->setCvmfs(serialize($tabEndpoints));
                            }


                            $em->persist($voRessources);
                            $em->persist($voMailingList);

                            $em->flush();
                            $voModel->deleteDiscipline();
                            $voModel->saveDiscipline($request->get("VoDiscipline"));


                            $voModel = new ModelVO($this->container, $serial);
                            $voModel->setLastChangeDate();
                            $messageVo = "VO ".$vo->getName()." has been updated with success.";
                            $this->addFlash("success", $messageVo);

                            return $this->redirect($this->generateUrl("voUpdate", array("serial" => $serial)));

                        } catch (Exception $e) {

                            $messageVo = "Update VO - can't persist  VoAcknowledgmentStatements, VoResources & VoMailingList- ".$e->getMessage(
                                );
                            $this->addFlash("danger", $messageVo);

                            return $this->redirect($this->generateUrl("voUpdate", array("serial" => $serial)));
                        }

                    } else {
                        $messageVo = "The form is invalid...";
                        $this->addFlash("danger", $messageVo);

                        return $this->redirect($this->generateUrl("voUpdate", array("serial" => $serial)));


                    }
                }

                if (strlen($voName) > 20) {
                    $subVoName = substr($voName, 0, 20)."...";
                } else {
                    $subVoName = $voName;
                }

                $creationDate = $vo->getCreationDate();
                $subCreationDate = substr($vo->getCreationDate()->format('Y-m-d H:i'), 0, 10);

                return $this->render(
                    'vo/voManagement/voUpdate.html.twig',
                    array(
                        "action" => $this->generateUrl('voUpdate', array("serial" => $serial)),
                        "voName" => $voName,
                        "subVoName" => $subVoName,
                        "serial" => $serial,
                        "isSubmitted" => $isSubmitted,
                        "creationDate" => $creationDate,
                        "subCreationDate" => $subCreationDate,
                        "voForm" => $voForm->createView(),
                        "listDiscipline" => $listDiscipline,
                        "viewDiscipline" => $viewDiscipline,
//                "messageVo" => $messageVo
                    )
                );
                //@codeCoverageIgnoreEnd
            } else {

                $message = "You have not been recognized as VO Manager or VO Deputy of this VO.

            Consequently you are not authorized to update it.";

                return $this->render(
                    "@Twig/Exception/errorAuthenticationFailed.html.twig",
                    array(
                        "message" => $message,
                    )
                );
            }
        } else {
            return new Response($this->render("@Twig/Exception/error404.html.twig"), 404);
        }

    }


    /**
     * @param Request $request
     * @return Response
     * @Route("/contactList", name="contactList")
     */
    public function contactListAction(Request $request)
    {
        $serial = $request->get('serial');

        $message = $request->get("message");
        if (!isset($message)) {
            $message = null;
        }
        // get current contacts
        $voModel = new ModelVO($this->container, $serial);
        $voContactsList = $voModel->getVOContacts();

        return $this->render(
            ':vo/voManagement:template_voContactList.html.twig',
            array(
                "voContactsList" => $voContactsList,
                "serial" => $serial,
                "message" => $message,
            )
        );
    }

    /**
     * @param $request (String $dn, integer $serial)
     * @Route("/contactForm", name="contactForm")
     */
    public function contactFormAction(Request $request)
    {

        $dn = $request->get('id');
        $serial = $request->get('serial');
        $oldDN = $request->get('oldDN');
        $gridBody = 0;

        $comment = "";
        $profileid = 1;


        if ($oldDN != null) {
            /** @var  $contact \AppBundle\Entity\VO\VoContacts */
            $contact = $this->getDoctrine()->getRepository("AppBundle:VO\VoContacts")->findOneBy(array("dn" => $oldDN));
        } else {
            /** @var  $contact \AppBundle\Entity\VO\VoContacts */
            $contact = $this->getDoctrine()->getRepository("AppBundle:VO\VoContacts")->findOneBy(array("dn" => $dn));
        }

        if (isset($contact)) {
            $mode = "update";

            $gridBody = $contact->getGridBody();

            $contactHP = $this->getDoctrine()->getRepository("AppBundle:VO\VoContactHasProfile")->findOneBy(
                array("serial" => $serial, "contact_id" => $contact->getId())
            );


            if ($contactHP == null) {
                $contactHP = new VoContactHasProfile();
            }
        } else {
            $mode = "create";

            $contactHP = new VoContactHasProfile();
            $contact = new VoContacts();
            $contact->setGridBody($gridBody);
        }

        $contactHP->setSerial($serial);

        $contactForm = $this->createForm('AppBundle\Form\VO\VoContactHasProfileType', $contactHP);
        $contactForm->add(
            'VoContacts',
            'AppBundle\Form\VO\VoContactsType',
            array('data_class' => 'AppBundle\Entity\VO\VoContacts', 'data' => $contact, 'mapped' => false)
        );


        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted()) {

            try {
                $em = $this->getDoctrine()->getManager();
                $contact->setGridBody($gridBody);
                $em->persist($contact);
                $em->flush();
                $em->refresh($contact);

                if ($contactHP->getComment() == null) {
                    $contactHP->setComment("");
                }
                $contactHP->setContactId($contact->getId());
                $em->persist($contactHP);
                $em->flush();

                $voModel = new ModelVO($this->container, $serial);
                $voModel->setLastChangeDate();
                $message = $contact->getDn()." has been saved.";


                return $this->forward("AppBundle:VO/VO:contactList", array('serial' => $serial, 'message' => $message));

                //@codeCoverageIgnoreStart
            } catch (Exception $e) {
                $message = "contactForm - can't save the contact [".$contact->getDn()."] - ".$e->getMessage();

                return $this->forward("AppBundle:VO/VO:contactList", array('serial' => $serial, 'message' => $message));
            }
            //@codeCoverageIgnoreEnd
        } else {
            return $this->render(
                ':vo/voManagement:modal_voContact.html.twig',
                array(
                    "contactForm" => $contactForm->createView(),
                    "comment" => $comment,
                    "profileId" => $profileid,
                    "serial" => $serial,
                    "dn" => $dn,
                    "mode" => $mode,
                )
            );
        }
    }

    /**
     * @param string $request ( integer $id, integer $serial)
     * @Route("/deleteContactHasProfil", name="deleteContactHasProfil")
     */
    public function deleteContactHasProfilAction(Request $request)
    {
        $idContact = $request->get('id');
        $serial = $request->get('serial');

        $em = $this->getDoctrine()->getManager();
        $contactAP = $this->getDoctrine()->getRepository("AppBundle:VO\VoContactHasProfile")->findOneBy(
            array("serial" => $serial, "contact_id" => $idContact)
        );

        $voModel = new ModelVO($this->container, $serial);
        $message = "Contact has been deleted.";
        try {
            $em->remove($contactAP);
            $em->flush();
            $voModel->setLastChangeDate();

            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            $message = "Can't delete contact... - ".$e->getMessage();
        }

        //@codeCoverageIgnoreEnd

        return $this->forward("AppBundle:VO/VO:contactList", array('serial' => $serial, 'message' => $message));
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/vomsServerList", name="vomsServerList")
     */
    public function vomsServerListAction(Request $request)
    {
        $serial = $request->get('serial');

        $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("serial" => $serial));

        /** @var  $voHeader \AppBundle\Entity\VO\VoHeader */
        $voHeader = $this->getDoctrine()->getRepository("AppBundle:VO\VoHeader")->findOneBy(
            array("id" => $vo->getHeaderId())
        );


        $perunUsed = false;
        if ($voHeader->getPerun() == 1) {
            $perunUsed = true;
        }

        $message = $request->get("message");
        if (!isset($message)) {
            $message = null;
        }
        // get Voms Server List
        $voModel = new ModelVO($this->container, $serial);
        $vomsList = $voModel->getVOMSList();


        return $this->render(
            ':vo/voManagement:template_voVomsInformation.html.twig',
            array(
                "vomsList" => $vomsList,
                "serial" => $serial,
                "perunUsed" => $perunUsed,
                "message" => $message,
            )
        );
    }

    /**
     * @param $request (String $hostname, integer $serial)
     * @Route("/vomsServerForm", name="vomsServerForm")
     */
    public function vomsServerFormAction(Request $request)
    {
        $hostname = $request->get('id');
        $serial = $request->get('serial');
        $mode = $request->get("mode");


        if ($mode == "update") {
            $hostname = $request->get("vo_voms_server")["hostname"];
            $serial = $request->get("vo_voms_server")["serial"];
        }

        if ($hostname != "") {
            $voms = $this->getDoctrine()->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(
                array("serial" => $serial, "hostname" => $hostname)
            );

            $voms->setIsVomsadminServer((bool)$voms->getIsVomsadminServer());
        } else {
            $voms = new VoVomsServer();
            $voms->setSerial($serial);
        }
        $vomsForm = $this->createForm('AppBundle\Form\VO\VoVomsServerType', $voms);

        $lquery = new \Lavoisier\Query($this->container->getParameter('lavoisierUrl'), 'voms-endpoint', 'lavoisier');
        $lvomss = simplexml_load_string($lquery->execute());
        if ($hostname != "") {
            $vomsForm->add('hostname', HiddenType::class);
            $mode = 'update';
        } else {
            $choices = array();
            foreach ($lvomss as $lvoms) {
                $choices[(string)$lvoms->HOSTNAME] = (string)$lvoms->HOSTNAME;
            }
            $select = array('-- Please select --' => '');
            $list = array_merge($select, $choices);
            asort($list);

            $vomsForm->add(
                'hostname',
                ChoiceType::class,
                array(
                    'choices' => $list,
                    'attr' => array(
                        'class' => 'form-control input-sm',
                    ),
                )
            );
            $mode = 'create';
        }
        $vomsForm->handleRequest($request);

        if ($vomsForm->isSubmitted()) {
            try {

                $em = $this->getDoctrine()->getManager();
                if ($voms->getIsVomsadminServer() == true) {
                    $voms->setIsVomsadminServer(1);
                } else {
                    $voms->setIsVomsadminServer(0);
                }

                $em->persist($voms);
                $em->flush();
                $serial = $voms->getSerial();
                $voModel = new ModelVO($this->container, $serial);
                $voModel->setLastChangeDate();
                $message = $voms->getHostname()." has been saved.";

                return $this->forward(
                    "AppBundle:VO/VO:vomsServerList",
                    array('serial' => $serial, 'message' => $message)
                );

                //@codeCoverageIgnoreStart
            } catch (Exception $e) {
                $message = "Can't save vomsAdminServer - ".$e->getMessage();

                return $this->forward(
                    "AppBundle:VO/VO:vomsServerList",
                    array('serial' => $serial, 'message' => $message)
                );
            }
            //@codeCoverageIgnoreEnd

        } else {

            return $this->render(
                ':vo/voManagement:modal_voVomsServer.html.twig',
                array(
                    "vomsForm" => $vomsForm->createView(),
                    'mode' => $mode,
                    "hostname" => $hostname,
                )
            );
        }
    }

    /**
     * @param string $request ( integer $id, integer $serial)
     * @Route("/deleteVomsServer", name="deleteVomsServer")
     */
    public function deleteVomsServerAction(Request $request)
    {
        $hostname = $request->get('id');
        $serial = $request->get('serial');

        try {
            $em = $this->getDoctrine()->getManager();
            $voms = $this->getDoctrine()->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(
                array("serial" => $serial, "hostname" => $hostname)
            );

            $voModel = new ModelVO($this->container, $serial);
            $message = $hostname." has been deleted.";
            $em->remove($voms);
            $em->flush();
            $voModel->setLastChangeDate();

            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            $message = "Can't delete vomsServer - ".$e->getMessage();
        }

        //@codeCoverageIgnoreEnd

        return $this->forward("AppBundle:VO/VO:vomsServerList", array('serial' => $serial, 'message' => $message));
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/vomsGroupList", name="vomsGroupList")
     */
    public function vomsGroupListAction(Request $request)
    {

        $serial = $request->get("serial");
        $message = $request->get("message");
        if (!isset($message)) {
            $message = null;
        }
        $voModel = new ModelVO($this->container, $serial);
        // get Voms Group List
        $vomsGroup = $voModel->getVOMSGroup();

        return $this->render(
            ':vo/voManagement:template_voVomsGroup.html.twig',
            array(
                "vomsGroup" => $vomsGroup,
                "serial" => $serial,
                "message" => $message,
            )
        );
    }

    /**
     * @param string $request (integer $id, integer $serial)
     * @Route("/vomsGroupForm", name="vomsGroupForm")
     */
    public function vomsGroupFormAction(Request $request)
    {
        $id = $request->get('id');
        $serial = $request->get('serial');
        $mode = "create";
        $groupName = null;
        $em = $this->getDoctrine()->getManager();
        if ($id != "") {
            $mode = "update";

            /** @var  $vomsGroup \AppBundle\Entity\VO\VoVomsGroup */
            $vomsGroup = $em->getRepository("AppBundle:VO\VoVomsGroup")->findOneBy(array("id" => $id));
            $vomsGroup->setIsGroupUsed((bool)$vomsGroup->getIsGroupUsed());
            $groupName = $vomsGroup->getGroupRole();
        } else {
            $vomsGroup = new VoVomsGroup();
            $vomsGroup->setSerial($serial);
        }
        $vomsGroupForm = $this->createForm(
            'AppBundle\Form\VO\VoVomsGroupType',
            $vomsGroup,
            array(
                'action' => $this->generateUrl('vomsGroupForm'),
                'method' => 'POST',
            )
        );
        $vomsGroupForm->handleRequest($request);
        if ($vomsGroup->getDescription() == null) {
            $vomsGroup->setDescription("");
        }

        if ($vomsGroupForm->isSubmitted()) {
            try {
                $em->persist($vomsGroup);
                $em->flush();
                $serial = $vomsGroup->getSerial();
                $voModel = new ModelVO($this->container, $serial);
                $voModel->setLastChangeDate();
                $message = $vomsGroup->getGroupRole()." has been saved.";

                return $this->forward(
                    "AppBundle:VO/VO:vomsGroupList",
                    array('serial' => $serial, 'message' => $message)
                );

                //@codeCoverageIgnoreStart
            } catch (Exception $e) {
                $message = "Can't save vomsGroup - ".$e->getMessage();

                return $this->forward(
                    "AppBundle:VO/VO:vomsGroupList",
                    array('serial' => $serial, 'message' => $message)
                );
            }
            //@codeCoverageIgnoreEnd

        } else {
            return $this->render(
                ':vo/voManagement:modal_voVomsGroup.html.twig',
                array(
                    "vomsGroupForm" => $vomsGroupForm->createView(),
                    'vomsGroupId' => $id,
                    'serial' => $serial,
                    "mode" => $mode,
                    "groupName" => $groupName,
                )
            );
        }
    }

    /**
     * @param string $request ( integer $id, integer $serial)
     * @Route("/deleteVomsGroup", name="deleteVomsGroup")
     */
    public function deleteVomsGroupAction(Request $request)
    {
        $id = $request->get('id');
        try {
            $em = $this->getDoctrine()->getManager();

            /** @var  $vomsGroup \AppBundle\Entity\VO\VoVomsGroup */
            $vomsGroup = $em->getRepository("AppBundle:VO\VoVomsGroup")->findOneBy(array("id" => $id));
            $serial = $vomsGroup->getSerial();

            $voModel = new ModelVO($this->container, $serial);
            $message = $vomsGroup->getGroupRole()." has been deleted.";
            $em->remove($vomsGroup);
            $em->flush();
            $voModel->setLastChangeDate();

            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            $message = "Can't delete vomsGroup - ".$e->getMessage();
        }
        //@codeCoverageIgnoreEnd

        // get Voms Group List
        return $this->forward("AppBundle:VO/VO:vomsGroupList", array('serial' => $serial, 'message' => $message));
    }


    /**
     * @Route("/manageAupFile/serial/{serial}", name="manageAupFile")
     * @param integer $serial
     */
    public function manageAupFileAction(Request $request, $serial)
    {
        $voSerial = $serial;

        $finder = null;
        $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("serial" => $voSerial));
        $voHeaderForm = $this->getDoctrine()->getRepository("AppBundle:VO\VoHeader")->find($vo->getHeaderId());
        $message = $request->get("message");


        if ($voHeaderForm) {
            $voName = $voHeaderForm->getName();
            $aupValue = $voHeaderForm->getAup();


            $aupFileForm = $this->createForm('AppBundle\Form\VO\ManageAupFileType');

            $finder = new Finder();
            $aupFileHandler = new \AppBundle\Model\VO\AUPFileHandler($voName, $this->getParameter("aupUrl"));
            $finder = $aupFileHandler->find();

            $aupFileForm->handleRequest($request);

            if ($aupFileForm->isSubmitted()) {

                try {
                    $file = $aupFileForm->getData()->getAupFile();
                    $file->move($this->getParameter("aupurl"), $aupFileForm->getData()->getName());
                    $message = "The file has been successfully added ";

                    return $this->forward(
                        "AppBundle:VO/VO:manageAupFile",
                        array('serial' => $serial, 'message' => $message)
                    );

                    //@codeCoverageIgnoreStart
                } catch (Exception $e) {
                    $message = "Error on file upload, please <a href=\"{{path('contact')}}\">Contact us</a> ";

                    return $this->forward(
                        "AppBundle:VO/VO:manageAupFile",
                        array('serial' => $serial, 'message' => $message)
                    );
                }
                //@codeCoverageIgnoreEnd

            }


            return $this->render(
                'vo/voManagement/manageAupFile.html.twig',
                array(
                    'aupFileForm' => $aupFileForm->createView(),
                    'serial' => $serial,
                    'aupFileList' => $finder,
                    'voname' => $voName,
                    'aupValue' => $aupValue,
                    'message' => $message,
                )
            );
        }
    }


    /**
     * get Detail for a VO by permalink
     * @Route("/view/voname/{voName}", name="VoDetailPermalink")
     *
     */
    public function voDetailPermalinkAction($voName)
    {

        /** @var $vo /AppBundle/VO/Vo */
        $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => $voName));


        if ($vo != null) {

            $arrayInfo = $this->getVoDetailInformation($vo->getSerial());

            return $this->render(
                ":vo:voDetail.html.twig",
                array(
                    "tabgeneral" => $arrayInfo["general"],
                    "description" => $arrayInfo["description"],
                    "aup" => $arrayInfo["aup"],
                    "acknowledgment" => $arrayInfo["acknowledgment"],
                    "resources" => $arrayInfo["resources"],
                    "cloud" => $arrayInfo["cloud"],
                    "otherrequirements" => $arrayInfo["other"],
                    "contacts" => $arrayInfo["contacts"],
                    "mailinglist" => $arrayInfo["mailing"],
                    "vomslist" => $arrayInfo["vomsList"],
                    "vomsgroup" => $arrayInfo["vomsGroup"],
                )
            );
        } else {
            $this->addFlash("danger", "Unknown vo name... Please enter a valid vo name.");

            return $this->render(":vo:voDetail.html.twig");
        }
    }

    /**
     * get Detail for a VO by ajax call
     * @Route("/voDetailAjax", name="voDetailAjax")
     * @param Request $request
     */
    public function voDetailAjaxAction(Request $request)
    {

        $voId = $request->get("serial");

        $arrayInfo = $this->getVoDetailInformation($voId);

        return $this->render(
            ":vo/templates:template_modal_voDetail.html.twig",
            array(
                "tabgeneral" => $arrayInfo["general"],
                "description" => $arrayInfo["description"],
                "aup" => $arrayInfo["aup"],
                "acknowledgment" => $arrayInfo["acknowledgment"],
                "resources" => $arrayInfo["resources"],
                "cloud" => $arrayInfo["cloud"],
                "otherrequirements" => $arrayInfo["other"],
                "contacts" => $arrayInfo["contacts"],
                "mailinglist" => $arrayInfo["mailing"],
                "vomslist" => $arrayInfo["vomsList"],
                "vomsgroup" => $arrayInfo["vomsGroup"],
            )
        );

    }

    /**
     * download a aup file
     * @Route("/downloadAUP/{file}", name="downloadAUP")
     * @param Request $request
     */
    public function downloadAUPAction($file)
    {
        $fullpath = $this->container->getParameter('aupUrl').$file;


        $response = new Response();

        $response->headers->set('Content-Type', 'text/force-download');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="'.basename($fullpath).'"'
        );
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        //$this->getResponse()->setHttpHeader('Content-Length', $file->getSize());
        $response->headers->set('Connection', 'close');


        $response->setContent(file_get_contents($fullpath));

        return $response;
    }


    /**
     * get Detail for a VOMS of a vo of "Other VO tab"
     * @Route("/vomsDetailAjax", name="vomsDetailAjax")
     * @param Request $request
     */
    public function VOMSDetailAjaxAction(Request $request)
    {

        //get the vo serial
        $voId = $request->get("serial");
        $host = $request->get("host");
        $colspan = $request->get("colspan");

        $hostFormated = preg_replace('/[^a-z0-9]/i', "", $host);

        if ($voId == "" && $host == "") {
            return $this->render(
                ":vo/templates:template_list_VOMSDetail.html.twig",
                array("error" => "An error occured : can't get voms detail - voId and/or host is/are empty")
            );
        }

        //get the model to call doctrine find method
        $voModel = new ModelVO($this->container, $voId);

        //voms part
        $voms = $voModel->getVOMSList($host);


        return $this->render(
            ":vo/templates:template_list_VOMSDetail.html.twig",
            array(
                "serial" => $voId,
                "host" => $hostFormated,
                "colspan" => $colspan,
                "voms" => $voms,
            )
        );
    }


    /**
     * update last validation date for a VO
     * @Route("/validateVOAjax", name="validateVOAjax")use AppBundle\Model\VO\VoChecker;
     * @param Request $request
     * @return Response
     */
    public function validateVOAjaxAction(Request $request)
    {

        //get the vo serial
        $voId = $request->get("serial");

        if ($voId != "") {


            $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("serial" => $voId));

            //get the model to call doctrine find method
            $voModel = new ModelVO($this->container, $voId);

            $result = $voModel->setVoValidation();


            $body = "\nAn update has been made on VO [".$vo->getName()."] validation date\n\n".
                "Made by user : ".$this->getUser()->getDn()."\n".
                "To check it, go to : https://operations-portal.egi.eu/vo/update/serial/".$voId
                ."\n".
                "\nRegards,\n".
                "User community Support team";

            try {
                //send message to cic-information
                $mailer = new Mailer();
                $mailer->contactVoManager(
                    $voId,
                    "[OPERATIONS PORTAL ".
                    ($this->container->get("kernel")->getEnvironment() != "prod" ? " - ".$this->container->get(
                            "kernel"
                        )->getEnvironment()."]" : "]"),
                    $body,
                    $this->container
                );

                //@codeCoverageIgnoreStart
            } catch (\Exception $e) {
                $result = array(
                    "res" => "Error on updating VO validation date... [".$e->getMessage()."]",
                    "isSuccess" => 0,
                );
            }
            //@codeCoverageIgnoreEnd

        } else {
            $result = array("res" => "Error on updating VO validation date, no serial parameter...", "isSuccess" => 0);

        }

        return new Response(json_encode($result));

    }


    /**
     * @Route("/askforPerunAjax/", name="askforPerunAjax")
     */
    public function askForPerunAjaxAction(Request $request)
    {

        $message = null;

        $serial = $request->get("serial");


        /** @var  $vo /AppBundle/Entity/VO/Vo */
        $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("serial" => $serial));

        $vo->setNeedVomsHelp(0);


        /** @var  $voHeader /AppBundle/Entity/VO/VoHeader */
        $voHeader = $this->getDoctrine()->getRepository("AppBundle:VO\VoHeader")->findOneBy(array("serial" => $serial));

        $voHeader->setPerun(1);

        //persist perun modification on voheader
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vo);
            $em->persist($voHeader);
            $em->flush();
            $em->refresh($vo);
            $em->refresh($voHeader);

            $message = "Your request for Perun has been submitted, thanks !";
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        if ($this->get("kernel")->getEnvironment() != "test") {
            mailer::askForPerun($serial, $this->container);
        }

        return $this->forward("AppBundle:VO/VO:vomsServerList", array('serial' => $serial, 'message' => $message));

    }

    /**
     * @Route("/synoptic", name="synoptic")
     */
    public function synopticAction()
    {

        $this->user = $this->getUser();
        $isSuUser = $this->user->isSuUser();

        if ($isSuUser) {

            $check = new VoChecker($this->container);
            $tabvopb = $check->checkVoFields();
            $tabicons = array(
                "missing" => array("fa fa-times-circle text-danger", 0, "brown"),
                "incorrect" => array("fa fa-exclamation text-warning", 1, "orange"),
                "ok" => array("fa fa-check-circle text-success", 5, "darkgreen"),
                "broken" => array("fa fa-unlink text-warning", 2, "orange"),
            );
            $tabreport = $check->getLastReport();


            return $this->render(
                ":vo/templates:template_tab_badVOList.html.twig",
                array(
                    "tabvopb" => $tabvopb,
                    "tabicons" => $tabicons,
                    "tabreport" => $tabreport,
                )
            );
        } else {

            $message = "You have not been recognized as Super User.
            Consequently you are not authorized to access this page.";

            return $this->render(
                "@Twig/Exception/errorAuthenticationFailed.html.twig",
                array(
                    "message" => $message,
                )
            );
        }
    }

    /**
     * @Route("/sendReport", name="sendReport")
     * @Method("POST")
     */
    public function sendReportAction(Request $request)
    {

        $voSerial = $request->get("voserial");

        $mailSubject = $request->get("mail_subject");

        $mailBody = $request->get("body");

        $body = "\nDifferent values recorded in your VO ID card are incorrect or outdated.\n\n".
            "Could you check and correct it ?\n".
            "https://operations-portal.egi.eu/vo/update/serial/".$voSerial
            ."\n\n"
            ."\nRegards,\n".
            "Operations Portal Team";

        try {
            $report = new VoReport();
            $report->setSerial($voSerial);
            $report->setReportBody($body);
            $em = $this->getDoctrine()->getManager();
            $em->persist($report);
            $em->flush();
            $mailer = new Mailer();
            $mailer->contactVoManager($voSerial, $mailSubject, $body, $this->container);
            $message = "Email has been sent to VO managers";
            $this->addFlash("success", $message);

            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            $message = "A Problem occurs - The Update is impossible";
            $this->addFlash("danger", $message);
        }
        //@codeCoverageIgnoreEnd


        return $this->redirect($this->generateUrl("registerUpdate"));

    }


    /**
     * @param Request $request
     * @Route("/userTracking", name="userTracking")
     */
    public function userTrackingAction(Request $request)
    {
        $form = $this->createForm(UserTrackingType::class);


        if ($request->isMethod('POST')) {
            //get the sent form
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                try {
                    /** @var  $qb \Doctrine\ORM\QueryBuilder */
                    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();


                    $qb->select('vu')
                        ->from("AppBundle:VO\VoUsers", "vu")
                        ->where("vu.dn = :dn")
                        ->andWhere("vu.vo = :vo")
                        ->setParameter("dn", $form->get("DN")->getData())
                        ->setParameter("vo", $form->get("vo")->getData());


                    $query = $qb->getQuery();

                    $voUserEmailRecord = $query->getSingleResult();


                    if ($voUserEmailRecord != null) {
                        $voUserEmail = $voUserEmailRecord->getEmail();
                    } else {
                        $voUserEmail = "anonymous@anonymous.com";
                    }

                    if ($voUserEmail <> "anonymous@anonymous.com") {

                        $mailBody = $this->render(
                            ':vo/templates:template-mail.txt.twig',
                            array(
                                'dn' => $form->get('DN')->getData(),
                                'message' => $form->get('body')->getData(),
                            )
                        );


                        $mail = new Message(
                            $this->container,
                            $form->get("subject")->getData(),
                            $mailBody,
                            array(strtolower($voUserEmail)),
                            'USER-TRACKING'
                        );

                        $mail->setReplyTo(
                            array(strtolower($form->get("email")->getData()) => $form->get("name")->getData())
                        );

                        // send email to user tracked
                        $mailer = $this->get('mailer');

                        $mailer->send($mail->getMail());

                        // send confirmation mail to sender
                        $mail->getMail()->setTo(
                            array(strtolower($form->get("email")->getData()) => $form->get("name")->getData())
                        );
                        $mail->setSubject("[User Tracking Confirmation Mail] ".$form->get("subject")->getData());

                        $mailer->send($mail->getMail());

                        // send information mail to webmaster
                        $mail->getMail()->setTo($this->container->getParameter("webMasterMail"));
                        $mail->setSubject(
                            "[User Tracking Tool has been used by ".$this->getUser()->getUsername()."]=>".$form->get(
                                "subject"
                            )->getData()
                        );

                        $mailer->send($mail->getMail());

                        $sender = strtolower($form->get("email")->getData());
                        $this->addFlash(
                            "success",
                            "
                            <p>Your mail was sent in blind copy to: " . $sender .
                            "<br/>Your message was sent to:  " . $form->get('DN')->getData() .
                            "<br/>You will be notified by mail in a short time.</p>"
                        );


                        return $this->redirect($this->generateUrl("userTracking"));
                    } else {
                        $this->addFlash(
                            "danger",
                            "We are sorry but we are not able to retrieve the email associated to the selected user."
                        );

                        return $this->redirect($this->generateUrl("userTracking"));

                    }

                    //@codeCoverageIgnoreStart
                } catch (\Exception $e) {
                    $this->addFlash("danger", "Unable to send your email... ".$e->getMessage());

                    return $this->redirect($this->generateUrl("userTracking"));

                }
                //@codeCoverageIgnoreEnd


            } else {
                $this->addFlash("danger", "Information of the user tracking form is incorrect...");

                return $this->redirect($this->generateUrl("userTracking"));
            }

        }

        return $this->render(
            ":vo:userTracking.html.twig",
            array('form' => $form->createView())
        );
    }

    /**
     * @param Request $request
     * @Route("/DNTrackingAjax", name="DNTrackingAjax")
     */
    public function DNTrackingAjaxAction(Request $request)
    {

        $dn = $request->get("q");


        try {
            /** @var  $qb \Doctrine\ORM\QueryBuilder */
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();

            $qb->select('vu')
                ->from("AppBundle:VO\VoUsers", "vu")
                ->where("vu.dn LIKE :dn")
                ->setParameter("dn", "%".$dn."%");


            $query = $qb->getQuery();

            $users = $query->getResult();

            $results = array();


            if ($users != null && count($users) != 0) {
                foreach ($users as $user) {
                    $results[$user->getDn()."---".$user->getVo()] = (string)$user->getDn()."   CN: ".$user->getUservo(
                        )."   VO: ".$user->getVo();
                }

                return new Response(json_encode($results));

            } else {
                return new Response(json_encode("No results...."), 500);
            }

            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            return new Response(json_encode("[ ".$e->getCode()." ] - ".$e->getMessage()), 500);
        }
        //@codeCoverageIgnoreEnd

    }

    /**
     * @param Request $request
     * @Route("/EmailTrackingAjax", name="EmailTrackingAjax")
     */
    public function EmailTrackingAjaxAction(Request $request)
    {

        if($this->getUser()->isSecurityOfficer("egi")){
            $dn = $request->get("DN");
            $vo = $request->get("VO");

            try {
                /** @var  $qb \Doctrine\ORM\QueryBuilder */
                $qb = $this->getDoctrine()->getManager()->createQueryBuilder();

                $qb->select('vu')
                    ->from("AppBundle:VO\VoUsers", "vu")
                    ->where("vu.dn = :dn")
                    ->andWhere("vu.vo = :vo")
                    ->setParameter("dn", $dn)
                    ->setParameter("vo", $vo);

                $query = $qb->getQuery();
                $results = $query->getSingleResult();

                if ($results != null ) {
//                    return new Response(json_encode($results->getEmail()));
                    return new Response(json_encode($results->getEmail()));

                } else {
                    return new Response(json_encode("No results...."), 500);
                }

                //@codeCoverageIgnoreStart
            } catch (\Exception $e) {
                return new Response(json_encode("[ ".$e->getCode()." ] - ".$e->getMessage()), 500);
            }
            //@codeCoverageIgnoreEnd
        }

        return new Response(json_encode("anonymous@anonymous.com"));


    }

    /**
     * @Route("/security", name="security")
     */
    public function securityAction()
    {
        if ($this->getUser()->isSecurityOfficer("project")) {
            $modelVO = new ModelVO($this->container, null);
            $securityList = $modelVO->getVoSecurityMailingList();

            return $this->render(":vo:securityList.html.twig", array("securityList" => $securityList));

        } else {
            $message = "You have not been recognized as an EGI Security Officer. Consequently you are not authorized to access to this URL.";

            return $this->render(
                "@Twig/Exception/errorAuthenticationFailed.html.twig",
                array(
                    "message" => $message,
                )
            );
        }

    }

//    /**
//     * @Route("/uploadAupFile", name="uploadAupFile")
//     */
//    public function uploadAupFileAction(Request $request)
//    {
//        $voSerial = $request->get('voserial');
//
//        $finder = null;
//        /** @var  $vo \AppBundle\Entity\VO\Vo */
//        $vo = $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findby(array("serial" => $voSerial));
//        $voHeaderForm = $this->getDoctrine()->getRepository("AppBundle:VO\VoHeader")->find($vo->getHeaderId());
//
//        if ($voHeaderForm) {
//            $voName = $voHeaderForm->getName();
//            $aupValue = $voHeaderForm->getAup();
//
//
//            $upload_aup_form = new uploadAupFileForm($voSerial);
//
//            if ($request->isMethod('post')) {
//                $upload_aup_form->bind($request->get('uploadAup'), $request->getFiles('uploadAup'));
//
//                if ($upload_aup_form->isSubmitted()) {
//                    $upload_aup_form->saveAUPFile();
//                }
//            }
//
//            $finder = new Finder();
//            $aupFileHandler = new AUPFileHandler($voName, sfConfig::get("op_aup_files_dir"));
//            $finder = $aupFileHandler->find();
//        }
//    }


    /**
     * @param $voSerial
     * @param VoContacts $voContact
     * @param VoHeader $voHeader
     * @return array
     * @throws \Exception
     *
     * @codeCoverageIgnore
     *
     */

    public function createTicketGgusRegistration(
        $voSerial,
        \AppBundle\Entity\VO\VoContacts $voContact,
        \AppBundle\Entity\VO\VoHeader $voHeader
    ) {
        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/Vo/VOM');


        $loader = new Loader($wf_paths);
        $containerBd = new ContainerBuilder();
        $loader->load(array(), $containerBd);

        $workflow = $containerBd->get("workflow_vo");
        $helpdesk = $this->get('ggus_helpdesk_ops');
        $helpdesk->setLavoisierNotification(false);

        $discipline_data = $this->getDoctrine()->getRepository("AppBundle:VO\VoDisciplines")->findBy(
            array("vo_id" => $voSerial)
        );

        $dataDiscipline = array();

        foreach ($discipline_data as $disciplines) {
            $dataDiscipline[] = $this->getDoctrine()->getRepository("AppBundle:VO\Disciplines")->findOneBy(
                array("discipline_id" => $disciplines->getDisciplineId())
            )->getDisciplineLabel();
        }

        $managerEmail = $voContact->getEmail();
        if ($this->container->get("kernel")->getEnvironment() != 'prod') {
            $managerEmail = $this->getParameter("webMasterMail");
        }

        $ticketREG = $workflow->ticketFromStepId(
            'registration',
            $helpdesk->getTicketInstance(),
            array(
                'user' => $this->getUser()->getUsername(),
                'manager' => sprintf('%s %s', $voContact->getFirstName(), $voContact->getLastName()),
                'manager_email' => $managerEmail,
                'vo_name' => $voHeader->getName(),
                'discipline' => implode(",", $dataDiscipline),
                'aup_link' => $this->displayAUP("Text", "text", false),
                'perma_link' => sprintf(
                    "https://%s%s",
                    $_SERVER['HTTP_HOST'],
                    $this->generateUrl("voUpdate", array("serial" => $voSerial))
                ),

            )
        );
        $ticketREG->setWorkflow("workflow_VO");
        $ticketREG->setHelpdesk("ggus_helpdesk_ops");
        $ticketREG->setResponsibleUnit('Operations');

        $pLinkTicketCreated = null;
        try {
            $ggus_id = $helpdesk->createTicket($ticketREG);
            $pLinkTicketCreated = $helpdesk->getTicketPermalink($ggus_id);
        } catch (Exception $e) {
            if ($this->container->get('kernel')->getEnvironment() == 'prod') {
                $GGUSTicketCreated = null;
                $ggus_id = 0;
            } else {
                return array("error" => "Error on ggus ticket creation - ".$e->getMessage());
            }
        }

        return array(
            "ggus_id" => $ggus_id,
            "pLinkTicketCreated" => $pLinkTicketCreated,
        );
    }


    /**
     *  return HTML code to update result of url checker
     * @Route("/voUrlCheckReport/voname/{voName}", name="voUrlCheckReport")
     *
     */
    public function voUrlCheckReportAction($voName)
    {

        try {

            //lavoisier call to get url checker for voName
            $lavoisierUrl = $this->container->getParameter("lavoisierUrl");
            $lquery = new Query($lavoisierUrl, 'urls_checker', 'lavoisier');
            $lquery->setMethod('POST');
            $lquery->setPostFields(array("vo" => $voName));

            $xmlStream = json_decode(
                json_encode(simplexml_load_string($lquery->execute(), 'SimpleXMLElement', LIBXML_NOCDATA)),
                true
            );

            //Enrollment URL part
            $arrayEnrollmentUrl = array();

            $arrayEnrollmentUrl["testDate"] = $xmlStream["GenerationDate"];
            $arrayEnrollmentUrl["urlChecked"] = $xmlStream["EnrollmentUrl"]["value"];
            $arrayEnrollmentUrl["httpReturnedStatus"] = $xmlStream["EnrollmentUrl"]["code"];
            $arrayEnrollmentUrl["httpStatusDescription"] = $xmlStream["EnrollmentUrl"]["description"];
            $arrayEnrollmentUrl["time"] = $xmlStream["EnrollmentUrl"]["time"];
            if ($xmlStream["EnrollmentUrl"]["last_error"] != null) {
                $arrayEnrollmentUrl["log"] = $xmlStream["EnrollmentUrl"]["last_error"];
            }


            //HomePage Url part
            $arrayHomepageUrl = array();

            $arrayHomepageUrl["testDate"] = $xmlStream["GenerationDate"];
            $arrayHomepageUrl["urlChecked"] = $xmlStream["HomepageUrl"]["value"];
            $arrayHomepageUrl["httpReturnedStatus"] = $xmlStream["HomepageUrl"]["code"];
            $arrayHomepageUrl["httpStatusDescription"] = $xmlStream["HomepageUrl"]["description"];
            $arrayHomepageUrl["time"] = $xmlStream["HomepageUrl"]["time"];
            if ($xmlStream["HomepageUrl"]["last_error"] != null) {
                $arrayHomepageUrl["log"] = $xmlStream["HomepageUrl"]["last_error"];
            }

            //Voms List Members part
            $arrayVomsListMembers = array();

            //if there is more that one URL
            if (!isset($xmlStream["VomsListMembers"]["Url"]["@attributes"]["Id"])) {

                foreach ($xmlStream["VomsListMembers"]["Url"] as $key => $val) {

                    $arrayVomsListMembers[$val["@attributes"]["Id"]]["testDate"] = $xmlStream["GenerationDate"];

                    $arrayVomsListMembers[$val["@attributes"]["Id"]]["urlChecked"] = $val["value"];
                    $arrayVomsListMembers[$val["@attributes"]["Id"]]["httpReturnedStatus"] = $val["code"];
                    $arrayVomsListMembers[$val["@attributes"]["Id"]]["httpStatusDescription"] = $val["description"];
                    $arrayVomsListMembers[$val["@attributes"]["Id"]]["time"] = $val["time"];
                    if ($val["last_error"] != null) {
                        $arrayVomsListMembers[$val["@attributes"]["Id"]]["log"] = $val["last_error"];
                    }
                }
            } else {
                $arrayVomsListMembers[$xmlStream["VomsListMembers"]["Url"]["@attributes"]["Id"]]["testDate"] = $xmlStream["GenerationDate"];

                $arrayVomsListMembers[$xmlStream["VomsListMembers"]["Url"]["@attributes"]["Id"]]["urlChecked"] = $xmlStream["VomsListMembers"]["Url"]["value"];
                $arrayVomsListMembers[$xmlStream["VomsListMembers"]["Url"]["@attributes"]["Id"]]["httpReturnedStatus"] = $xmlStream["VomsListMembers"]["Url"]["code"];
                $arrayVomsListMembers[$xmlStream["VomsListMembers"]["Url"]["@attributes"]["Id"]]["httpStatusDescription"] = $xmlStream["VomsListMembers"]["Url"]["description"];
                $arrayVomsListMembers[$xmlStream["VomsListMembers"]["Url"]["@attributes"]["Id"]]["time"] = $xmlStream["VomsListMembers"]["Url"]["time"];
                if ($xmlStream["VomsListMembers"]["Url"]["last_error"] != null) {
                    $arrayVomsListMembers[$xmlStream["VomsListMembers"]["Url"]["@attributes"]["Id"]]["log"] = $xmlStream["VomsListMembers"]["Url"]["last_error"];
                }
            }

            return $this->render(
                ":vo/voManagement:voUrlCheckReport.html.twig",
                array(
                    "voName" => $voName,
                    "arrayEnrollmentUrl" => $arrayEnrollmentUrl,
                    "arrayHomepageUrl" => $arrayHomepageUrl,
                    "arrayVomsListMembers" => $arrayVomsListMembers,
                )
            );

        } catch (exception $e) {
            $this->addFlash("danger", "Sorry, no data to display");

            return $this->render(":vo/voManagement:voUrlCheckReport.html.twig");

        }


    }


    /**
     * get list of all vo in production
     * @return array
     * @throws \Lavoisier\Exceptions\HTTPStatusException
     *
     */
    private function getVoListOther()
    {
        $lavoisierUrl = $this->container->getParameter("lavoisierurl");

        try {
            //lavoisier call
            $lquery = new Query($lavoisierUrl, 'VoEntries', 'lavoisier');
            $lquery2 = new \Lavoisier\Query($lavoisierUrl, "VO_full", 'lavoisier');

            $lquery->setPath("/e:entries/e:entries[e:entry[@key='status']/text()='Production']");
            $linkVOListOther = $lquery->getUrl()."&download=true";

            $linkVOFull = $lquery2->getUrl()."&download=true";

            $hydrator = new EntriesHydrator();

            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();
            $voOtherList = $result->getArrayCopy();


            return array(
                "voListOther" => $voOtherList,
                "voListOtherLink" => $linkVOListOther,
                "voFullLink" => $linkVOFull,
            );

            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            return array("error" => $e->getMessage());
        }
        // @codeCoverageIgnoreEnd
    }


    /**
     * create GGUS ticket Number for VO Support Unit creation follow-up
     * @param $voSerial
     * @param VoHeader $headerValues
     * @param $voManagerEmail
     * @return null
     * @throws \Exception
     *
     *
     * @codeCoverageIgnore
     *
     */
    private function createTicketForGgusSUCreation($voSerial, VoHeader $headerValues, $voManagerEmail)
    {


        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/Vo/VOM');


        $loader = new Loader($wf_paths);
        $containerBd = new ContainerBuilder();
        $loader->load(array(), $containerBd);

        $workflow = $containerBd->get("workflow_vo");
        $helpdesk = $this->get('ggus_helpdesk_ops');
        $helpdesk->setLavoisierNotification(false);


        $voManagers = $this->getDoctrine()->getRepository("AppBundle:VO\VoContactHasProfile")->findBy(
            array("serial" => $voSerial, "user_profile_id" => 1)
        );


        $manager_list = '';
        foreach ($voManagers as $voManager) {
            /** @var  $voContact \AppBundle\Entity\VO\VoContacts */
            $voContact = $this->getDoctrine()->getRepository("AppBundle:VO\VoContacts")->findOneBy(
                array("id" => $voManager->getContactId())
            );
            $manager_list .= sprintf(
                    "%s %s : %s",
                    $voContact->getFirstName(),
                    $voContact->getLastName(),
                    $voContact->getEmail()
                )."\n";
        }

        $ticketGGUSSU = $workflow->ticketFromStepId(
            'ggus_su_request',
            $helpdesk->getTicketInstance(),
            array(
                'user' => $this->getUser()->getUsername(),
                'manager' => $this->getUser()->getUsername(),
                'manager_email' => $voManagerEmail,
                'vo_name' => $headerValues->getName(),
                'manager_list' => $manager_list,
                'perma_link' => sprintf(
                    "https://%s%s",
                    $_SERVER['HTTP_HOST'],
                    $this->generateUrl("voUpdate", array("serial" => $voSerial))
                ),
            )
        );

        $ticketGGUSSU->setWorkflow("workflow_vo");
        $ticketGGUSSU->setHelpdesk("ggus_helpdesk_ops");
        $ticketGGUSSU->setResponsibleUnit('EGI Catch-all Services');

        $pLinkTicketSUCreation = null;
        try {
            $ggus_id = $helpdesk->createTicket($ticketGGUSSU);
            $pLinkTicketSUCreation = $helpdesk->getTicketPermalink($ggus_id);
        } catch (Exception $e) {
            $env = $this->get("kernel")->getEnvironment();
            if ($env == 'prod') {
                $ggus_id = 0;
            } else {
                return "Error on ggus SU creation - ".$e->getMessage();
            }
        }

        $vo = $this->getDoctrine()->getRepository('AppBundle:VO\Vo')->findOneBy(array("serial" => $voSerial));
        $vo->setGgusTicketIdSuCreation($ggus_id);
        $em = $this->getDoctrine()->getManager();
        $em->persist($vo);
        $em->flush();

        return $pLinkTicketSUCreation;

    }

    /**
     * render AUP depending on its type
     **
     * @param <string> $aup_type AUP type (text | file | url)
     * @param <string> $aup_value AUP value
     * @param <bool> $format set to true to get formatted value depending on type, false to get litteral  value
     * @param <bool> $escapeJS set to true to escape string for JS content
     *
     * @codeCoverageIgnore
     */
    private function displayAUP($aup_type, $aup_value, $format = true, $escapeJS = false)
    {
        $htm = '';
        switch ($aup_type) {
            case "Url":
                if (!$format) {
                    $htm = $aup_value;
                } else {
                    $htm .= "<span><a class='message' href='$aup_value'>$aup_value</a></span>";
                }
                break;
            case "File":
                if (!$format) {
                    $htm = "http://".$_SERVER['HTTP_HOST'].$this->generateUrl(
                            'downloadAUP',
                            array("file" => $aup_value)
                        );
                } else {
                    $htm .= "<span><a class='file_link' href='http://".$_SERVER['HTTP_HOST'].$this->generateUrl(
                            'downloadAUP',
                            array('file' => $aup_value)
                        )."'>$aup_value</a></span>";
                }
                break;
            case "Text":
                if (!$format) {
                    $htm = $aup_value;
                } else {
                    $htm .= nl2br($aup_value);
                }
                break;
            default:
                $htm .= $aup_value;
        }

        if ($escapeJS) {
            $htm = myString::escapeToJS($htm);
        }

        return $htm;
    }

    /**
     * @param $listDiscipline
     * @param $viewDiscipline
     * @return string
     *
     * @codeCoverageIgnore
     */
    private function parseDiscipline($listDiscipline, $viewDiscipline)
    {

        $disciplineArray = "<ul>";
        foreach ($viewDiscipline->discipline as $discipline) {
            $child = "";
            if (isset($discipline->discipline)) {
                $child = $this->parseDiscipline($listDiscipline, $discipline);
            }

            $parentid = "";
            if (isset($discipline->parentid)) {
                $parentid = "parentid='$discipline->parentid'";
            }

            if (array_search($discipline->id, array_column($listDiscipline, 'id')) !== false) {
                $disciplineArray .= "<li $parentid class='jstree-open' id='$discipline->id' order='$discipline->order'><a href='#' class='jstree-clicked'>$discipline->value</a>$child";
            } else {
                $disciplineArray .= "<li $parentid id='$discipline->id' order='$discipline->order'><a href='#'>$discipline->value</a>$child";
            }
        }

        $disciplineArray .= "</ul>";

        return $disciplineArray;
    }

    private function getVoDetailInformation($voId)
    {

        //get the model to call doctrine find method
        $voModel = new ModelVO($this->container, $voId);

        //general info part
        $arrayGeneral = $voModel->constructVODetailGeneral();

        //description part
        $description = $voModel->constructVODetailDescription();

        //aup part
        $aup = $voModel->getAUP();

        //acknowlegement statements part
        $acknowledgment = $voModel->constructVODetailAcknowledgments();

        //resources part
        $resources = $voModel->constructVODetailResources();

        //cloud part
        $cloud = $voModel->constructVODetailCloud();

        //otehr requirements part
        $otherreq = $voModel->constructVODetailOtherReq();

        //contacts part
        $contacts = $voModel->getVOContacts();

        //mailing list part
        $mailingList = $voModel->constructVODetailsMailingList();

        //voms part
        $vomsList = $voModel->getVOMSList();

        //voms group part
        $vomsGroup = $voModel->getVOMSGroup();

        return array(
            "general" => $arrayGeneral,
            "description" => $description,
            "aup" => $aup,
            "acknowledgment" => $acknowledgment,
            "resources" => $resources,
            "cloud" => $cloud,
            "other" => $otherreq,
            "contacts" => $contacts,
            "mailing" => $mailingList,
            "vomsList" => $vomsList,
            "vomsGroup" => $vomsGroup,
        );

    }

}
