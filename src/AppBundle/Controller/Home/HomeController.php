<?php

namespace AppBundle\Controller\Home;

use Lavoisier\Hydrators\CSVasXMLHydrator;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\Home\Type\MailType;
use AppBundle\Entity\Home\Mail;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;


use AppBundle\Model\Broadcast\ModelBroadcast;

use Symfony\Component\BrowserKit\Cookie;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\HttpFoundation\Response;


use Lavoisier\Query;
use Lavoisier\Hydrators\EntriesHydrator;


use AppBundle\Services\op\Message;


class HomeController extends Controller
{

    /**
     * @var $container \Symfony\Component\DependencyInjection\Container
     */
    protected $container;


    /**
     * @Route(path="/{page}", defaults={"page" = null}, requirements={"page" = "opsportal/app_dev.php"}, name="home")
     */

    public function homeAction()
    {

        //get last release date, id and version

        $lavoisierUrl = $this->container->getParameter("lavoisierUrl");

        $lastRelease = null;

        try {
            // release list OPS
            $lquery = new Query($lavoisierUrl, 'OPSCORE_listReleases', 'lavoisier');
            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $releaseList = $lquery->execute();
            $orderedReleaseList = $releaseList->getArrayCopy();


            usort(
                $orderedReleaseList,
                function ($a, $b) {
                    return $b['end_date'] >= $a['end_date'];
                }
            );

            $lastRelease = array("version" => $orderedReleaseList[0]["number"],
                "date" => $orderedReleaseList[0]["end_date"],
                "id" => $orderedReleaseList[0]["id"]
            );


            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getCode() . " " . $e;
            $this->addFlash("danger", $error);

            return $this->redirect($this->generateUrl("home"));

        }
        //@codeCoverageIgnoreEnd

        return $this->render(":home:home.html.twig", array("lastRelease" => $lastRelease));

    }


    /**
     * @Route(path="/home/auth",  name="auth")
     */

    public function authAction()
    {


        //@codeCoverageIgnoreEnd

        return $this->render(":home:home.html.twig");

    }

    /**
     * @Route("/news/{limit}", defaults={"limit" = false}, name="news")
     * @param $limit
     */
    public function newsAction($limit)
    {
        if ($limit) {
            $min = 6;
            $max = 15;

        } else {
            $min = 0;
            $max = 5;
        }

        $modelBC = new ModelBroadcast($this->container);

        $tabNews = $modelBC->getLastBroadcasts(15);

        return $this->render(":home/templates:template-news.html.twig", array("tabNews" => $tabNews, "min" => $min, "max" => $max));

    }

    /**
     * @Route("/home/a/credits", name="credits")
     */
    public function creditsAction()
    {
        return $this->render(":home:credits.html.twig");

    }

    /**
     * @Route("/home/a/siteMap", name="siteMap")
     */
    public function siteMapAction()
    {
        return $this->render(":home:siteMap.html.twig");
    }

