<?php

namespace AppBundle\Entity\User;

use Doctrine\DBAL\Event\SchemaEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\User;

class UserProvider implements UserProviderInterface
{

    private $session;
    private $lavoisierUrl;
    private $container;

    public function supportsClass($class)
    {
        return $class === 'AppBundle\Entity\User';
    }


    public function __construct(Session $session, $lavoisierUrl, ContainerInterface $container)
    {
        $this->session = $session;
        $this->lavoisierUrl = $lavoisierUrl;
        $this->container = $container;
    }

  


    public function loadUserByUsername($dn)
    {
        if(!$this->session instanceof Session){
            $session = new Session();
        }else{
            $session = $this->session;
        }



            // Get Session
            $user = $session->get("user");
            if($user == null && $session->get("_security_secured_area") != null){
                $user = unserialize($session->get("_security_secured_area"))->getUser();

            }
            // If User in Session then load it in symfony user
            if(isset($user) && $user != null ) {
                $user =  new User($user->getDn(), null, null, $user->getRoles(),$user->getOpRoles());
            } else {
                $user =  new User($dn, null, null, array('ROLE_USER'));
            }

           // $user->setOpRoles($this->lavoisierUrl);



        return $user;

    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        return $this->loadUserByUsername($user->getDn());
    }



}
