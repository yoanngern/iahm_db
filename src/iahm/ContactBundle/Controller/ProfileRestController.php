<?php

namespace iahm\ContactBundle\Controller;

use iahm\ContactBundle\Entity\Comment;
use iahm\ContactBundle\Entity\Donation;
use iahm\ContactBundle\Entity\Event;
use iahm\ContactBundle\Entity\Person;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use iahm\ContactBundle\Entity\Unit;
use iahm\UserBundle\Entity\User;
use iahm\ContactBundle\Form\DonationToContactType;
use iahm\ContactBundle\Form\EventType;
use iahm\ContactBundle\Form\PersonType;
use iahm\ContactBundle\Form\UnitToContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class ProfileRestController extends Controller
{


    /**
     * @return array
     * @Rest\View(serializerGroups={"Default"})
     *
     *
     */
    public function getProfileAction()
    {

        $me = $this->container->get('security.context')->getToken()->getUser();

        $user = [];
        $user["username"] = $me->getUsername();

        return $user;


        if (!is_array($contacts)) {
            throw $this->createNotFoundException();
        }
        return $contacts;

    }

}
