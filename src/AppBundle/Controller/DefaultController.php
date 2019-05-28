<?php
/**
 * Created by PhpStorm.
 * User: frebault
 * Date: 15/12/15
 * Time: 15:30
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class DefaultController extends Controller
{


    /**
     * @Route("/user/userInfo", name="userInfo")
     */
    public function userInfoAction(Request $request)
    {
        $user = $this->getUser();

        if ($request->query->get('Id'))
            $user=$this->getDoctrine()->getRepository('AppBundle:User')->findOneById($request->query->get('Id'));


        $isSU = $user->isSuUser();



        return $this->render('default/userInfo.html.twig', array(
            'user' => $user,
            'dn' => $user->getDn(),
            'isSU' => $isSU,
            'opRoles' => $user->getOpRoles()));
    }

    /**
     * @Route("/user/getRoles", name="getRoles")
     */
    public function GetRolesAction()
    {
        $user = $this->getUser();
        $isSU = $user->isSuUser();

        return $this->render('default/userRoles.html.twig', array(
            'user' => $user,
            'dn' => $user->getDn(),
            'isSU' => $isSU,
            'opRoles' => $user->getOpRoles()));
    }

    /**
     * @Route("/user/refreshSession", name="refreshSession")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function RefreshSessionAction(Request $request)
    {
        $session = $request->getSession();
        $session->invalidate();
        $this->get('security.token_storage')->setToken(NULL);


        return $this->redirect("userInfo");
    }


    /**
     * @Route("/saml/aai-login", name="aai-login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function AaiLoginAction(Request $request)
    {
        $this->get('security.token_storage')->setToken(NULL);
        $session = $request->getSession();
        $session->invalidate();
     
        return $this->redirect($this->generateUrl('lightsaml_sp.login'));
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loginAction(Request $request)
    {


        return $this->render(
            'default/login.html.twig'

        );

    }
}