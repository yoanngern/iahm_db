<?php

namespace iahm\ContactBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use iahm\ContactBundle\Form\UnitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use iahm\ContactBundle\Entity\Unit;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class UnitRestController extends FOSRestController
{

    /**
     * @param Unit $group
     * @return object
     * @Rest\View(serializerGroups={"Default","Details","GroupDetails"})
     *
     * @ApiDoc(
     *  section="Group",
     *  resource=true,
     *  description="Get Group's details",
     *  requirements={
     *      {
     *          "name"="group",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Group"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getGroupAction(Unit $group)
    {
        return $group;
    }


    /**
     * @return array
     * @Rest\View(serializerGroups={"Default"})
     *
     * @ApiDoc(
     *  section="Group",
     *  resource=true,
     *  description="Return a collection of Groups",
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function getGroupsAction()
    {
        $donations = $this->getDoctrine()->getRepository('iahmContactBundle:Unit')->findAll();

        if (!is_array($donations)) {
            throw $this->createNotFoundException();
        }
        return $donations;
    }


    /**
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Group",
     *  resource=true,
     *  description="Create a new Group",
     *  input="iahm\ContactBundle\Form\UnitType",
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function postGroupsAction(Request $request)
    {
        return $this->processForm(new Unit(), $request);
    }


    /**
     * @param Unit $group
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Group",
     *  resource=true,
     *  description="Update a Group",
     *  requirements={
     *      {
     *          "name"="group",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Group"
     *      }
     *  },
     *  input="iahm\ContactBundle\Form\UnitType",
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function putGroupAction(Unit $group, Request $request)
    {
        return $this->processForm($group, $request);
    }


    /**
     * @param Unit $group
     * @return Response
     *
     * @ApiDoc(
     *  section="Group",
     *  resource=true,
     *  description="Remove a Group",
     *  requirements={
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
     *
     * )
     *
     */
    public function deleteGroupAction(Unit $group)
    {

        $statusCode = ($group->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($group);
            $em->flush();
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;

    }


    /**
     * @param Unit $group
     * @param Request $request
     * @return View|Response
     */
    private function processForm(Unit $group, Request $request)
    {
        $statusCode = ($group->getId() == null) ? 201 : 204;

        if ($statusCode === 201) {
            $form = $this->createForm(new UnitType(), $group, array('method' => 'POST'));
        } else {
            $form = $this->createForm(new UnitType(), $group, array('method' => 'PUT'));
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            $result = $this->addToSolr($group);

            if($result->getStatus() != 0) {
                $statusCode = 500;
            }

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_get_group', array('group' => $group->getId(),
                        true // absolute
                    ))
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }


    /**
     * @param Unit $group
     * @return mixed
     */
    protected function addToSolr(Unit $group) {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $documents = [];

        $documents[] = $group->toSolrDocument($update->createDocument());

        foreach ($group->getMembers() as $contact) {
            $documents[] = $contact->toSolrDocument($update->createDocument());
        }

        foreach ($group->getLeaders() as $contact) {
            $documents[] = $contact->toSolrDocument($update->createDocument());
        }

        foreach ($group->getEntities() as $entity) {
            $documents[] = $entity->toSolrDocument($update->createDocument());
        }

        if($group->getEvent() != null) {
            $documents[] = $group->getEvent()->toSolrDocument($update->createDocument());
        }

        foreach ($group->getChildrens() as $group) {
            $documents[] = $group->toSolrDocument($update->createDocument());
        }

        if($group->getParent() != null) {
            $documents[] = $group->getParent()->toSolrDocument($update->createDocument());
        }

        $update->addDocuments($documents);
        $update->addCommit();

        $result = $client->update($update);

        return $result;
    }


}
