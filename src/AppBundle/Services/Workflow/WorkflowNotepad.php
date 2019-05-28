<?php
/**
 * Created by PhpStorm.
 * User: debarban
 * Date: 11/04/2018
 * Time: 09:56
 */

namespace AppBundle\Services\Workflow;


use Lavoisier\Query;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class WorkflowNotepad implements EventSubscriberInterface
{


    public function onLeave()
    {
    }

    public function oncompleted($container)
    {
        $lavoisierUrl = $container->getParameter("lavoisierurl");
        try{
            $lquery = new Query($lavoisierUrl, 'DASHBOARD_notepads', 'notify');
            $lquery->execute();
        }catch (\Exception $e) {
            $this->addFlash(
                "danger",
                "Update - Can't notify Lavoisier view - ".$e->getMessage()
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.notepad_workflow.leave' => 'onLeave',
            'workflow.notepad_workflow.entered.updated' => 'Update',
            'workflow.notepad_workflow.completed' => 'oncompleted',
        ];
    }


    public function Update(Event $event)
    {
        $event->getSubject();
    }
}

