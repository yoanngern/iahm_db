<?php

namespace iahm\ContactBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use iahm\ContactBundle\Entity\Event;
use iahm\ContactBundle\Entity\Unit;
use iahm\ContactBundle\Form\EventType;
use iahm\ContactBundle\Form\UnitSimpleType;
use iahm\ContactBundle\Form\UnitToEventType;
use iahm\ContactBundle\Form\UnitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class EventRestController extends Controller
{

    /**
     * @param Event $event
     * @return object
     * @Rest\View(serializerGroups={"Default","Details","EventDetails"})
     *
     * @ApiDoc(
     *  section="Event",
     *  resource=true,
     *  description="Get Event's details",
     *  requirements={
     *      {
     *          "name"="event",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Event"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     */
    public function getEventAction(Event $event)
    {
        return $event;

    }

    /**
     * @return array
     * @Rest\View(serializerGroups={"Default"})
     *
     * @ApiDoc(
     *  section="Event",
     *  resource=true,
     *  description="Return a collection of Events",
     *  tags={
     *         "dev"
     *     }
     * )
     */
    public function getEventsAction()
    {

        $donations = $this->getDoctrine()->getRepository('iahmContactBundle:Event')->findAll();

        if (!is_array($donations)) {
            throw $this->createNotFoundException();
        }
        return $donations;

    }


    /**
     * @param Event $event
     * @return array
     * @Rest\View(serializerGroups={"Default","EventGroups","Details"})
     *
     * @ApiDoc(
     *  section="Event",
     *  resource=true,
     *  description="Return a collection of Event's Groups",
     *  requirements={
     *      {
     *          "name"="event",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Event"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getEventGroupsAction(Event $event)
    {

        $event = $this->getDoctrine()->getRepository('iahmContactBundle:Event')->findGroups($event);

        if (!is_object($event)) {
            throw $this->createNotFoundException();
        }
        return $event;

    }

    /**
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Event",
     *  resource=true,
     *  description="Create a new Group for an existing Event",
     *  requirements={
     *      {
     *          "name"="event",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Event"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\UnitToEventType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function postEventGroupsAction(Event $event, Request $request)
    {
        $statusCode = ($event->getId() == null) ? 404 : 204;

        $unit = new Unit();

        if ($statusCode === 204) {
            $form = $this->createForm(new UnitToEventType(), $unit, array('method' => 'POST'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $unit->setEvent($event);
                $em->persist($unit);
                $em->flush();

                $statusCode = 201;

                $result = $this->addToSolr($event);

                if ($result->getStatus() != 0) {
                    $statusCode = 500;
                }

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_get_event', array('event' => $event->getId(),
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
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Event",
     *  resource=true,
     *  description="Create a new Event",
     *  input="iahm\ContactBundle\Form\EventType",
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function postEventsAction(Request $request)
    {
        return $this->processForm(new Event(), $request);
    }

    /**
     * @param Event $event
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Event",
     *  resource=true,
     *  description="Update an Event",
     *  input="iahm\ContactBundle\Form\EventType",
     *  requirements={
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
     *
     * )
     */
    public function putEventsAction(Event $event, Request $request)
    {
        return $this->processForm($event, $request);
    }


    /**
     * @param Event $event
     * @return Response
     *
     * @ApiDoc(
     *  section="Event",
     *  resource=true,
     *  description="Remove an Event",
     *  requirements={
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
     *
     * )
     *
     */
    public function deleteEventAction(Event $event)
    {

        $statusCode = ($event->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;

    }


    /**
     * @param Event $event
     * @param Request $request
     * @return View|Response
     */
    private function processForm(Event $event, Request $request)
    {
        $statusCode = ($event->getId() == null) ? 201 : 204;

        if ($statusCode === 201) {
            $form = $this->createForm(new EventType(), $event, array('method' => 'POST'));
        } else {
            $form = $this->createForm(new EventType(), $event, array('method' => 'PUT'));
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($event);
            $em->flush();


            $result = $this->addToSolr($event);

            if ($result->getStatus() != 0) {
                $statusCode = 500;
            }


            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_get_event', array('event' => $event->getId(),
                        true // absolute
                    ))
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }


    /**
     * @param Event $event
     * @return mixed
     */
    protected function addToSolr(Event $event)
    {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $documents = [];

        $documents[] = $event->toSolrDocument($update->createDocument());

        if (sizeof($event->getPersons())) {
            foreach ($event->getPersons() as $contact) {
                $documents[] = $contact->toSolrDocument($update->createDocument());
            }
        }

        if (sizeof($event->getGroups())) {
            foreach ($event->getGroups() as $group) {
                $documents[] = $group->toSolrDocument($update->createDocument());
            }
        }

        if (sizeof($event->getChildrens())) {
            foreach ($event->getChildrens() as $event) {
                $documents[] = $event->toSolrDocument($update->createDocument());
            }
        }

        if ($event->getParent() != null) {
            $documents[] = $event->getParent()->toSolrDocument($update->createDocument());
        }

        $update->addDocuments($documents);
        $update->addCommit();

        $result = $client->update($update);

        return $result;
    }

}
