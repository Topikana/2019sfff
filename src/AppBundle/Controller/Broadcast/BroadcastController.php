<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 09/02/16
 * Time: 14:14
 */


namespace AppBundle\Controller\Broadcast;

use AppBundle\Form\Broadcast\BroadcastMessageType;
use AppBundle\Model\Broadcast\ModelBroadcast;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Broadcast\BroadcastMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Services\JSTree\JSTree;
use AppBundle\Form\Broadcast\BroadcastSearchType;
use AppBundle\Form\Broadcast\BroadcastMailingListsType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Services\op\Message;

/**
 * Class BroadcastController
 * @package AppBundle\Controller\Broadcast
 * @Route("/broadcast")
 */
class BroadcastController extends Controller
{


    /**
     * @var $container \Symfony\Component\DependencyInjection\Container
     */
    protected $container;


    /**
     * broadcast contact all EGI Communities page
     * @Route("/send/{from}", name="broadcast")
     * @Route("/")
     */
    public function broadcastHomeAction(Request $request, $from = null)
    {

        //load targets from jstree services to display in jstree view
        $jSTreeService = $this->get('broadcastjstree');
        $targets = $jSTreeService->getBranchesHTML($this->getUser()->getDN());
        $jSTreeService->populateRecipients($this->getUser()->getDN());


        //if parameter "from" is set broadcast message must be recover to show info in form
        if ($from != null) {
            $broadcastMessage = $this->getDoctrine()->getRepository("AppBundle:Broadcast\BroadcastMessage")->findOneBy(array("id" => $from));
            if ($broadcastMessage == null) {
                $broadcastMessage = new BroadcastMessage();
                $this->addFlash("danger", "There is no broadcast message registered for the id $from...");
            }
        } else {
            //create a new instance of BroadcastMessage object to construct the form
            $broadcastMessage = new BroadcastMessage();
        }

        //get author cn to put by default in form
        $broadcastMessage->setAuthorCn($this->getUser()->getUsername());

        //build the form with BroadcastMessageType
        $form = $this->createForm(BroadcastMessageType::class, $broadcastMessage);

        //get the information of user role to show or not model
        $isGrid = $this->getUser()->isSuUser();

        if ($request->isMethod('POST')) {

            //get the sent form
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                //get targets
                $targets = explode(",", $form->get("targets")->getData());

                $jstreeParameters = array();

                //recompose targets as array
                foreach ($targets as $target) {

                    $temp_tab = explode("_", $target);

                    $jstreeParameters[$temp_tab[0]][] = $target;
                }

                //format targets elements
                $targetsLabels= $jSTreeService->renderHuman($jstreeParameters);

                $targetsEmail = $jSTreeService->renderEmail($jstreeParameters);
                $targetsIDs = $jSTreeService->renderIDs($jstreeParameters);


                //if from then we have to get data already presents in broadcast message old version
                if ($from != null) {
                    $broadcastMessage = clone $broadcastMessage;
                }

                //set targets data in broadcast message
                $broadcastMessage->setPublicationType((int)$form->get("publication_type")->getData());


                $broadcastMessage->setTargetsId(serialize($targetsIDs));

                $broadcastMessage->setTargetsMail(serialize($targetsEmail));

                $broadcastMessage->setTargetsLabel(serialize($targetsLabels));

                //save new broadcast message
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($broadcastMessage);
                    $em->flush();
                    // @codeCoverageIgnoreStart
                } catch (\Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
                // @codeCoverageIgnoreEnd

                //don't send mail if there is a "model" publication type
                if ($broadcastMessage->getPublicationType() != 3) {

                    try {

                        $mailer = $this->get('mailer');

                        //send message to first target of jstree
                        $mail = new Message(
                            $this->container,
                            $broadcastMessage->getSubject(),
                            $this->renderView(':Broadcast/templates:template-mail.txt.twig', array(
                                'name' => $broadcastMessage->getAuthorCn(),
                                'mail' => $broadcastMessage->getAuthorEmail(),
                                'targets' => $targetsEmail,
                                'id' => $broadcastMessage->getId(),
                                'message' => $broadcastMessage->getBody()
                            )),
                            array_keys($targetsEmail)[0],
                            'EGI BROADCAST'
                        );


                        //add cc if present
                        if ($broadcastMessage->getCC() != null || $broadcastMessage->getCC()  != "") {
                            $ccs = explode(",",$broadcastMessage->getCC() );
                            foreach ($ccs as $cc) {
                                $mail->addBcc($cc);
                                //send mail
                                $mailer->send($mail->getMail());
                            }
                        }

                        //if user ask for copy
                        if ($form->get("confirmation")->getData()) {
                            $mail->addBcc($broadcastMessage->getAuthorEmail());

                        }

                        //send mail
                        $mailer->send($mail->getMail());


                        //send mail to other target jstree
                        foreach($targetsEmail as $email => $label) {
                            if ($email != array_keys($targetsEmail)[0]) {
                                $mail = new Message(
                                    $this->container,
                                    $broadcastMessage->getSubject(),
                                    $this->renderView(':Broadcast/templates:template-mail.txt.twig', array(
                                        'name' => $broadcastMessage->getAuthorCn(),
                                        'mail' => $broadcastMessage->getAuthorEmail(),
                                        'targets' => $targetsEmail,
                                        'id' => $broadcastMessage->getId(),
                                        'message' => $broadcastMessage->getBody()
                                    )),
                                    $email,
                                    'EGI BROADCAST'
                                );

                                //send mail
                                $mailer->send($mail->getMail());

                            }

                        }



                        // send flash message to page
                        $this->addFlash('success', 'Your broadcast has been sent! Thanks! <br> To see your broadcast : <a href="'.$this->generateUrl("archive", array("id" => $broadcastMessage->getId())).'">your broadcast</a>');

                        // @codeCoverageIgnoreStart
                    } catch (\Exception $e) {
                        //error on send mail
                        $this->addFlash('danger', $e->getMessage());
                    }
                    // @codeCoverageIgnoreEnd

                } else {
                    // send flash message to page for model
                    $this->addFlash('success', 'Your model has been save successfully ! Thanks! ');

                }

            } else {
                // send flash message to page for error in submitting mail
                $this->addFlash('danger', 'Error when try to send your broadcast... ');

            }

            //reload page
            return $this->redirect($this->generateUrl('broadcast'));

        }

