<?php

namespace iahm\ContactBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{

    public function findGroups(Event $event)
    {

        $query = $this->getEntityManager()
            ->createQuery(
                '
                SELECT e
                FROM iahmContactBundle:Event e
                LEFT JOIN e.groups g
                WHERE e.id = :event_id'
            )->setParameters(
                array(
                    'event_id' => $event->getId()
                )
            );

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
