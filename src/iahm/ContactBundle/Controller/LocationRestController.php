<?php

namespace iahm\ContactBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use iahm\ContactBundle\Entity\Comment;
use iahm\ContactBundle\Entity\Location;
use iahm\ContactBundle\Form\LocationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class LocationRestController extends Controller
{

    /**
     * @param Location $location
     * @return object
     * @Rest\View(serializerGroups={"Default","Details","LocationDetails"})
     *
     * @ApiDoc(
     *  section="Location",
     *  resource=true,
     *  description="Get Location's details",
     *  requirements={
     *      {
     *          "name"="location",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Location"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getLocationAction(Location $location)
    {
        return $location;

    }


    /**
     * @param Location $location
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Location",
     *  resource=true,
     *  description="Update a Location",
     *  input="iahm\ContactBundle\Form\LocationType",
     *  requirements={
     *      {
     *          "name"="location",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Location"
     *      }
     *  },
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function putLocationAction(Location $location, Request $request)
    {
        $statusCode = ($location->getId() == null) ? 201 : 204;

        $form = $this->createForm(new LocationType(), $location, array('method' => 'PUT'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($location);
            $em->flush();

            $result = $this->addToSolr($location);

            if($result->getStatus() != 0) {
                $statusCode = 500;
            }

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_get_location', array('location_id' => $location->getId(),
                        true // absolute
                    ))
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }


    /**
     * @param Location $location
     * @return Response
     *
     * @ApiDoc(
     *  section="Location",
     *  resource=true,
     *  description="Remove a Location",
     *  requirements={
     *      {
     *          "name"="location",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Location"
     *      }
     *  },
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function deleteLocationAction(Location $location)
    {

        $statusCode = ($location->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();

            $client = $this->get('solarium.client');

            $update = $client->createUpdate();

            $update->addDeleteById("location_" . $location->getId());

            $update->addCommit();
            $update->addOptimize();
            $result = $client->update($update);

            if($result->getStatus() != 0) {
                $statusCode = 500;
            } else {
                $em->remove($location);
                $em->flush();
            }

        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;

    }


    /**
     * @param Location $location
     * @return mixed
     */
    protected function addToSolr(Location $location) {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $documents = [];


        $documents[] = $location->getEntity()->toSolrDocument($update->createDocument());

        $update->addDocuments($documents);
        $update->addCommit();

        $result = $client->update($update);

        return $result;
    }

}