    /**
     * @Route("/home/tasksList/{releaseId}", name="tasksList")
     */
    public function tasksListAction($releaseId = null)
    {

        $tabClasses = array(
            "Bug" => "badge badge-danger",
            "bug" => "badge badge-danger",
            "enhancement" => "badge badge-success",
            "Low Priority" => "badge badge-info",
            "Medium Priority" => "badge badge-warning",
            "Task" => "badge",
            "new feature" => "badge badge-primary",
            "Feature" => "badge badge-primary",
            "Closed" => "badge badge-success",
            "Suspended" => "badge badge-danger",
            "Resolved" => "badge badge-success",
            "closed" => "badge badge-success",
            "New" => "badge badge-warning",
            "opened" => "badge badge-warning",
            "To Do" => "badge badge-warning",
            "Rejected" => "badge badge-danger",
            "Assigned" => "badge badge-primary",
            "task" => "badge badge-primary",
            "In progress" => "badge badge-primary",
            "Operations Dashboard" => "badge badge-primary",
            "Service Order Management" => "badge badge-danger",
            "v5.0" => "badge badge-secondary",
            "bootstrap4" => "badge badge-secondary"
        );


        $arrayReturn = array("tabClasses" => $tabClasses);


        $lavoisierUrl = $this->container->getParameter("lavoisierUrl");

        try {
            // release list OPS
            $lquery = new Query($lavoisierUrl, 'OPSCORE_listReleases', 'lavoisier');
            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $releaseList = $lquery->execute();
            $orderedReleaseList = $releaseList->getArrayCopy();


            usort(
                $orderedReleaseList,
                function ($a, $b) {
                    return $b['end_date'] < $a['end_date'];
                }
            );

            $arrayReturn["releaseList"] = $orderedReleaseList;


            if ($releaseId == null) {
                $releaseId = $orderedReleaseList[count($orderedReleaseList) - 1]['id'];
                $arrayReturn["releaseId"] = $releaseId;

            } else {
                $arrayReturn["releaseId"] = $releaseId;
            }

            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getCode() . " " . $e;
            $arrayReturn["errors"] = $error;

        }
        //@codeCoverageIgnoreEnd


        //release detail OPS
        try {
            $lquery = new Query($lavoisierUrl, 'OPSCORE_renderReleaseNote', 'lavoisier');
            $lquery->setMethod('POST');
            $lquery->setPostFields(array('release_id' => $releaseId));
            $hydrator = new CSVasXMLHydrator();
            $lquery->setHydrator($hydrator);
            $rows = $lquery->execute();

            $headers = $rows[0];
            unset($rows[0]);

            if (count($rows)==0)
            {

                $lquery = new Query($lavoisierUrl, 'OPSCORE_OspPortal_Releases', 'lavoisier');
                $lquery->setMethod('POST');
                $lquery->setPostFields(array('release_id' => $releaseId));
                $hydrator = new CSVasXMLHydrator();
                $lquery->setHydrator($hydrator);
                $rows = $lquery->execute();
            }

            $arrayReturn["headers"] = $headers;
            $arrayReturn["rows"] = $rows;
            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getCode() . " " . $e;
            $arrayReturn["errors"] = $error;

        }
        //@codeCoverageIgnoreEnd

        return $this->render(":home:tasksList.html.twig", $arrayReturn);

    }

    /**
     * @Route("/home/contact", name="contact")
     */
    public function contactAction(Request $request)
    {

        //create a new instance of Mail object to construct the form
        $mail = new Mail();

        //build the form with MailType
        $form = $this->createForm(MailType::class, $mail);


        //get the request
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            //test if form is valid
            if ($form->isSubmitted()) {

                try {


                    //send message to cic-information
                    $mail = new Message(
                        $this->container,
                        $form->get("subject")->getData(),
                        $this->renderView(':home/templates:template-mail.txt.twig', array(
                            'name' => $form->get('name')->getData(),
                            'message' => $form->get('body')->getData(),
                        )),
                        $form->get('email')->getData(),
                        ' CONTACT US'

                    );


                    if ($form->get("cc")->getData() != null || $form->get("cc")->getData() != "") {
                        $ccs = explode(",", $form->get("cc")->getData());
                        foreach ($ccs as $cc) {
                            $mail->setCc($cc);
                        }
                    }


                    $mailer = $this->get('mailer');

                    $mailer->send($mail->getMail());

                    //sen flash message to page
                    $this->addFlash('success', 'Your email has been sent! Thanks!');
                    //@codeCoverageIgnoreStart
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Error when try to send your email... [' . $e->getMessage() . ']');
                }
                //@codeCoverageIgnoreEnd

                return $this->redirect($this->generateUrl('contact'));

            }
        }

        return $this->render(':home:contact.html.twig', array(
            'form' => $form->createView()
        ));

    }



    /**
     * @Route("/sam/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
        throw new \Exception('Which means that this Exception will not be raised anytime soon …');
    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request)
    {
        $session = $request->getSession();
        $session->invalidate();
        $this->get('security.token_storage')->setToken(NULL);
        return $this->redirect("/");
    }


    /**
     * @codeCoverageIgnore
     * @Route("home/a/metadata", defaults={"_format"="xml"})
     */
    public function metadataAction()
    {
        $filePath = $this->getParameter('metadataFile');
        $rootNode = simplexml_load_file($filePath);

        return new Response($rootNode->asXML());
    }

    /**
     * @Route("/home/a/termsofuse", name="termsofuse")
     */
    public function TermsUseAction()
    {
        return $this->render(":home:TermsOfUse.html.twig");

    }


    /**
     * @Route("/home/loginAAI", name="loginAAI")
     */
    public function LoginAAIAction()
    {
        return $this->render(":home:AAI.html.twig");

    }




}