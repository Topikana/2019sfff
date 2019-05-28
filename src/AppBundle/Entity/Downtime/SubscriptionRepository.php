<?php

namespace AppBundle\Entity\Downtime;

/**
 * SubscriptionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubscriptionRepository extends \Doctrine\ORM\EntityRepository
{

    public function getSubscription(User $user, $type = null){

        $query = $this->createQueryBuilder('s')
            ->select('s');


        if($user){
            $query->join('s.user', 'u', 'WITH', 'u.id = :user_id')
                ->setParameter('user_id', $user->getId());
        }

        if($type){
            $query->join('s.communications', 'c', 'WITH', 'c.type = :type')
                ->setParameter('type', $type);
        }

        $result = $query->getQuery();

        return $result->getResult();

    }
}
