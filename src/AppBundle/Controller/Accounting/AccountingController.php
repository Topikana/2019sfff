<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 12/04/16
 * Time: 10:40
 */

namespace AppBundle\Controller\Accounting;

use Lavoisier\Query;
use Lavoisier\Hydrators\EntriesHydrator;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Services\op\Message;
use AppBundle\Model\VO\ModelVO;

/**
 * Class AccountingController
 * @package AppBundle\Controller\Accounting
 * @Route("/accounting")
 * @codeCoverageIgnore
 */
class AccountingController extends Controller
{
    /**
     * @param int $messages
     * send accounting report by mail to vo managers or sites managers
     * @return Response
     * @throws \Exception
     * @Route("/sendAccountingMail/{type}", name="sendAccountingMail", requirements={"type" = "vo|site"})
     */
    public function sendAccountingMailAction($type)
    {
        $lavoisierUrl = $this->container->getParameter("lavoisierUrl");
        $rows = null;
        $sliceRows = null;

        $lavoisierView = ($type == "vo" ? "OPSCORE_ACCPORTAL_VOS" : "OPSCORE_ACCPORTAL_SITES");

        //get vo accounting information
        try {
            $lquery = new Query($lavoisierUrl, $lavoisierView);
            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $rows = $lquery->execute()->getArrayCopy();

            //@TODO to remove
            $sliceRows = array_slice($rows, 0, 3, true);

            //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getCode() . " " . $e;
            $arrayReturn["errors"] = $error;

        }
        //@codeCoverageIgnoreEnd


        //create a new instance of Mail object
        $sent = true;
        $error = "";

        if ($sliceRows != null) {
            //loop on rows and send mail for each vo
            foreach ($sliceRows as $serial => $row) {

                try {

                    if ($type == "vo") {

                        $modelVo = new ModelVO($this->container, $serial);

                        $voAdminsMail = $modelVo->getVoAdminsMailingList();
                    }

                    //create message
                    $mail = new Message(
                        $this->container,
                        $row["title"],
                        $this->renderView(":accounting/template:template-mail.html.twig", array("type" => $type, "rows" => $row)),
                        ($type == "vo" ? $voAdminsMail : $row["email"]),
                        ' ' . $type . ' ACCOUNTING REPORT '

                    );

                    $mail->setContentType("text/html");

                    $mailer = $this->get('mailer');

                    $mailer->send($mail->getMail());


                    //@codeCoverageIgnoreStart
                } catch (\Exception $e) {
                    $sent = false;
                    $error = $e->getCode() . " - " . $e->getMessage();
                }
                //@codeCoverageIgnoreEnd

            }
        } else {
            return new Response("Accounting report email can't be sent, no report on lavoisier view...",500);
        }

        //if every mail were sent
        if ($sent) {
            //sen flash message to page
            return new Response($type." accounting report email sent with success", 200);
        } else {
            return new Response($error,500);
        }
    }


}