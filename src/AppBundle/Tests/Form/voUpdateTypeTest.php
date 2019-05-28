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
//use Symfony\Component\Form\Test\TypeTestCase;
//
//class voUpdateTypeTest extends TypeTestCase
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
//    public function testSubmitValidData()
//    {
//
////        // Create forms
////        $voForm = $this->createForm('AppBundle\Form\VO\VoType', $vo);
////        // add voHeaderForm
////        $voForm->add('VoHeader', 'AppBundle\Form\VO\VoHeaderType', array('data_class' => 'AppBundle\Entity\VO\VoHeader', 'data' => $voHeader, 'mapped' => false));
////        $voForm = $voModel->addAupFields($voHeader, $voForm);
////
////        $voForm->add('VoRessources', 'AppBundle\Form\VO\VoRessourcesType', array('data_class' => 'AppBundle\Entity\VO\VoRessources', 'data' => $voRessources, 'mapped' => false));
////        $voForm->add('VoMailingList', 'AppBundle\Form\VO\VoMailingListType', array('data_class' => 'AppBundle\Entity\VO\VoMailingList', 'data' => $voMailingList, 'mapped' => false));
////        $voForm->add('VoAcknowledgmentStatements', 'AppBundle\Form\VO\VoAcknowledgmentStatementsType', array('data_class' => 'AppBundle\Entity\VO\VoAcknowledgmentStatements', 'data' => $voAS, 'mapped' => false));
//
//        $formData = array(
//            'test' => 'test',
//            'test2' => 'test2',
//        );
//
//        $form = $this->factory->create(TestedType::class);
//
//        $object = TestObject::fromArray($formData);
//
//        // submit the data to the form directly
//        $form->submit($formData);
//
//        $this->assertTrue($form->isSynchronized());
//        $this->assertEquals($object, $form->getData());
//
//        $view = $form->createView();
//        $children = $view->children;
//
//        foreach (array_keys($formData) as $key) {
//            $this->assertArrayHasKey($key, $children);
//        }
//    }
//
//}