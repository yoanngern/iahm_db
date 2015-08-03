<?php

namespace iahm\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SolrController extends Controller
{
    /**
     * @return mixed
     */
    public function indexAllAction()
    {

        $this->indexDelete();
        $this->indexContacts();
        $this->indexEntities();
        $this->indexDonations();
        $this->indexGroups();
        $this->indexEvents();

        return $this->redirect($this->generateUrl('api_get_contacts'));

    }

    /**
     * @return mixed
     */
    public function indexContactsAction()
    {
        $this->indexContacts();
        return $this->redirect($this->generateUrl('api_get_contacts'));
    }

    /**
     * @return mixed
     */
    public function indexEntitiesAction()
    {

        $this->indexEntities();

        return $this->redirect($this->generateUrl('api_get_entities'));

    }

    /**
     * @return mixed
     */
    public function indexDonationsAction()
    {
        $this->indexDonations();
        return $this->redirect($this->generateUrl('api_get_donations'));
    }

    /**
     * @return mixed
     */
    public function indexGroupsAction()
    {
        $this->indexGroups();
        return $this->redirect($this->generateUrl('api_get_groups'));
    }

    /**
     * @return mixed
     */
    public function indexEventsAction()
    {
        $this->indexEvents();
        return $this->redirect($this->generateUrl('api_get_events'));
    }

    /**
     * @return mixed
     */
    public function indexDeleteAction()
    {
        $this->indexDelete();
        return $this->redirect($this->generateUrl('api_get_contacts'));
    }

    /**
     * @return mixed
     */
    public function indexContacts()
    {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $update->addDeleteQuery("doc_type:contact");

        $documents = [];

        $contacts = $this->getDoctrine()->getRepository('iahmContactBundle:Person')->findAll();

        foreach ($contacts as $contact) {
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
        }

        $update->addDocuments($documents);
        $update->addCommit();
        $update->addOptimize();

        $result = $client->update($update);

        return $result;
    }


    /**
     * @return mixed
     */
    public function indexEntities()
    {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $update->addDeleteQuery("doc_type:entity");

        $documents = [];

        $entities = $this->getDoctrine()->getRepository('iahmContactBundle:Family')->findAll();

        foreach ($entities as $entity) {
            $documents[] = $entity->toSolrDocument($update->createDocument());

            foreach ($entity->getPersonsTypes() as $personType) {
                $contact = $personType->getPerson();

                $documents[] = $contact->toSolrDocument($update->createDocument());

                foreach ($contact->getEvents() as $event) {
                    $documents[] = $event->toSolrDocument($update->createDocument());
                }
            }

            foreach ($entity->getUnits() as $group) {
                $documents[] = $group->toSolrDocument($update->createDocument());
            }

            foreach ($entity->getDonations() as $donation) {
                $documents[] = $donation->toSolrDocument($update->createDocument());
            }
        }

        $update->addDocuments($documents);
        $update->addCommit();
        $update->addOptimize();

        $result = $client->update($update);

        return $result;
    }


    /**
     * @return mixed
     */
    public function indexDonations()
    {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $update->addDeleteQuery("doc_type:donation");

        $documents = [];

        $donations = $this->getDoctrine()->getRepository('iahmContactBundle:Donation')->findAll();

        foreach ($donations as $donation) {
            $documents[] = $donation->toSolrDocument($update->createDocument());

            if($donation->getEntity() != null) {
                $documents[] = $donation->getEntity()->toSolrDocument($update->createDocument());
            }

            if($donation->getPerson() != null) {
                $documents[] = $donation->getPerson()->toSolrDocument($update->createDocument());
            }
        }

        $update->addDocuments($documents);
        $update->addCommit();
        $update->addOptimize();

        $result = $client->update($update);

        return $result;
    }

    /**
     * @return mixed
     */
    public function indexGroups()
    {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $update->addDeleteQuery("doc_type:group");

        $documents = [];

        $groups = $this->getDoctrine()->getRepository('iahmContactBundle:Unit')->findAll();

        foreach ($groups as $group) {
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
        }

        $update->addDocuments($documents);
        $update->addCommit();
        $update->addOptimize();

        $result = $client->update($update);

        return $result;
    }


    /**
     * @return mixed
     */
    public function indexEvents()
    {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $update->addDeleteQuery("doc_type:event");

        $documents = [];

        $events = $this->getDoctrine()->getRepository('iahmContactBundle:Event')->findAll();

        foreach ($events as $event) {
            $documents[] = $event->toSolrDocument($update->createDocument());

            foreach ($event->getPersons() as $contact) {
                $documents[] = $contact->toSolrDocument($update->createDocument());
            }

            foreach ($event->getGroups() as $group) {
                $documents[] = $group->toSolrDocument($update->createDocument());
            }

            foreach ($event->getChildrens() as $event) {
                $documents[] = $event->toSolrDocument($update->createDocument());
            }

            if($event->getParent() != null) {
                $documents[] = $event->getParent()->toSolrDocument($update->createDocument());
            }
        }

        $update->addDocuments($documents);
        $update->addCommit();
        $update->addOptimize();

        $result = $client->update($update);

        return $result;
    }


    /**
     * @return mixed
     */
    public function indexDelete()
    {
        $client = $this->get('solarium.client');

        $update = $client->createUpdate();

        $update->addDeleteQuery("*:*");
        $update->addCommit();
        $update->addOptimize();

        $result = $client->update($update);

        return $result;
    }
}
