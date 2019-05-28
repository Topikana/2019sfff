<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 11/01/16
 * Time: 17:04
 */

namespace AppBundle\Services;

use AppBundle\Model\VO\ModelVO;
use AppBundle\Services\OpsLavoisier\Services\EntityService;

use AppBundle\Services\op\Message;
use AppBundle\Entity\VO\VoVomsServer;
use Symfony\Component\DependencyInjection\ContainerInterface;

use AppBundle\Entity\VO\VoStatus;


class Mailer implements IVoEmailGetter
{

    const SENDER_NAME = "EGI Operations Portal - VO ID Card Management";
    const SENDER_MAIL = "cic-information@cc.in2p3.fr";
    const MAIL_FROM = "cic-information@cc.in2p3.fr";
    const GRID_BODIES = "EGI Operations Representatives";
    const REGEX_EMAIL = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';


    private $eeService;

    public function setEntityService(EntityService $EntityService) {
        $this->eeService = $EntityService;
    }


    public static function notifyForRegistration($voSerial, $ticketPermalink,ContainerInterface $container, VoVomsServer $vomsServer = NULL)
    {
        $VoChangeNotifier = self::getVoChangeNotifier($voSerial, "notifyPendingRegistration", $container);

        $message = "Dear all,\n\n";
        $message .= "VO ".$VoChangeNotifier->getVoName()." has been registered and is waiting for validation.\n\n";

        if (isset($vomsServer)) {
            $message .= "This VO already has at least one VOMS server up and working:\n";
            $message .= "Hostname: " . $vomsServer->getHostname() . "\n";
            $message .= "Https port: " . $vomsServer->getHttpsPort() . "\n";
            $message .= "VOMSES port: " . $vomsServer->getVomsesPort() . "\n";
            $message .= "Member list url: " . $vomsServer->getMembersListUrl() . "\n";
            $message .= "\n\n";
        }

        $message .= "View VO ID Card at: " . $VoChangeNotifier->getVoPermalink() . " \n";
        $message .= "View GGUS ticket at: " . " \n" . $ticketPermalink . "\n\n";
        $message .= self::SENDER_NAME;


        //send message to cic-information
        $mail = new Message(
            $container,
            $VoChangeNotifier->getSubject(),
            $message,
            $VoChangeNotifier->getTargets(),
            'VO ID Card');


        /** @noinspection PhpUndefinedClassInspection */
        $container->get('mailer')->send($mail->getMail());
    }

    public static function notifyForPerun($voSerial, ContainerInterface $container){
        $modelVO = new ModelVO($container, $voSerial);
        $voName = $modelVO->getVONameBySerial($voSerial);

        $message = "Dear Operations-portal team,\n\n";
        $message .= "VO ".$voName." has been registered.\n\n";
        $message .= "This VO use perun management system.\n\n";

        $message .= "Permalink: https://" . $container->getParameter("sf3Url") . 'vo/update/voserial/' . $voSerial;

        $mail = new Message(
            $container,
            "New request",
            $message,
            $container->getParameter("webmastermail"),
            'VO ID Card Perun'
            );


        /** @noinspection PhpUndefinedClassInspection */
        $container->get('mailer')->send($mail->getMail());

    }


    public static function askForPerun($voSerial, ContainerInterface $container){
        $modelVO = new ModelVO($container, $voSerial);
        $voName = $modelVO->getVONameBySerial($voSerial);

        $message = "Dear Operations-portal team,\n\n";
        $message .= "VO ".$voName." has been modified.\n\n";
        $message .= "This VO asks to use perun management system.\n\n";

        $message .= "Permalink: https://" . $container->getParameter("sf3Url") . 'vo/update/voserial/' . $voSerial;

        $mail = new Message(
            $container,
            "Perun request",
            $message,
            $container->getParameter("webmastermail"),
            'VO ID Card Perun'
        );


        /** @noinspection PhpUndefinedClassInspection */
        $container->get('mailer')->send($mail->getMail());

    }

    public static function notifyForYearlyValidationCheck($voSerial,ContainerInterface $container, $message = null){


        $VoChangeNotifier = self::getVoChangeNotifier($voSerial, "notifyOnMsgToVoManagerAndDeputy", $container);
        if(!$message) {
            $message = "
            Dear all,

            The VO ID card information for  " . $VoChangeNotifier->getVoName() . "  has been validated by one representative of the VO .
            You can consult/update it at: " . $VoChangeNotifier->getVoPermalink() . "

            -----------
            ";
            $message .= self::SENDER_NAME;
        }


        $mail = new Message(
            $container,
            $VoChangeNotifier->getSubject(),
            $message,
            $VoChangeNotifier->getTargets(),
            'VO ID Card');


        /** @noinspection PhpUndefinedClassInspection */
        $container->get('mailer')->send($mail->getMail());
    }


    static private function getVoChangeNotifier($serial, $notifyOn,ContainerInterface $container, array $optionalDynamicTargets = array())
    {
        $modelVO = new ModelVO($container, $serial);

        $voName = $modelVO->getVONameBySerial($serial);

        $notifier = new VoChangeNotifier(
            $container->getParameter('EGI_mail_strategy')[$notifyOn],
            $voName,
            $serial,
            new mailer(new EntityService($container->getParameter("lavoisierUrl"))),
            $container,
            $optionalDynamicTargets);

        /** @noinspection PhpUndefinedClassInspection */
        //ProjectConfiguration::getActive()->loadHelpers(array('Url'));
        $notifier->setVoPermalink("https://" . $container->getParameter("sf3Url") . 'vo/update/voserial/' . $serial);


        return $notifier;

    }

