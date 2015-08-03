<?php

namespace iahm\ContactBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class SearchRestController extends Controller
{

    /**
     * @return object
     * @Rest\View(serializerGroups={"Default","Details"})
     *
     * @ApiDoc(
     *  section="Search",
     *  resource=true,
     *  description="List of documents where matching a specific query",
     *  parameters={
     *      {"name"="q", "dataType"="string", "required"=true, "description"="query"},
     *      {"name"="type", "dataType"="string", "required"=false, "description"="type of document"}
     *  },
     *  tags={
     *          "dev"
     *      }
     * )
     *
     */
    public function getSearchAction(Request $request)
    {


        $q = $request->get("q");
        $type = $request->get("type");


        if($q == "") {
            $query = "*:*";
        } else {
            $query = $q;
        }

        if($type != "") {
            $query .= " AND doc_type:" . $type;
        }

        $client = $this->get('solarium.client');

        $select = $client->createSelect();
        $select->setQuery($query);
        $results = $client->select($select);

        $documents = [];

        foreach ($results as $result) {
            $documents[] = $result->getFields();
        }

        $data = [];
        $data["num_results"] = $results->getNumFound();
        $data["completed_in"] = $results->getQueryTime();
        $data["query"] = $q;
        $data["documents"] = $documents;

        return $data;

    }

}
