<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 05/10/18
 * Time: 15:34
 */

namespace AppBundle\Tests\Entity\Notepad;


use AppBundle\Entity\Comment;
use AppBundle\Entity\HistoryNotepad;
use AppBundle\Entity\Notepad;
use AppBundle\Entity\NotepadsAlarms;
use PHPUnit\Framework\TestCase;


class Populate extends TestCase
{

    public static function createNotepad(){
        $data = Populate::datasource();

        $notepad = new Notepad();
        $notepad->setSubject($data['n.subject']);
        $notepad->setComment($data['n.comment']);
        $notepad->setCarbonCopy($data['n.carbon_copy']);
        $notepad->setCurrentPlace($data['n.currentPlace']);
        $notepad->setSite($data['n.site']);
        $notepad->setLinkToAlarm($data['n.linktoalarm']);
        $notepad->setCreationDate($data['n.creation_date']);
        $notepad->setLastUpdate($data['n.last_update']);
        $notepad->setLastModifer($data['n.last_modifer']);
        $notepad->setStatus($data['n.status']);
        $notepad->setGroupAlarms($data['n.group_alarms']);


        return $notepad;

    }

    public static function createComment(){
        $data = Populate::datasource();

        $comment = new Comment();
        $comment->setCommentary($data['c.commentary']);
        $comment->setNotepadId($data['c.notepad_id']);
        $comment->setAuthor($data['c.author']);
        $comment->setCreationDate($data['c.creation_date']);

        return $comment;
    }

    public static function createNotepadAlarms(){
        $data = Populate::datasource();

        $notepadAlarms = new NotepadsAlarms();
        $notepadAlarms->setIdAlarm($data['na.id_alarm']);
        $notepadAlarms->setIdNotepad($data['na.id_notepad']);

        return $notepadAlarms;
    }

    public static function createHistoryNotepad(){
        $data = Populate::datasource();

        $historyNotepad = new HistoryNotepad();
        $historyNotepad->setNotepadId($data['hn.notepad_id']);
        $historyNotepad->setAlarmId($data['hn.alarm_id']);
        $historyNotepad->setStatus($data['hn.status']);
        $historyNotepad->setCreationDate($data['hn.creation_date']);

        return $historyNotepad;
    }

    public static function datasource(){
        $data = [];


//        notepad table
        $data["n.subject"] = "[Rod Dashboard] Issue detected : IN2P3-IRES";
        $data["n.comment"] = "Dear all, Issues have been detected at IN2P3-IRES. ----
        ------- *ALARM #0 ----------- id : bd234872-3728-11e8-b58b-1418776844f0 created_at
         : 2018-09-06 14:11:17.0 test_name : eu.egi.cloud.OCCI-AppDB-Sync host_name : sbgcloud.in2p3.fr
          service : eu.egi.cloud.vm-management.occi status :";
        $data["n.carbon_copy"] = 1;
        $data["n.currentPlace"] = "create";
        $data["n.site"] = "IN2P3-IRES";
        $data["n.linktoalarm"] = 1;
        $data["n.creation_date"] = new \DateTime('now');
        $data["n.last_update"] = new \DateTime('now');
        $data["n.last_modifer"] = "Chris Robert";
        $data["n.status"] = 1;
        $data["n.group_alarms"] = ['bd234872-3728-11e8-b58b-1418776844f0','e25544d9-3728-11e8-b58b-1418776844f0'];

//        comment table
        $data['c.commentary'] = "Blabla bla bla Blabla bla bla Blabla bla bla Blabla bla bla 
        Blabla bla bla Blabla bla bla ";
        $data['c.notepad_id'] = 265;
        $data['c.author'] = "Robert Plant";
        $data['c.creation_date'] = new \DateTime('now');

//        notepad_alarms table
        $data['na.id_alarm'] = ['bd234872-3728-11e8-b58b-1418776844f0'];
        $data['na.id_notepad'] = ['595'];


//        history_notepad
        $data['hn.notepad_id'] = ['565'];
        $data['hn.alarm_id'] = ['bd234872-3728-11e8-b58b-1418776844f0', 'e25544d9-3728-11e8-b58b-1418776844f0'];
        $data['hn.status'] = ['4'];
        $data['hn.creation_date'] = new \DateTime('now');


    }
}