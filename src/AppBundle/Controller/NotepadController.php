<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\HistoryNotepad;
use AppBundle\Entity\Notepad;
use AppBundle\Entity\NotepadsAlarms;

use AppBundle\Entity\RodNagiosProblem;
use AppBundle\Entity\User;
use AppBundle\Services\NotepadService;

use Lavoisier\Hydrators\EntriesHydrator;
use Lavoisier\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notepad controller.
 *
 * @Route("ROD/notepad")
 */
class NotepadController extends Controller
{
    /**
     * Lists all notepad entities.
     *
     * @Route("/", name="notepad_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $notepads = $this->get('app.notepad');
        $notepads->CreateOrUpdateNotepad();
        $notepads = $this->getDoctrine()->getRepository(Notepad::class)->findAll();

        return $this->render('notepad/index.html.twig', array(
            'notepads' => $notepads->CreateOrUpdateNotepad(),
        ));
    }

    /**
     * Creates a new notepad entity.
     *
     * @Route("/new", name="notepad_new")
     * @Method({"GET", "POST"})
     */
    public function newNotepadAction(Request $request)
    {
        $notepad = new Notepad();
        $commentary = new Comment();

        $form = $this->createForm('AppBundle\Form\NotepadType', $notepad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $alarmsId = $notepad->getGroupAlarms();
            $alarmsArray = explode(',', $alarmsId);

            $user = $this->getUser()->getUsername();
            $comment = $notepad->getComment();
            $createAt = $notepad->getCreationDate();

            $notepad->setLastModifer($user);
            $notepad->setStatus(1);

            $commentary->setAuthor($user);
            $commentary->setCommentary($comment);
            $commentary->setCreationDate($createAt);

            $em = $this->getDoctrine()->getManager();
            $em->persist($notepad);
            $em->flush();

            $notepadId = $notepad->getId();

            $commentary->setNotepadId($notepadId);
            $em->persist($commentary);
            $em->flush();

            $commentaryId = $commentary->getId();

            $notepad_history = new HistoryNotepad();
            $notepad_history->setNotepadId($notepadId);
            $notepad_history->setAlarmId($alarmsArray);
            $notepad_history->setCreationDate($createAt);
            $notepad_history->setStatus(1);
            $notepad_history->setCommentId($commentaryId);

            $em->persist($notepad_history);
            $em->flush();

            if(!empty($alarmsId)) {
                foreach ($alarmsArray as $alarmId) {
                    $notepads_alarms = new NotepadsAlarms();
                    $notepads_alarms->setIdNotepad($notepadId);
                    $notepads_alarms->setIdAlarm($alarmId);

                    $alarm = $this->getDoctrine()->getManager()->getRepository(RodNagiosProblem::class)->findOneBy(array('id' => $alarmId));
                    $alarm->setOpsFlags('2');

                    $em->persist($notepads_alarms);
                    $em->persist($alarm);
                }
            }
            $em->flush();

            // Notify Lavasier view
            $lavoisierUrl = $this->container->getParameter("lavoisierurl");
            try{
                $lquery = new Query($lavoisierUrl, 'DASHBOARD_notepads', 'notify');
                $lquery->execute();
            }catch (\Exception $e) {
                $this->addFlash(
                    "danger",
                    "Update - Can't notify Lavoisier view - ".$e->getMessage()
                );
                return $this->redirectToRoute('rod');

            }

            $this->addFlash('success','Notepad create with success !');
            return $this->redirectToRoute('rod');
        }

        return $this->render('notepad/new.html.twig', array(
            'notepad' => $notepad,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a notepad entity.
     *
     * @Route("/detail/{id}", name="notepad_show")
     * @Method({"GET", "POST"})
     */
    public function getDetailNotepadAction(Request $request, Notepad $notepad)
    {

        $site = $notepad->getSite();
        $alarms = $this->getDetailsSite($site,'alarms', false);

        $commentary = new Comment();
        $editForm = $this->createForm('AppBundle\Form\AddCommentType',$commentary);
        $editForm->handleRequest($request);

//        $lavoisierUrl = $this->getParameter("lavoisierUrl");
//        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_notepads', 'lavoisier');
//        $lquery->setMethod('POST');

        $id = $notepad->getId();

        $notepadAlarm = $this->getDoctrine()->getRepository(NotepadsAlarms::class)->findBy(['idNotepad' => $id]);
        $commentaries = $this->getDoctrine()->getRepository(Comment::class)->findBy(['notepadId' => $id]);
        $history = $this->getDoctrine()->getManager()->getRepository(HistoryNotepad::class)->findBy(['notepadId' => $id]);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser()->getUsername();
            $updateAt = $commentary->getCreationDate();
            $notepadId = $notepad->getId();

            $notepad->setLastModifer($user);
            $notepad->setLastUpdate($updateAt);

            $commentary->setAuthor($user);
            $commentary->setNotepadId($notepadId);

            $createAt = $commentary->getCreationDate();

            $em->persist($notepad);
            $em->persist($commentary);
            $em->flush();

            $commentaryHistoryInfo = $this->getDoctrine()->getManager()->getRepository(Comment::class)->findOneBy(
                array('creation_date' => $createAt),
                array('id' => 'DESC')
            );

            $commentId = $commentaryHistoryInfo->getId();

            $notepad_history = new HistoryNotepad();
            $notepad_history->setNotepadId($notepadId);
            $notepad_history->setCommentId($commentId);
            $notepad_history->setCreationDate($createAt);
            $notepad_history->setStatus(4);

            $em->persist($notepad_history);
            $em->flush();

            $lavoisierUrl = $this->getParameter("lavoisierUrl");
            $lquery = new Query($lavoisierUrl, 'DASHBOARD_notepads', 'notify');
            $lquery->execute();



            return $this->redirectToRoute('rod');

        }

        return $this->render(':notepad:detail_notepad_modal.html.twig', array(
            'notepad' => $notepad,
            'edit_form' => $editForm->createView(),
            'alarmNotepad' => $notepadAlarm,
            'commentaries' => $commentaries,
            'alarms' => $alarms,
            'history' => $history
        ));
    }


    /**
     * Displays a form to edit an existing notepad entity.
     *
     * @Route("/{id}/edit", name="notepad_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Notepad $notepad)
    {
        $editForm = $this->createForm('AppBundle\Form\NotepadType', $notepad);
        $editForm->handleRequest($request);

        $lavoisierUrl = $this->getParameter("lavoisierUrl");
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_notepads', 'lavoisier');
        $lquery->setMethod('POST');

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $user = $this->getUser()->getUsername();
            $notepad->setLastModifer($user);
            $this->getDoctrine()->getManager()->flush();

            $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_notepads', 'notify');
            $result = $lquery->execute();

            return $this->redirectToRoute('notepad_edit', array('id' => $notepad->getId()));
        }

        return $this->render('notepad/edit.html.twig', array(
            'notepad' => $notepad,
            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a notepad entity.
     *
     * @Route("/remove_notepad/{id}", name="notepad_delete")
     *
     */
    public function deleteNotepadAction(Request $request, Notepad $notepad)
    {

        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();

        $notepadAlarms = $em->getRepository(NotepadsAlarms::class)->findby(['idNotepad' => $id]);
        $comments = $em->getRepository(Comment::class)->findBy(['notepadId' => $id]);
        $historys = $em->getRepository(HistoryNotepad::class)->findBy(['notepadId' => $id]);


        if(!empty($notepadAlarms)){
            foreach ($notepadAlarms as $notepadAlarm){

                $alarmId = $notepadAlarm->getIdAlarm();
                $em->remove($notepadAlarm);


                    $ragiosAlarm = $this->getDoctrine()->getRepository(RodNagiosProblem::class)->find($alarmId);
                    if(!empty($ragiosAlarm)){
                        $ragiosAlarm->setOpsFlags(0);
                        $em->persist($ragiosAlarm);
                        $em->flush();
                    }

            }
        }
        if(!empty($comments)){
            foreach ($comments as $comment){
                $em->remove($comment);
                $em->flush();
            }
        }
        if(!empty($historys)){
            foreach ($historys as $history){
                $em->remove($history);
                $em->flush();
            }
        }

        $em->remove($notepad);
        $em->flush();

        $lavoisierUrl = $this->getParameter("lavoisierUrl");
        $lquery = new Query($lavoisierUrl, 'DASHBOARD_notepads', 'notify');
        $lquery->execute();

        return $this->redirectToRoute('rod');
    }

    /**
     * @param Request $request
     * @Route ("/delete_alarm_test/{id}", name="alarm_test_delete")
     */
    public function deleteAlarmTestAction(Request $request){

        $alarmId = $request->get('id');
        $em = $this->getDoctrine()->getManager();

        $alarmTest = $this->getDoctrine()->getRepository(RodNagiosProblem::class)->findOneBy(['id' => $alarmId]);

        $em->remove($alarmTest);
        $em->flush();

//        $lavoisierUrl = $this->getParameter('lavoisierUrl');
//        $lquery = new \Lavoisier\Query($lavoisierUrl, 'DASHBOARD_alarms', 'notify');
//        $lquery->execute();
//        sleep(4);



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
        $profilesList = $user->getSetting()->getProfileAlarm();
        $statusList = $user->getSetting()->getAlarmStatus();
        $FilterAssigned = (isset($assigned))? $assigned : !in_array(4,explode(',',$user->getSetting()->getAlarmStatus()));
        $data_details=array();

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
            if (isset($result) && $result!=null)
                $data_details = $result->getArrayCopy();

        }catch (\Exception $e) {
            $this->addFlash(
                "danger",
                "ROD Dashboard- Can't get details of Site  .. Lavoisier call failed - ".$e->getMessage()
            );
        }


        return $data_details;
    }



}
