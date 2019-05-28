<?php
/**
 * Created by PhpStorm.
 * User: frebault
 * Date: 15/12/15
 * Time: 15:30
 */

namespace AppBundle\Controller\Backend\VoUsersContact;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Helper\Backend\BackendHelper;

/**
 * Class VoUsersContactController
 * @package AppBundle\Controller\Backend\VoUsersContact
 * @Route("/a/backend/voUsersContact")
 */
class VoUsersContactController extends Controller
{

    /**
     * affichage de la liste des vo users contact enregistrés en bdd
     * @Route("/list/{letter}", name="voUsersContactList")
     * @param string $letter
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voUsersContactListAction($letter = 'A')
    {
            $alphaArray = range('A', 'Z');

            try {

                $dq = $this->getDoctrine()->getManager()->createQueryBuilder();


                $dq->select("vc.first_name, vc.last_name, vc.dn, vc.email")
                    ->from("AppBundle:VO\VoContacts", "vc");

                $query = $dq->getQuery();

                $result = $query->getResult();

                $backendHelper = new BackendHelper();
                $listUsers = $backendHelper->filter($letter, $result);

                //@codeCoverageIgnoreStart
            } catch (\Exception $e) {
                $this->addFlash("danger", "Vo Users Contact error -  " . $e->getMessage());
                return $this->render(":backend/VoUsersContact:list.html.twig", array("pageTitle" => "VO Users Contacts List"));
            }
            //@codeCoverageIgnoreEnd

            return $this->render(':backend/VoUsersContact:list.html.twig',
                array("pageTitle" => "VO Users Contacts List",
                    "listUsers" => $listUsers,
                    "alphaArray" => $alphaArray,
                    "currentLetter" => $letter));
    }

    /**
     * formulaire contact pour modifier un vo contact
     * @Route("/modify/firstName/{firstName}/lastName/{lastName}/dn/{dn}/email/{email}", name="modifyContact", requirements={"dn"=".+"})
     * @param $name
     * @param $dn
     * @param $email
     * @param $vo
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function modifyVoContactAction(Request $request, $firstName, $lastName, $dn,  $email)
    {
            $arraySearch = array("dn" => urldecode($dn));

            if ($firstName != "N.A") {
                $arraySearch["first_name"] = $firstName;
            }

            if ($lastName != "N.A") {
                $arraySearch["last_name"] = $lastName;
            }

            if ($email != "N.A") {
                $arraySearch["email"] = $email;
            }

            $voContact = $this->getDoctrine()->getRepository("AppBundle:VO\VoContacts")->findOneBy($arraySearch);

            $voContactForm = $this->createForm('AppBundle\Form\VO\VoContactsWithGridType', $voContact);


            if ($request->getMethod() == "POST") {
                $voContactForm->handleRequest($request);

                if ($voContactForm->isSubmitted()) {
                    try {

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($voContact);
                        $em->flush();

                        $this->addFlash("success", "Vo contact - " . ($voContact->getFirstName() != "N.A" && $voContact->getLastName() != "N.A" ? $voContact->getFirstName()." ".$voContact->getLastName() : urldecode($voContact->getDn())). "Modification was made successfully - last update : [" . (new \DateTime())->format('Y-m-d H:i:s') . "]");
                        return $this->redirect($this->generateUrl("modifyContact",
                            array("firstName" => $voContact->getFirstName(), "lastName" => $voContact->getLastName(), "dn" => urlencode($voContact->getDn()), "email" => $voContact->getEmail())));
                        //@codeCoverageIgnoreStart
                    } catch (\Exception $e) {
                        $this->addFlash("danger", "modify vo contact - " . ($voContact->getFirstName() != "N.A" && $voContact->getLastName() != "N.A" ? $voContact->getFirstName()." ".$voContact->getLastName() : urldecode($voContact->getDn())) . " failed... - error : " . $e->getMessage());
                        return $this->redirect($this->generateUrl("modifyContact",
                            array("firstName" => $firstName, "lastName" => $lastName, "dn" => urlencode($dn), "email" => $email)));
                    }
                    //@codeCoverageIgnoreEnd

                } else {
                    $this->addFlash("danger", "modify vo user - " . ($voContact->getFirstName() != "N.A" && $voContact->getLastName() != "N.A" ? $voContact->getFirstName()." ".$voContact->getLastName() : urldecode($voContact->getDn())) . " failed...");
                    return $this->redirect($this->generateUrl("modifyContact",
                        array("firstName" => $firstName, "lastName" => $lastName, "dn" => urlencode($dn), "email" => $email)));

                }
            }

            return $this->render(":backend/VoUsersContact:modify.html.twig", array("pageTitle" => "Modify Contact " . ($firstName != "N.A" && $lastName != "N.A" ? $firstName." ".$lastName : urldecode($dn)), "voContactForm" => $voContactForm->createView()));

    }


}