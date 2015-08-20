<?php

namespace iahm\ContactBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
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
use iahm\ContactBundle\Form\ContactEventsType;
use iahm\ContactBundle\Form\ContactGroupsType;
use iahm\ContactBundle\Form\ContactDonationsType;
use iahm\ContactBundle\Form\DonationToContactType;
use iahm\ContactBundle\Form\EventType;
use iahm\ContactBundle\Form\PersonType;
use iahm\ContactBundle\Entity\PersonRepository;
use iahm\ContactBundle\Form\UnitToContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class ContactRestController extends Controller
{

    /**
     * @param Person $contact
     * @return object
     * @Rest\View(serializerGroups={"Default","Details","ContactDetails"})
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Get Contact's details",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getContactAction(Person $contact)
    {
        return $contact;
    }

    /**
     * @return array
     * @Rest\View(serializerGroups={"Default"})
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Return a collection of Contacts",
     *  tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getContactsAction()
    {

        $contacts = $this->getDoctrine()->getRepository('iahmContactBundle:Person')->findAll();

        if (!is_array($contacts)) {
            throw $this->createNotFoundException();
        }
        return $contacts;

    }

    /**
     * @param Person $contact
     * @return array
     * @Rest\View(serializerGroups={"Default","ContactEvents","Details"})
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Return a collection of Contact's Events",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getContactEventsAction(Person $contact)
    {

        $contact = $this->getDoctrine()->getRepository('iahmContactBundle:Person')->findEvents($contact);

        if (!is_object($contact)) {
            throw $this->createNotFoundException();
        }
        return $contact;

    }


    /**
     * @param Person $contact
     * @return array
     * @Rest\View(serializerGroups={"Default","ContactGroups","Details"})
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Return a collection of Contact's Groups",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getContactGroupsAction(Person $contact)
    {

        $contact = $this->getDoctrine()->getRepository('iahmContactBundle:Person')->findGroups($contact);

        if (!is_object($contact)) {
            throw $this->createNotFoundException();
        }
        return $contact;

    }

    /**
     * @param Person $contact
     * @return array
     * @Rest\View(serializerGroups={"Default","ContactMembers","Details"})
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Return a collection of Groups where a Contact is member",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getContactMembersAction(Person $contact)
    {

        $contact = $this->getDoctrine()->getRepository('iahmContactBundle:Person')->findMembers($contact);

        if (!is_object($contact)) {
            throw $this->createNotFoundException();
        }
        return $contact;

    }


    /**
     * @param Person $contact
     * @return array
     * @Rest\View(serializerGroups={"Default","ContactLeaders","Details"})
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Return a collection of Groups where a Contact is leader",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getContactLeadersAction(Person $contact)
    {

        $contact = $this->getDoctrine()->getRepository('iahmContactBundle:Person')->findLeaders($contact);

        if (!is_object($contact)) {
            throw $this->createNotFoundException();
        }
        return $contact;

    }

    /**
     * @param Person $contact
     * @return array
     * @Rest\View(serializerGroups={"Default","ContactDonations","Details"})
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Return a collection of Contact's Donations",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getContactDonationsAction(Person $contact)
    {

        $contact = $this->getDoctrine()->getRepository('iahmContactBundle:Person')->findDonations($contact);

        if (!is_object($contact)) {
            throw $this->createNotFoundException();
        }
        return $contact;

    }


    /**
     * @param Person $contact
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Update a Contact",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\PersonType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function putContactAction(Person $contact, Request $request)
    {
        return $this->processForm($contact, $request);
    }


    /**
     * @param Person $contact
     * @return Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Remove a Contact",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function deleteContactAction(Person $contact)
    {

        $statusCode = ($contact->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();

            $client = $this->get('solarium.client');

            $update = $client->createUpdate();

            $update->addDeleteById("contact_" . $contact->getId());

            $update->addCommit();
            $update->addOptimize();
            $result = $client->update($update);

            if ($result->getStatus() != 0) {
                $statusCode = 500;
            } else {
                $em->remove($contact);
                $em->flush();
            }
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;

    }


    /**
     * @param Person $person
     * @param Request $request
     * @return View|Response
     */
    private function processForm(Person $person, Request $request)
    {
        $statusCode = ($person->getId() == null) ? 201 : 204;

        if ($statusCode === 201) {
            $form = $this->createForm(new PersonType(), $person, array('method' => 'POST'));
        } else {
            $form = $this->createForm(new PersonType(), $person, array('method' => 'PUT'));
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($person->getCommentTxt() != "") {

                if ($person->getComment() != null) {
                    $comment = $person->getComment();
                } else {
                    $comment = new Comment();
                }

                $comment->setText($person->getCommentTxt());
                $person->setComment($comment);

            }

            if (($person->getCommentTxt() === "" || $person->getCommentTxt() == null) && $person->getComment() != null) {

                $person->getComment()->setText("");
            }


            $em->persist($person);
            $em->flush();

            $result = $this->addToSolr($person);

            if ($result->getStatus() != 0) {
                $statusCode = 500;
            }

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_get_contact', array('contact_id' => $person->getId(),
                        true // absolute
                    ))
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }




    /**
     * @param Person $contact
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Create a new Contact's Donation",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\DonationToContactType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function postContactsDonationsAction(Person $contact, Request $request)
    {
        $statusCode = ($contact->getId() == null) ? 404 : 204;

        $donation = new Donation();

        if ($statusCode === 204) {
            $form = $this->createForm(new DonationToContactType(), $donation, array('method' => 'POST'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                if ($donation->getCommentTxt() != "") {

                    if ($donation->getComment() != null) {
                        $comment = $donation->getComment();
                    } else {
                        $comment = new Comment();
                    }

                    $comment->setText($donation->getCommentTxt());
                    $donation->setComment($comment);

                }

                if (($donation->getCommentTxt() === "" || $donation->getCommentTxt() == null) && $donation->getComment() != null) {

                    $donation->getComment()->setText("");
                }

                $donation->setPerson($contact);

                $em->persist($donation);
                $em->flush();

                $statusCode = 201;

                $result = $this->addToSolr($contact);

                if ($result->getStatus() != 0) {
                    $statusCode = 500;
                }

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_get_donation', array('donation' => $donation->getId(),
                            true // absolute
                        ))
                    );
                }

                return $response;
            } else {
                return View::create($form, 400);
            }
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }


    /**
     * @param Person $contact
     * @param Request $request
     * @return Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Update list of donations",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\ContactDonationsType",
     *  tags={
     *          "dev"
     *      }
     * )
     */
    public function putContactsDonationsAction(Person $contact, Request $request)
    {
        $statusCode = ($contact->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {


            $originalDonations = new ArrayCollection();
            foreach ($contact->getDonations() as $donation) {
                $originalDonations->add($donation);
            }


            $form = $this->createForm(new ContactDonationsType(), $contact, array('method' => 'PUT'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                foreach ($originalDonations as $donation) {

                    if (!$contact->getDonations()->contains($donation)) {
                        $donation->removePerson($contact);
                    }
                }

                foreach ($contact->getDonations() as $donation) {

                    if (!$donation->getPersons()->contains($contact)) {
                        $donation->addPerson($contact);
                    }

                }

                $em->persist($contact);
                $em->flush();

                $result = $this->addToSolr($contact);

                if ($result->getStatus() != 0) {
                    $statusCode = 500;
                }


                $response = new Response();
                $response->setStatusCode($statusCode);

                return $response;
            }
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }


    /**
     * @param Person $contact
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Create a new Group where a Contact is member",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\UnitToContactType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function postContactsMembersAction(Person $contact, Request $request)
    {
        $statusCode = ($contact->getId() == null) ? 404 : 204;

        $group = new Unit();

        if ($statusCode === 204) {
            $form = $this->createForm(new UnitToContactType(), $group, array('method' => 'POST'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $group->addMember($contact);

                $em->persist($group);
                $em->flush();

                $statusCode = 201;

                $result = $this->addToSolr($contact);

                if ($result->getStatus() != 0) {
                    $statusCode = 500;
                }

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_get_group', array('group_id' => $group->getId(),
                            true // absolute
                        ))
                    );
                }

                return $response;
            } else {
                return View::create($form, 400);
            }
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }


    /**
     * @param Person $contact
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Create a new Group where a Contact is leader",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\UnitToContactType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function postContactsLeadersAction(Person $contact, Request $request)
    {
        $statusCode = ($contact->getId() == null) ? 404 : 204;

        $group = new Unit();

        if ($statusCode === 204) {
            $form = $this->createForm(new UnitToContactType(), $group, array('method' => 'POST'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $group->addLeader($contact);

                $em->persist($group);
                $em->flush();

                $statusCode = 201;

                $result = $this->addToSolr($contact);

                if ($result->getStatus() != 0) {
                    $statusCode = 500;
                }

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_get_group', array('group_id' => $group->getId(),
                            true // absolute
                        ))
                    );
                }

                return $response;
            } else {
                return View::create($form, 400);
            }
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }



    /**
     * @param Person $contact
     * @param Request $request
     * @return Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Update list of events",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\ContactEventsType",
     *  tags={
     *          "dev"
     *      }
     * )
     */
    public function putContactsEventsAction(Person $contact, Request $request)
    {
        $statusCode = ($contact->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {


            $originalEvents = new ArrayCollection();
            foreach ($contact->getEvents() as $event) {
                $originalEvents->add($event);
            }


            $form = $this->createForm(new ContactEventsType(), $contact, array('method' => 'PUT'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                foreach ($originalEvents as $event) {

                    if (!$contact->getEvents()->contains($event)) {
                        $event->removePerson($contact);
                    }
                }

                foreach ($contact->getEvents() as $event) {

                    if (!$event->getPersons()->contains($contact)) {
                        $event->addPerson($contact);
                    }

                }

                $em->persist($contact);
                $em->flush();

                $result = $this->addToSolr($contact);

                if ($result->getStatus() != 0) {
                    $statusCode = 500;
                }


                $response = new Response();
                $response->setStatusCode($statusCode);

                return $response;
            }
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }


    /**
     * @param Person $contact
     * @param Event $event
     * @param Request $request
     * @return Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Add an existing Contact to an existing Event",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      },
     *      {
     *          "name"="event",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Event"
     *      }
     *  },
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function putContactsEventAction(Person $contact, Event $event, Request $request)
    {
        $statusCode = ($contact->getId() == null || $event->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();

            $in_array = false;

            foreach ($contact->getEvents() as $existingEvent) {
                if ($existingEvent->getId() == $event->getId()) {
                    $in_array = true;
                }
            }

            if (!$in_array) {
                $contact->addEvent($event);

                $em->persist($contact);
                $em->flush();
            }

            $statusCode = 204;

            $result = $this->addToSolr($contact);

            if ($result->getStatus() != 0) {
                $statusCode = 500;
            }

            $response = new Response();
            $response->setStatusCode($statusCode);

            return $response;
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }


    /**
     * @param Person $contact
     * @param Request $request
     * @return Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Update list of groups",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\ContactGroupsType",
     *  tags={
     *          "dev"
     *      }
     * )
     */
    public function putContactsGroupsAction(Person $contact, Request $request)
    {
        $statusCode = ($contact->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {

            $originalLeaderOfs = new ArrayCollection();
            foreach ($contact->getLeaderOfs() as $group) {
                $originalLeaderOfs->add($group);
            }

            $originalMemberOfs = new ArrayCollection();
            foreach ($contact->getMemberOfs() as $group) {
                $originalMemberOfs->add($group);
            }


            $form = $this->createForm(new ContactGroupsType(), $contact, array('method' => 'PUT'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                foreach ($originalLeaderOfs as $group) {

                    if (!$contact->getLeaderOfs()->contains($group)) {
                        $group->removeLeader($contact);
                    }
                }

                foreach ($contact->getLeaderOfs() as $group) {

                    if (!$group->getLeaders()->contains($contact)) {
                        $group->addLeader($contact);
                    }

                }

                foreach ($originalMemberOfs as $group) {

                    if (!$contact->getMemberOfs()->contains($group)) {
                        $group->removeMember($contact);
                    }
                }

                foreach ($contact->getMemberOfs() as $group) {

                    if (!$group->getMembers()->contains($contact)) {
                        $group->addMember($contact);
                    }

                }

                $em->persist($contact);
                $em->flush();

                $result = $this->addToSolr($contact);

                if ($result->getStatus() != 0) {
                    $statusCode = 500;
                }


                $response = new Response();
                $response->setStatusCode($statusCode);

                return $response;
            }
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }


    /**
     * @param Person $contact
     * @param Unit $group
     * @param Request $request
     * @return Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Add an existing Contact to an existing Group where he is leader",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      },
     *      {
     *          "name"="group",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Group"
     *      }
     *  },
     *  tags={
     *          "dev"
     *      }
     * )
     */
    public function putContactsLeaderAction(Person $contact, Unit $group, Request $request)
    {
        $statusCode = ($contact->getId() == null || $group->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();

            $in_array = false;

            foreach ($contact->getLeaderOfs() as $existingGroup) {
                if ($existingGroup->getId() == $group->getId()) {
                    $in_array = true;
                }
            }

            if (!$in_array) {
                $contact->addLeaderOf($group);

                $em->persist($contact);
                $em->flush();
            }

            $statusCode = 204;

            $result = $this->addToSolr($contact);

            if ($result->getStatus() != 0) {
                $statusCode = 500;
            }

            $response = new Response();
            $response->setStatusCode($statusCode);

            return $response;
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }


    /**
     * @param Person $contact
     * @param Unit $group
     * @param Request $request
     * @return Response
     *
     * @ApiDoc(
     *  section="Contact",
     *  resource=true,
     *  description="Add an existing Contact to an existing Group where he is member",
     *  requirements={
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      },
     *      {
     *          "name"="group",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Group"
     *      }
     *  },
     *  tags={
     *          "dev"
     *      }
     * )
     */
    public function putContactsMemberAction(Person $contact, Unit $group, Request $request)
    {
        $statusCode = ($contact->getId() == null || $group->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();

            $in_array = false;

            foreach ($contact->getMemberOfs() as $existingGroup) {
                if ($existingGroup->getId() == $group->getId()) {
                    $in_array = true;
                }
            }

            if (!$in_array) {
                $contact->addMemberOf($group);

                $em->persist($contact);
                $em->flush();
            }

            $statusCode = 204;

            $result = $this->addToSolr($contact);

            if ($result->getStatus() != 0) {
                $statusCode = 500;
            }

            $response = new Response();
            $response->setStatusCode($statusCode);

            return $response;
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }


    /**
     * @param Person $contact
     * @return mixed
     */
    protected function addToSolr(Person $contact)
    {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $documents = [];

        $documents[] = $contact->toSolrDocument($update->createDocument());

        foreach ($contact->getEvents() as $event) {
            $documents[] = $event->toSolrDocument($update->createDocument());
        }

        foreach ($contact->getPersonsTypes() as $personType) {
            $entity = $personType->getFamily();
            $documents[] = $entity->toSolrDocument($update->createDocument());
        }

        foreach ($contact->getMemberOfs() as $group) {
            $documents[] = $group->toSolrDocument($update->createDocument());
        }

        foreach ($contact->getLeaderOfs() as $group) {
            $documents[] = $group->toSolrDocument($update->createDocument());
        }

        foreach ($contact->getDonations() as $donation) {
            $documents[] = $donation->toSolrDocument($update->createDocument());
        }


        $update->addDocuments($documents);
        $update->addCommit();

        $result = $client->update($update);

        return $result;
    }
}
