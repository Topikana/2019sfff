<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Templating\EngineInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    private $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $content="You are not authorized to access this page !<br> You need to be authentified with a DN to access this page.<br>You can use EGI checkin to retrieve it either with EGI panel either with the x509 certificates panel.";

        return new Response($this->templating->render('@Twig/Exception/error403.html.twig',
            array("message" =>$content)),
            403);
    }
}