    public static function notifyForStatusChange(ContainerInterface $container, $voSerial, VoStatus $oldStatus, VoStatus $newStatus, $isRejected = false, $ticketPermalink = NULL, $cause = null)
    {


        if ($isRejected) {
            $VoChangeNotifier = self::getVoChangeNotifier($voSerial, "notifyOnRegistrationRejected", $container);
        } else {
            // specific case from https://ggus.eu/ws/ticket_info.php?ticket=86988
            if (($newStatus->getId() == 6) || ($newStatus->getId() == 2)) { // deleted or production status
                $VoChangeNotifier = self::getVoChangeNotifier($voSerial, "notifyOnCriticalStatusChanged", $container);
            } else {
                $VoChangeNotifier = self::getVoChangeNotifier($voSerial, "notifyOnStatusChanged", $container);
            }
        }

        if ($isRejected) {
            $message = "Dear all,\n\n";
            $message .= "Registration of VO ".$VoChangeNotifier->getVoName(). " has been rejected.\n\n";
            $message .= $cause."\n\n";

        } else {
            $message = "Dear alls,\n\n";
            $message .= self::GRID_BODIES . " have changed VO " . $VoChangeNotifier->getVoName() . " status from " . $oldStatus->getStatus() . " to " . $newStatus->getStatus() . "\n\n";

        }


        // case of first set in production, vo registration follow-up ticket has been closed
        if (isset($ticketPermalink) && $newStatus->getId() == 2) {
            $message .= "Vo registration follow-up ticket has been closed: " . $ticketPermalink . "\n\n";
        }


        //send message to cic-information
        $mail = new Message(
            $container,
            $VoChangeNotifier->getSubject(),
            $message,
            $VoChangeNotifier->getTargets(),
            'VO ID Card');


        $mailer = $container->get('mailer');

        $mailer->send($mail->getMail());


    }


    // return an array of all manager email for a given VO serial
    public function VoManagers(ContainerInterface $container, $serial)
    {

        $return = array();

        $voModel = new ModelVO($container, $serial);

        $managers = $voModel->getManagerBySerial();

        foreach ($managers as $manager) {


            $mail = trim(strtolower($manager['VoContacts']['email']));

            if ((bool)(preg_match(self::REGEX_EMAIL, $mail))) {
                $return[$mail] = 'VO Manager';
            }
        }

        return $return;
    }


    // return an array of all ngi manager email for a given VO serial (corresponding to its scope)
    public function NgiManagers(ContainerInterface $container, $serial)
    {

        $voModel = new ModelVO($container, $serial);

        $rocScope = $voModel->getNGIManagers();

        try {
            if ($rocScope == "all") { // Vo has a scope concerning all NGIs, we contact NOC MANAGERS MALING LIST
                $return[$container->getParameter('app_mail_nocmanagers')] = 'NGI Managers';
            } else { // Vo Has a specific NGI scope, we contact only VO's NGI MANAGER
                $this->setEntityService(new EntityService($container->getParameter("lavoisierUrl")));
                $return[$this->eeService->getNGIRODEmail($rocScope)] = 'NGI Manager/' . $rocScope;
            }
        } catch (Exception $e) {
            echo "Error: ".$e->getMessage();
        }

        return $return;
    }

    //returns emails of supporting sites for a given VO as array
    public function SiteManagers(ContainerInterface $container,$serial)
    {
        $siteManagers = null;
        $return = array();
        $voName = VoHandler::getVoName($serial);

        try {


            $ee = new EntityService(sfConfig::get('project_lavoisier_v2_core'));
            $supportingSites=array_keys($ee->getSitesVo($voName));


            foreach ($supportingSites as $key => $site) { // for each site retrieve contact email

                $contactEmail = $ee->getSiteContactEmail($site);
                $contactEmail = str_replace(";", ",", $contactEmail);
                $return = array_merge($return, explode(",", $contactEmail));
                $siteManagers = array();
                foreach ($return as $email) {
                    $siteManagers[$email] = 'Site Manager/' . $site;
                }
            }
        } catch (\Exception $e) {
            echo "Error: ".$e->getMessage();
        }

        return $siteManagers;
    }


    // send a message to VO manager(s) of a given VO
    public  function contactVoManager($voSerial, $subject, $body, ContainerInterface $container)
    {

        $VoChangeNotifier = $this->getVoChangeNotifier($voSerial, "notifyOnMsgToVoManager", $container);

        $message = "Dear VO Manager(s),\n\n";
        $message .= "You have a message from " . self::GRID_BODIES . " concerning VO " . $VoChangeNotifier->getVoName() . ":\n\n";
        $message .= $body . "\n\n";
        $message .= self::SENDER_NAME;

        $mail = new Message(
            $container,
            $VoChangeNotifier->getSubject() . ':' . $subject,
            $message,
            $VoChangeNotifier->getTargets(),
            'VO ID Card'
            );



        $mailer = $container->get('mailer');
        $mailer->send($mail->getMail());
    }




}