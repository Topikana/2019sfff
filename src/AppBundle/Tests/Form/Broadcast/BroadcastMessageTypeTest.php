<?php
///**
// * Created by PhpStorm.
// * User: frebault
// * Date: 29/03/16
// * Time: 14:28
// */
//
//namespace AppBundle\Tests\Form;
//
//
//use AppBundle\Entity\Broadcast\BroadcastMessage;
//use AppBundle\Form\Broadcast\BroadcastMessageType;
//use Symfony\Component\Form\Test\TypeTestCase;
//
//use Symfony\Component\Form\PreloadedExtension;
//
//class BroadcastMessageTypeTest extends TypeTestCase
//{
//    private $entityManager;
//
//    protected function setUp()
//    {
//        // mock any dependencies
//        $this->entityManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
//
//        parent::setUp();
//    }
//
//    protected function getExtensions()
//    {
//        // create a type instance with the mocked dependencies
//        $type = new BroadcastMessageType($this->entityManager);
//
//        return array(
//            // register the type instances with the PreloadedExtension
//            new PreloadedExtension(array($type), array()),
//        );
//    }
//
////    public function testSubmitValidData()
////    {
////
//////        // Create forms
//////        $voForm = $this->createForm('AppBundle\Form\VO\VoType', $vo);
//////        // add voHeaderForm
//////        $voForm->add('VoHeader', 'AppBundle\Form\VO\VoHeaderType', array('data_class' => 'AppBundle\Entity\VO\VoHeader', 'data' => $voHeader, 'mapped' => false));
//////        $voForm = $voModel->addAupFields($voHeader, $voForm);
//////
//////        $voForm->add('VoRessources', 'AppBundle\Form\VO\VoRessourcesType', array('data_class' => 'AppBundle\Entity\VO\VoRessources', 'data' => $voRessources, 'mapped' => false));
//////        $voForm->add('VoMailingList', 'AppBundle\Form\VO\VoMailingListType', array('data_class' => 'AppBundle\Entity\VO\VoMailingList', 'data' => $voMailingList, 'mapped' => false));
//////        $voForm->add('VoAcknowledgmentStatements', 'AppBundle\Form\VO\VoAcknowledgmentStatementsType', array('data_class' => 'AppBundle\Entity\VO\VoAcknowledgmentStatements', 'data' => $voAS, 'mapped' => false));
////
////        $formData = array(
////            'publication_type' => 0,
////            'confirmation' => 1,
////            'author_cn' => 'Laure Souai',
////            'author_email' => 'laure.souai@cc.in2p3.fr',
////            'cc' => 'cic-information@in2p3.fr',
////            'subject' => 'unit test on broadcast message form',
////            'body' => 'making unit test on broadcast message form to validate submission...',
////            'targets' => 'gm_1,gm_2,gm_3,gm_5,gm_4,gm_13,gm_7'
////        );
////
////        $form = $this->factory->create(BroadcastMessageType::class);
////
////        $object = BroadcastMessage::fromArray($formData);
////
////        // submit the data to the form directly
////        $form->submit($formData);
////
////        $this->assertTrue($form->isSynchronized());
////        $this->assertEquals($object, $form->getData());
////
////        $view = $form->createView();
////        $children = $view->children;
////
////        foreach (array_keys($formData) as $key) {
////            $this->assertArrayHasKey($key, $children);
////        }
////    }
//
//}