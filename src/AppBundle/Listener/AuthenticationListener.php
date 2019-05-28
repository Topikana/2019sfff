<?php

/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 16/02/16
 * Time: 11:04
 */
namespace AppBundle\Listener;


use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class AuthenticationListener
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    function __construct($container) {
        $this->container = $container;
    }


    /**
     * onAuthenticationFailure
     *
     * @author 	Joe Sexton <joe@webtipblog.com>
     * @param 	AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(GetResponseForExceptionEvent $event )
    {


        $templating = $this->container->get('templating');
        $exception = $event->getException();

        if ($exception instanceof InsufficientAuthenticationException) {


            $response = new Response($templating->render('TwigBundle:Exception:errorAuthenticationFailed.html.twig', array(
                'pageTitle' => 'Access Denied',
                'message' => "This page has a restricted access.  You are not allowed to access this page without the correct credentials.\n"
            )));


            // Send the modified response object to the event
            $event->setResponse($response);


        }
    }


}