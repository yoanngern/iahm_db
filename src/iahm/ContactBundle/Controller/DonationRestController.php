<?php

namespace iahm\ContactBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use iahm\ContactBundle\Entity\Comment;
use iahm\ContactBundle\Entity\Donation;
use iahm\ContactBundle\Form\DonationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class DonationRestController extends Controller
{

    /**
     * @param Donation $donation
     * @return object
     * @Rest\View(serializerGroups={"Default","Details","DonationDetails"})
     *
     * @ApiDoc(
     *  section="Donation",
     *  resource=true,
     *  description="Get Donation's details",
     *  requirements={
     *      {
     *          "name"="donation",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Donation"
     *      }
     *  },
     * tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getDonationAction(Donation $donation)
    {
        return $donation;

    }

    /**
     * @return array
     * @Rest\View(serializerGroups={"Default"})
     *
     * @ApiDoc(
     *  section="Donation",
     *  resource=true,
     *  description="Return a collection of Donation",
     *  tags={
     *         "dev"
     *     }
     * )
     *
     */
    public function getDonationsAction()
    {

        $donations = $this->getDoctrine()->getRepository('iahmContactBundle:Donation')->findAll();

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
     *  section="Donation",
     *  resource=true,
     *  description="Create a new Donation",
     *  input="iahm\ContactBundle\Form\DonationType",
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function postDonationsAction(Request $request)
    {
        return $this->processForm(new Donation(), $request);
    }

    /**
     * @param Donation $donation
     * @param Request $request
     * @return View|Response
     *
     * @ApiDoc(
     *  section="Donation",
     *  resource=true,
     *  description="Update a Donation",
     *  input="iahm\ContactBundle\Form\DonationType",
     *  requirements={
     *      {
     *          "name"="donation",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Donation"
     *      }
     *  },
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function putDonationAction(Donation $donation, Request $request)
    {
        return $this->processForm($donation, $request);
    }

    /**
     * @param Donation $donation
     * @return Response
     *
     * @ApiDoc(
     *  section="Donation",
     *  resource=true,
     *  description="Remove a Donation",
     *  requirements={
     *      {
     *          "name"="donation",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="id of the Donation"
     *      }
     *  },
     *  tags={
     *          "dev"
     *      }
     *
     * )
     *
     */
    public function deleteDonationAction(Donation $donation)
    {

        $statusCode = ($donation->getId() == null) ? 404 : 204;

        if ($statusCode === 204) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($donation);
            $em->flush();
        }

        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;

    }


    /**
     * @param Donation $donation
     * @param Request $request
     * @return View|Response
     */
    private function processForm(Donation $donation, Request $request)
    {
        $statusCode = ($donation->getId() == null) ? 201 : 204;

        if ($statusCode === 201) {
            $form = $this->createForm(new DonationType(), $donation, array('method' => 'POST'));
        } else {
            $form = $this->createForm(new DonationType(), $donation, array('method' => 'PUT'));
        }

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

            $em->persist($donation);
            $em->flush();

            $result = $this->addToSolr($donation);

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
        }

        return View::create($form, 400);
    }


    /**
     * @param Donation $donation
     * @return mixed
     */
    protected function addToSolr(Donation $donation)
    {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $documents = [];

        $documents[] = $donation->toSolrDocument($update->createDocument());

        if ($donation->getEntity() != null) {
            $documents[] = $donation->getEntity()->toSolrDocument($update->createDocument());
        }

        if ($donation->getPerson() != null) {
            $documents[] = $donation->getPerson()->toSolrDocument($update->createDocument());
        }

        $update->addDocuments($documents);
        $update->addCommit();

        $result = $client->update($update);

        return $result;
    }

}
