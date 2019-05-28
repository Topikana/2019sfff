<?php
/**
 * Created by PhpStorm.
 * User: debarban
 * Date: 23/04/2018
 * Time: 09:51
 */

namespace AppBundle\Services;


use AppBundle\Entity\Notepad;
use AppBundle\Entity\NotepadsAlarms;
use Doctrine\ORM\EntityManager;

class NotepadService
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

    }

    public function showAll(){
        return $this->em->getRepository(Notepad::class)->findAll();
    }

    public function CreateOrUpdateNotepad(Notepad $notepad, $alarm_id = null){
        $this->em->persist($notepad);

        if($alarm_id != null){
            $notepadAlarm = new NotepadsAlarms();
            $notepadAlarm->setIdAlarm($alarm_id);
            $notepadAlarm->setIdNotepad($notepad->getId());
            $this->em->persist($notepadAlarm);

        }
        $this->em->flush();
    }
}