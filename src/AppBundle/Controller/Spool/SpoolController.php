<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 12/04/16
 * Time: 10:40
 */

namespace AppBundle\Controller\Spool;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class SpoolController
 * @package AppBundle\Controller\Spool
 * @Route("/spool")
 * @codeCoverageIgnore
 */
class SpoolController extends Controller
{
    /**
     * @param int $messages
     * @return Response
     * @throws \Exception
     * @Route("/sendSpool", name="sendSpool")
     */
    public function sendSpoolAction($messages = 80)
    {
        try {
            $kernel = $this->get('kernel');
            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput(array(
                'command' => 'swiftmailer:spool:send',
                '--message-limit' => $messages,
            ));
            // You can use NullOutput() if you don't need the output
            $output = new BufferedOutput();
            $application->run($input, $output);

            // return the output, don't use if you used NullOutput()
            $content = $output->fetch();

            // return new Response(""), if you used NullOutput()
            return new Response($content);
        } catch (\Swift_TransportException $se) {
            return new Response($se->getCode(). " - ".$se->getMessage());
        }

    }
}