        return $this->render(':Broadcast:home.html.twig', array(
            'formBd' => $form->createView(),
            'targets' => $targets,
            "isGrid" => $isGrid));
    }

    /**
     * show predefined broadcast in modal
     * @Route("/showMyBroadcastAjax", name="showMyBroadcastAjax")
     */
    public function showMyBroadcastAjaxAction(Request $request)
    {

        $bdModel = new ModelBroadcast($this->container);


        //get list of broadcast sent by user
        $broadcasts = $bdModel->getMyBroadcast();

        //get list of registred models
        $models = $bdModel->getModels();

        //get targets
        $targets = array();
        foreach ($broadcasts as $broadcast) {
            $targets[$broadcast->getId()] = @unserialize($broadcast->getTargetsLabel());
        }

        return $this->render(":Broadcast/templates:template-modal-contentBroadcastList.html.twig",
            array("models" => $models, "broadcasts" => $broadcasts, "targets" => $targets, "displayHead" => false));


    }

    /**
     * @Route("/selectedTargetsAjax", name="selectedTargets")
     */
    public function getSelectedTargetsAjaxAction(Request $request) {
        $jSTreeService = $this->get('broadcastjstree');
        $jSTreeService->populateRecipients($this->getUser()->getDN());


        $targets = explode(",", $request->get("targets"));


        $jstreeParameters = array();


        //recompose targets as array
        foreach ($targets as $target) {

            $temp_tab = explode("_", $target);

            $jstreeParameters[$temp_tab[0]][] = $target;
        }


        //format targets elements
        $targetsLabels= $jSTreeService->renderHuman($jstreeParameters);


        $targetsLabels = serialize($targetsLabels);

//        foreach(@unserialize($targetsLabels) as $key => $val) {
//
//            $index = $this->customSearch("all",$val["item"]);
//            if ($index >= 0) {
//                var_dump($val["item"][$index]);
//
//            }
//        }
//
//        die;


        return new Response(json_encode(array("targets" => @unserialize($targetsLabels))));


    }

    /**
     * For old broadcast
     * @param null $id
     * @Route("/archive/id/{id}", name="archiveOld")
     */
    public function oldArchiveAction($id) {
        return $this->redirect($this->generateUrl("archive", array("id" => $id)));
    }

    /**
     * @Route("/archive/{id}", name="archive")
     */
    public function archiveAction(Request $request, $id = null)
    {


        $arrayResult = array();

        //headers of tab must be displayed
        $arrayResult["displayHead"] = true;

        //no specific broadcast to consult
        if ($id == null) {


            $bdModel = new ModelBroadcast($this->container);

            //get the 10 last saved broadcast
            $lastBroadcasts = $bdModel->getLastBroadcasts(10);

            //get broadcasts send by user
            $userBroadcasts = $bdModel->getMyBroadcast();



            $arrayResult["lastBroadcasts"] = $lastBroadcasts;
            $arrayResult["userBroadcasts"] = $userBroadcasts;


            //compose targets



            $targets = array();
            foreach ($lastBroadcasts as $broadcast) {
                $targets[$broadcast->getId()] = @unserialize($broadcast->getTargetsLabel());
            }


            foreach ($userBroadcasts as $broadcast) {
                $targets[$broadcast->getId()] = @unserialize($broadcast->getTargetsLabel());
            }


            //build the form with BroadcastSearchType
            $form = $this->createForm(BroadcastSearchType::class);

            $arrayResult["form"] = $form->createView();


            if ($request->isMethod('POST')) {

                //recover form data
                $form->handleRequest($request);


                //test if form is valid
                if ($form->isSubmitted()) {

                    //build search criteria from form data
                    $criteria = array();
                    $criteria["author"] = $form->get("author")->getData();
                    $criteria["subject"] = $form->get("subject")->getData();
                    $criteria["body"] = $form->get("body")->getData();
                    $criteria["email"] = $form->get("email")->getData();
                    $criteria["begin_date"] = $form->get("begin_date")->getData();
                    $criteria["end_date"] = $form->get("end_date")->getData();


                    $bdModel = new ModelBroadcast($this->container);

                    //get result of search in data base
                    $broadcasts = $bdModel->search($criteria);

                    $arrayResult["broadcasts"] = $broadcasts;

                    //get targets of broadcasts found
                    $targets = array();

                    foreach ($broadcasts as $broadcast) {
                        $targets[$broadcast->getId()] = @unserialize($broadcast->getTargetsLabel());
                    }

                }
            }

            //the user wants to see a specific broadcast
        } else {
            //recover the broadcast from his id
            $broadcastMessage = $this->getDoctrine()->getRepository("AppBundle:Broadcast\BroadcastMessage")->findBy(array("id" => $id));

            $arrayResult["broadcasts"] = $broadcastMessage;

            //get targets of broadcasts found
            $targets = array();

            foreach ($broadcastMessage as $broadcast) {

                $targets[$broadcast->getId()] = @unserialize($broadcast->getTargetsLabel());
            }

        }

        //set targets in response
        $arrayResult["targets"] = $targets;

        return $this->render(":Broadcast:archive.html.twig", $arrayResult);
    }





}