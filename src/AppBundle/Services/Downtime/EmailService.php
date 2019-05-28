<?php


namespace AppBundle\Services\Downtime;

use AppBundle\Entity\Downtime\Communication;
use AppBundle\Entity\Downtime\EmailStatus;
use AppBundle\Entity\Downtime\Subscription;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailService
{
    protected $em;
    private $templating;
    private $mailer;
    private $container;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->templating = $container->get('templating');
        $this->mailer = $container->get('swiftmailer.mailer');
        $this->container = $container;
    }


    public function sendEmailNotification($downtime, $type, $email, EmailStatus $es, $typeEmail){


        if($this->container->get('kernel')->getEnvironment() == 'dev'){
            $email = 'thibaut.salanon@cc.in2p3.fr';
        }

        if($typeEmail == Communication::TYPE_EMAIL_HTML && filter_var($email, FILTER_VALIDATE_EMAIL)){
            $message = \Swift_Message::newInstance()
                ->setSubject('[DOWNTIME] '.$type.' : '.$downtime["NGI"].' - '.$downtime["SITENAME"])
                ->setFrom('cic-information@in2p3.fr')
                ->setTo($email)
                ->setBody(
                    $this->templating->render(
                    // app/Resources/views/Emails/registration.html.twig
                        ':Downtime/email:notification.html.twig', array(
                            'type' => $type,
                            'site' => $downtime["SITENAME"],
                            'ngi' => $downtime["NGI"],
                            'classification' => $downtime["CLASSIFICATION"],
                            'start_date' => $downtime["START_DATE"],
                            'end_date' => $downtime["END_DATE"],
                            'hosts' => $downtime["ENDPOINTS"]["hosts"],
                            'services' => $downtime["ENDPOINTS"]["vos"],
                            'link' => $downtime["GOCDB_PORTAL_URL"],
                            'severity' => $downtime["SEVERITY"],
                            'description' => $downtime["DESCRIPTION"],
                            'es' => $es
                        )
                    ),
                    'text/html'
                )
            ;
            $this->mailer->send($message);
        }else if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $message = \Swift_Message::newInstance()
                ->setSubject('[DOWNTIME] '.$type.' : '.$downtime["NGI"].' - '.$downtime["SITENAME"])
                ->setFrom('cic-information@in2p3.fr')
                ->setTo($email)
                ->setBody(
                    $this->templating->render(
                    // app/Resources/views/Emails/registration.html.twig
                        ':Downtime/email:notification.txt.twig', array(
                            'type' => $type,
                            'site' => $downtime["SITENAME"],
                            'ngi' => $downtime["NGI"],
                            'classification' => $downtime["CLASSIFICATION"],
                            'start_date' => $downtime["START_DATE"],
                            'end_date' => $downtime["END_DATE"],
                            'hosts' => $downtime["ENDPOINTS"]["hosts"],
                            'services' => $downtime["ENDPOINTS"]["vos"],
                            'link' => $downtime["GOCDB_PORTAL_URL"],
                            'severity' => $downtime["SEVERITY"],
                            'description' => $downtime["DESCRIPTION"],
                            'es' => $es
                        )
                    ),
                    'text'
                )
            ;
            $this->mailer->send($message);
        }





    }

}
