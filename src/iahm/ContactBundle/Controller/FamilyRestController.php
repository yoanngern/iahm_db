<?php

namespace iahm\ContactBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use iahm\ContactBundle\Entity\Comment;
use iahm\ContactBundle\Entity\Donation;
use iahm\ContactBundle\Entity\Location;
use iahm\ContactBundle\Entity\Person;
use iahm\ContactBundle\Entity\PersonType;
use iahm\ContactBundle\Entity\Unit;
use iahm\ContactBundle\Form\DonationToEntityType;
use iahm\ContactBundle\Form\FamilySimpleType;
use iahm\ContactBundle\Form\FamilyType;
use iahm\ContactBundle\Entity\Family;
use iahm\ContactBundle\Form\LocationType;
use iahm\ContactBundle\Form\PersonToEntityType;
use iahm\ContactBundle\Form\UnitToEntityType;
use iahm\ContactBundle\Form\PersonTypeSimpleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class FamilyRestController extends Controller
{

    /**
     * @param Family $entity
     * @return object
     * @Rest\View(serializerGroups={"Default","Details","EntityDetails"})
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Get Entity's details",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getEntityAction(Family $entity)
    {
        return $entity;

    }

    /**
     * @return array
     * @Rest\View(serializerGroups={"Default"})
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Return a collection of Entities",
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getEntitiesAction()
    {

        $entities = $this->getDoctrine()->getRepository('iahmContactBundle:Family')->findAll();

        if (!is_array($entities)) {
            throw $this->createNotFoundException();
        }
        return $entities;

    }

    /**
     * @param Family $entity
     * @return object
     * @Rest\View(serializerGroups={"Default","EntityDonations","Details"})
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Return a collection of Entity's Donations",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getEntityDonationsAction(Family $entity)
    {

        $entity = $this->getDoctrine()->getRepository('iahmContactBundle:Family')->findDonations($entity);

        if (!is_object($entity)) {
            throw $this->createNotFoundException();
        }
        return $entity;

    }

    /**
     * @param Family $entity
     * @return object
     * @Rest\View(serializerGroups={"Default","EntityGroups","Details"})
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Return a collection of Entity's Groups",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getEntityGroupsAction(Family $entity)
    {

        $entity = $this->getDoctrine()->getRepository('iahmContactBundle:Family')->findGroups($entity);

        if (!is_object($entity)) {
            throw $this->createNotFoundException();
        }
        return $entity;

    }

    /**
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Create a new Entity",
     *  input="iahm\ContactBundle\Form\FamilyType",
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function postEntityAction(Request $request)
    {
        return $this->processForm(new Family(), $request);
    }


    /**
     * @param Family $entity
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Update an Entity",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\FamilySimpleType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function putEntityAction(Family $entity, Request $request)
    {
        return $this->processForm($entity, $request);
    }


    /**
     * @param Family $entity
     * @return Response
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Remove an Entity",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      }
     *  },
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function deleteEntityAction(Family $entity)
    {

        $statusCode = ($entity->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;

    }


    /**
     * @param Family $entity
     * @param Request $request
     * @return View|Response
     */
    private function processForm(Family $entity, Request $request)
    {
        $statusCode = ($entity->getId() == null) ? 201 : 204;

        if ($statusCode === 201) {
            $form = $this->createForm(new FamilyType(), $entity, array('method' => 'POST'));
        } else {
            $form = $this->createForm(new FamilySimpleType(), $entity, array('method' => 'PUT'));
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($entity->getCommentTxt() != "") {

                if ($entity->getComment() != null) {
                    $comment = $entity->getComment();
                } else {
                    $comment = new Comment();
                }

                $comment->setText($entity->getCommentTxt());
                $entity->setComment($comment);

            }

            if (($entity->getCommentTxt() === "" || $entity->getCommentTxt() == null) && $entity->getComment() != null) {

                $entity->getComment()->setText("");
            }

            $em->persist($entity);
            $em->flush();

            $result = $this->addToSolr($entity);

            if($result->getStatus() != 0) {
                $statusCode = 500;
            }

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {

                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_get_entity', array('entity' => $entity->getId(),
                        true // absolute
                    ))
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }


    /**
     * @param Family $entity
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Create a new Contact to an Entity",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\PersonToEntityType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function postEntitiesContactsAction(Family $entity, Request $request)
    {
        $statusCode = ($entity->getId() == null) ? 404 : 204;

        $person = new Person();

        if ($statusCode === 204) {
            $form = $this->createForm(new PersonToEntityType(), $person, array('method' => 'POST'));

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

                $personType = new PersonType($person, $entity);

                if($person->getType() != null) {
                    $personType->setType($person->getType());
                }

                $em->persist($personType);
                $em->flush();

                $statusCode = 201;

                $result = $this->addToSolr($entity);

                if($result->getStatus() != 0) {
                    $statusCode = 500;
                }

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_get_contact', array('contact' => $personType->getPerson()->getId(),
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
     * @param Family $entity
     * @param Person $contact
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Add an existing Contact to an existing Entity",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      },
     *      {
     *          "name"="contact",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Contact"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\PersonTypeSimpleType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function putEntityContactAction(Family $entity, Person $contact, Request $request)
    {
        $statusCode = ($entity->getId() == null || $contact->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {

            if($this->getPersonType($entity, $contact) != null) {
                $isNew = false;
                $personType = $this->getPersonType($entity, $contact);
            } else {
                $isNew = true;
                $personType = new PersonType($contact, $entity);
            }

            $form = $this->createForm(new PersonTypeSimpleType(), $personType, array('method' => 'PUT'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($personType);
                $em->flush();

                if($isNew) {
                    $statusCode = 201;
                } else {
                    $statusCode = 204;
                }

                $result = $this->addToSolr($entity);

                if($result->getStatus() != 0) {
                    $statusCode = 500;
                }

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_get_event', array('event_id' => $personType->getPerson()->getId(),
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
     * @param Family $entity
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Create a new Donation of an Entity",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\DonationToEntityType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function postEntitiesDonationsAction(Family $entity, Request $request)
    {
        $statusCode = ($entity->getId() == null) ? 404 : 204;

        $donation = new Donation();

        if ($statusCode === 204) {
            $form = $this->createForm(new DonationToEntityType(), $donation, array('method' => 'POST'));

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

                $donation->setEntity($entity);

                $em->persist($donation);
                $em->flush();

                $statusCode = 201;

                $result = $this->addToSolr($entity);

                if($result->getStatus() != 0) {
                    $statusCode = 500;
                }

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_get_donation', array('donation_id' => $donation->getId(),
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
     * @param Family $entity
     * @param Person $contact
     * @param Request $request
     * @return Response
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Unlink a Contact from an Entity",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      },
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
     * )
     *
     */
    public function deleteEntityContactAction(Family $entity, Person $contact, Request $request)
    {

        $statusCode = ($entity->getId() == null || $contact->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();

            $personType = $this->getPersonType($entity, $contact);

            $em->remove($personType);
            $em->flush();
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;

    }


    /**
     * @param Family $entity
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Create a new Group to an Entity",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\UnitToEntityType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function postEntitiesGroupsAction(Family $entity, Request $request)
    {
        $statusCode = ($entity->getId() == null) ? 404 : 204;

        $group = new Unit();

        if ($statusCode === 204) {
            $form = $this->createForm(new UnitToEntityType(), $group, array('method' => 'POST'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $group->addEntity($entity);

                $em->persist($group);
                $em->flush();

                $statusCode = 201;

                $result = $this->addToSolr($entity);

                if($result->getStatus() != 0) {
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
     * @param Family $entity
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Entity",
     *  resource=true,
     *  description="Create a new Location for an Entity",
     *  requirements={
     *      {
     *          "name"="entity",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Entity"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\LocationType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function postEntitiesLocationsAction(Family $entity, Request $request)
    {
        $statusCode = ($entity->getId() == null) ? 404 : 204;

        $location = new Location();

        if ($statusCode === 204) {
            $form = $this->createForm(new LocationType(), $location, array('method' => 'POST'));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $location->setEntity($entity);

                $em->persist($location);
                $em->flush();

                $statusCode = 201;

                $result = $this->addToSolr($entity);

                if($result->getStatus() != 0) {
                    $statusCode = 500;
                }

                $response = new Response();
                $response->setStatusCode($statusCode);

                // set the `Location` header only when creating new resources
                if (201 === $statusCode) {
                    $response->headers->set('Location',
                        $this->generateUrl(
                            'api_get_location', array('location' => $location->getId(),
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
     * @param Family $entity
     * @param Person $person
     * @return mixed
     *
     */
    protected function getPersonType(Family $entity, Person $person) {

        $personType = $this->getDoctrine()->getRepository('iahmContactBundle:PersonType')->findOneByRelation($entity, $person);

        return $personType;
    }

    /**
     * @param Family $entity
     * @return mixed
     */
    protected function addToSolr(Family $entity) {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $documents = [];

        $documents[] = $entity->toSolrDocument($update->createDocument());

        foreach ($entity->getPersonsTypes() as $personType) {
            $contact = $personType->getPerson();

            $documents[] = $contact->toSolrDocument($update->createDocument());
        }

        foreach ($entity->getUnits() as $group) {
            $documents[] = $group->toSolrDocument($update->createDocument());
        }

        foreach ($entity->getDonations() as $donation) {
            $documents[] = $donation->toSolrDocument($update->createDocument());
        }

        $update->addDocuments($documents);
        $update->addCommit();

        $result = $client->update($update);

        return $result;
    }
}
