<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 05/10/18
 * Time: 13:27
 */

namespace AppBundle\Tests\Entity\Notepad;




use AppBundle\Entity\Comment;
use AppBundle\Entity\HistoryNotepad;
use AppBundle\Entity\Notepad;
use AppBundle\Entity\NotepadsAlarms;
use PHPUnit\Framework\TestCase;

class NotepadTest extends TestCase
{
    private $datasource;

    /**
     * @var Notepad
     */
    private $notepad;

    /**
     * @var Comment
     */
    private $comment;

    /**
     * @var NotepadsAlarms
     */
    private $notepadAlarms;

    /**
     * @var HistoryNotepad
     */
    private $historyNotepad;



  public function setUp()
  {
     $this->datasource = Populate::datasource();
     $this->notepad = Populate::createNotepad();
     $this->comment = Populate::createComment();
     $this->notepadAlarms = Populate::createNotepadAlarms();
     $this->historyNotepad = Populate::createHistoryNotepad();
  }


  public function testNotepad(){

      $this->assertEquals($this->datasource['n.subject'], $this->notepad->getSubject());
      $this->assertEquals($this->datasource['n.comment'], $this->notepad->getComment());
      $this->assertEquals($this->datasource['n.carbon_copy'], $this->notepad->getCarbonCopy());
      $this->assertEquals($this->datasource['n.currentPlace'], $this->notepad->getCurrentPlace());
      $this->assertEquals($this->datasource['n.site'], $this->notepad->getSite());
      $this->assertEquals($this->datasource['n.linktoalarm'], $this->notepad->getLinkToAlarm());
      $this->assertEquals($this->datasource['n.creation_date'], $this->notepad->getCreationDate());
      $this->assertEquals($this->datasource['n.last_update'], $this->notepad->getLastUpdate());
      $this->assertEquals($this->datasource['n.last_modifer'], $this->notepad->getLastModifer());
      $this->assertEquals($this->datasource['n.status'], $this->notepad->getStatus());
      $this->assertEquals($this->datasource['n.group_alarms'], $this->notepad->getGroupAlarms());
  }


  public function testComment(){

      $this->assertEquals($this->datasource['c.commentary'], $this->comment->getCommentary());
      $this->assertEquals($this->datasource['c.notepad_id'], $this->comment->getNotepadId());
      $this->assertEquals($this->datasource['c.author'], $this->comment->getAuthor());
      $this->assertEquals($this->datasource['c.creation_date'], $this->comment->getCreationDate());
  }

  public function testNotepadAlarms(){

      $this->assertEquals($this->datasource['na.id_notepad'], $this->notepadAlarms->getIdNotepad());
      $this->assertEquals($this->datasource['na.id_alarm'], $this->notepadAlarms->getIdAlarm());

  }

  public function testHistoryNotepad(){

      $this->assertEquals($this->datasource['hn.notepad_id'], $this->historyNotepad->getNotepadId());
      $this->assertEquals($this->datasource['hn.alarm_id'], $this->historyNotepad->getAlarmId());
      $this->assertEquals($this->datasource['hn.status'], $this->historyNotepad->getStatus());
      $this->assertEquals($this->datasource['hn.creation_date'], $this->historyNotepad->getCreationDate());
  }

}