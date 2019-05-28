<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 29/02/16
 * Time: 15:31
 */

namespace Tests\Model\Metrics;


use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Model\Metrics\ModelMetrics;


class ModelMetricsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var $container \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var $modelMetrics \AppBundle\Model\Metrics\ModelMetrics
     */
    private $modelMetrics;

    private $voList = array('biomed', 'vlemed', 'enmr.eu', 'vo.france-grilles.fr');


    public function __construct()
    {
        parent:: __construct();

        $kernel = new \AppKernel('dev', true);
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->container = $kernel->getContainer();
        $this->modelMetrics = new ModelMetrics($this->container);
    }


    public function testConstructModel() {
        $testMdMetrics = new ModelMetrics($this->container);
        $this->assertNotNull($testMdMetrics,"Error on model metrics initialization");
    }
    /**
     * test on get nb users for a discipline, for each vo supporting this discipline
     */
    public function testgetNbUsersbyDiscipline() {
        $this->assertNotNull($this->modelMetrics->getNbUsersbyDiscipline("Agricultural Sciences"), "no result on call [getNbUsersbyDiscipline] for 'Agricultural Sciences' disciplines");
    }


    /**
     * test on get vos by discipline
     */
    public function testgetVosByDiscipline() {

        // test with parameter discipline
        $this->assertNotNull($this->modelMetrics->getVosByDiscipline("Agricultural Sciences"));
        $this->assertGreaterThanOrEqual(1, count($this->modelMetrics->getVosByDiscipline("Agricultural Sciences")), "no result on call [getVosByDiscipline] for 'Agricultural Sciences' disciplines");

        //test with no parameter
        $this->assertNotNull($this->modelMetrics->getVosByDiscipline());
        $this->assertGreaterThanOrEqual(1, count($this->modelMetrics->getVosByDiscipline()), "no result on call [getVosByDiscipline] ");

        //test with discipline type old discipline
        $this->assertNotNull($this->modelMetrics->getVosByDiscipline("Computational Chemistry", true), "no result on call [getVosByDiscipline] for old discipline 'Computational Chemistry' ");
        $this->assertGreaterThanOrEqual(1, count($this->modelMetrics->getVosByDiscipline("Computational Chemistry", true)),  "no result on call [getVosByDiscipline] for old discipline 'Computational Chemistry' ");


    }

    /**
     * test on get users number for a list of vo
     */
    public function testgetUsersNumber() {

        //test with vo list and without fixed date
        $this->assertNotNull($this->modelMetrics->getUsersNumber($this->voList),  "no result on call [getUsersNumber] with voList parameter");

        //test with vo list and fixed date
        $this->assertNotNull($this->modelMetrics->getUsersNumber($this->voList,"2015-03-01"), "no result on call [getUsersNumber] with voList and fixed date parameters");

    }

    /**
     * test on get nb users month
     */
    public function testgetNbUsersMonth() {

        //test without vo list and without month and year
        $this->assertNotNull($this->modelMetrics->getNbUsersMonth(null,null,null), "no result on call [getNbUsersMonth] ");

        //test with vo list
        $this->assertNotNull($this->modelMetrics->getNbUsersMonth(null,null,$this->voList),"no result on call [getNbUsersMonth] with volist parameter");

        //test with vo list with month and year
        $this->assertNotNull($this->modelMetrics->getNbUsersMonth("03","2015",$this->voList), "no result on call [getNbUsersMonth] with volist, month and year parameters");

    }

}
