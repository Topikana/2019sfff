<?php

namespace AppBundle\Controller\VO;

use AppBundle\Entity\User;
use AppBundle\Entity\VO\Vo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Model\VO\ModelVO;


use Lavoisier\Query;
use Lavoisier\Hydrators\EntriesHydrator;

use AppBundle\Services\TicketingSystem\Workflow\Loader;
use AppBundle\Services\TicketingSystem\HelpDesk\OpsHelpdesk;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;



/**
 * Class VOAdminController
 * @package AppBundle\Controller
 * @Route("/voadmin")
 */
class VOAdminController extends Controller
{


    /**
     * @var $container \Symfony\Component\DependencyInjection\Container
     */
    protected $container;


    /**
     * @Route("/createVomsTicket/serial/{serial}", name="createVomsTicket")
     * @param integer $serial
     */
    public function createVomsTicketAction(Request $request)
    {
        //--------------------------------------------------------------------------------------------------------//
        //                                      I : GET THE SELECTED VO

//        dump($request);die;

        $voSerial = $request->get("serial");
        $vo =  $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("serial" => $voSerial));


        //get the VO manager
        $modelVO = new ModelVO($this->container, $voSerial);
        $arrayManager = $modelVO->getVoManageInfo();
        //--------------------------------------------------------------------------------------------------------//
        //                                      II : GET THE WORKFLOW VO SERVICE

        //1) get the directory to find service.xml
        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/VoAdmin/VOM');

        //2) load yml files that service is depending on (vomshelprequest and closeregistration)
        $loader = new Loader($wf_paths);
        $containerBd = new ContainerBuilder();
        $loader->load(array(),  $containerBd);

        //3) get service
        $workflow =  $containerBd->get("workflow_vo");
        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                                      III : GET THE GGUS SERVICE

        $helpdesk = $this->get('ggus_helpdesk_ops');
        $helpdesk->setLavoisierNotification(false);

        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                                      IV : GET THE TICKET STRUCTURE

        $ticketVOMSREQUEST = $workflow->ticketFromStepId(
            'voms_help_request',
            $helpdesk->getTicketInstance(),
            array(
                'manager' => $arrayManager['manager'],
                'manager_email' => $arrayManager['manager_email'],
                'vo_name' => $vo->getName(),
                'signature' => 'EGI Operations Portal - VO ID Card Management',
                'recipients' => 'EGI Operations Representatives',
                'perma_link' => $this->generateUrl("voUpdate", array("serial" => $voSerial)),
            )
        );

        $ticketVOMSREQUEST->setWorkflow("workflow_vo");
        $ticketVOMSREQUEST->setHelpdesk("ggus_helpdesk_ops");
        $ticketVOMSREQUEST->setResponsibleUnit('EGI Catch-all Services');



        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                         V : CREATE TICKET WITH GGUS SERVICE AND REGISTER IT IN DB

        $ggus_id = 0;
        try {
            $ggus_id = $helpdesk->createTicket($ticketVOMSREQUEST);


            $vo->setVomsTicketId($ggus_id);
            $em = $this->getDoctrine()->getManager();
            $em->persist($vo);
            $em->flush();
            //notify waiting VO
            $lavoisierUrl = $this->container->getParameter("lavoisierurl");
            $lquery = new Query($lavoisierUrl, 'VoWaitingList', 'notify');
            $lquery->execute();
            $message = 'Successful Creation of VOMS ticket for VO '.$vo->getName();
            $this->addFlash('success', $message);
        } catch (Exception $e) {
            $message = 'Unable to create VOMS ticket on "ggus_helpdesk_ops" helpdesk for VO '.$vo->getName()." with serial ".$voSerial;

            $this->addFlash('danger', $message." - ".$e->getMessage());
        }
        //--------------------------------------------------------------------------------------------------------//

        return $this->redirect($this->generateUrl("registerUpdate"));
    }



    /**
     * @Route("/createPerunsTicket/serial/{serial}", name="createPerunsTicket")
     * @param integer $serial
     */
    public function createPerunsTicketAction(Request $request)
    {
        //--------------------------------------------------------------------------------------------------------//
        //                                      I : GET THE SELECTED VO

//        dump($request);die;

        $voSerial = $request->get("serial");
        $vo =  $this->getDoctrine()->getRepository("AppBundle:VO\Vo")->findOneBy(array("serial" => $voSerial));


        //get the VO manager
        $modelVO = new ModelVO($this->container, $voSerial);
        $arrayManager = $modelVO->getVoManageInfo();
        //--------------------------------------------------------------------------------------------------------//
        //                                      II : GET THE WORKFLOW VO SERVICE

        //1) get the directory to find service.xml
        $wf_paths = $this->container->get('kernel')->locateResource('@AppBundle/Services/Workflow/VoAdmin/VOM');

        //2) load yml files that service is depending on (vomshelprequest and closeregistration)
        $loader = new Loader($wf_paths);
        $containerBd = new ContainerBuilder();
        $loader->load(array(),  $containerBd);

        //3) get service
        $workflow =  $containerBd->get("workflow_vo");
        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                                      III : GET THE GGUS SERVICE

        $helpdesk = $this->get('ggus_helpdesk_ops');
        $helpdesk->setLavoisierNotification(false);

        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                                      IV : GET THE TICKET STRUCTURE

        $ticketVOMSREQUEST = $workflow->ticketFromStepId(
            'perun_help_request',
            $helpdesk->getTicketInstance(),
            array(
                'manager' => $arrayManager['manager'],
                'manager_email' => $arrayManager['manager_email'],
                'vo_name' => $vo->getName(),
                'signature' => 'EGI Operations Portal - VO ID Card Management',
                'recipients' => 'EGI Operations Representatives',
                'perma_link' => $this->generateUrl("voUpdate", array("serial" => $voSerial)),
            )
        );

        $ticketVOMSREQUEST->setWorkflow("workflow_vo");
        $ticketVOMSREQUEST->setHelpdesk("ggus_helpdesk_ops");
        $ticketVOMSREQUEST->setResponsibleUnit('Perun');



        //--------------------------------------------------------------------------------------------------------//

        //--------------------------------------------------------------------------------------------------------//
        //                         V : CREATE TICKET WITH GGUS SERVICE AND REGISTER IT IN DB

        $ggus_id = 0;
        try {
            $ggus_id = $helpdesk->createTicket($ticketVOMSREQUEST);


            $vo->setVomsTicketId($ggus_id);
            $em = $this->getDoctrine()->getManager();
            $em->persist($vo);
            $em->flush();
            //notify waiting VO
            $lavoisierUrl = $this->container->getParameter("lavoisierurl");
            $lquery = new Query($lavoisierUrl, 'VoWaitingList', 'notify');
            $lquery->execute();
            $message = 'Successful Creation of VOMS ticket for VO '.$vo->getName();
            $this->addFlash('success', $message);
        } catch (Exception $e) {
            $message = 'Unable to create VOMS ticket on "ggus_helpdesk_ops" helpdesk for VO '.$vo->getName()." with serial ".$voSerial;

            $this->addFlash('danger', $message." - ".$e->getMessage());
        }
        //--------------------------------------------------------------------------------------------------------//

        return $this->redirect($this->generateUrl("registerUpdate"));
    }


}
