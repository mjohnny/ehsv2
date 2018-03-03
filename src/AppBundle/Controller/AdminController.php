<?php
/**
 * Created by PhpStorm.
 * User: macej
 * Date: 03/01/2018
 * Time: 21:33
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Event;
use AppBundle\Entity\Program;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class AdminController extends BaseAdminController
{
    public function answerAction()
    {
        $contact = $this->em->getRepository('AppBundle:Contact')->find($this->request->get('id'));
        return $this->render('easy_admin/contact/contactAnswer.html.twig', array('contact' => $contact));
    }

    public function prePersistEventEntity(Event $event)
    {
       $program = new Program();
       $event->setProgram($program);
       parent::prePersistEntity($event);
    }

    public function preUpdateEntity($entity)
    {
        if ($entity instanceof Article) $entity->setCreateDate(new \DateTime());
        if (method_exists($entity, 'setUser'))  $entity->setUser($this->getUser());
        if (method_exists($entity, 'setModificationDate')) $entity->setModificationDate(new \DateTime());

        parent::prePersistEntity($entity);
    }

    public function prePersistEntity($entity)
    {
        if (method_exists($entity, 'setUser')) $entity->setUser($this->getUser());
        parent::prePersistEntity($entity);
    }

    public function listRegisteredAction()
    {
//        $event = $this->em->getRepository('AppBundle:Event')->find($this->request->get('id'));
//        return $this->render('easy_admin/event/eventRegisteredList.html.twig', array('registeredList' => $event->getInscriptions()));
        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => 'EventInscription',
            'eventId'     => $this->request->query->get('id'),
        ));
    }

    public function listEventInscriptionAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_LIST);

        $dql_filter = $this->entity['list']['dql_filter'];
        $dql_filter = str_replace('eventId', $this->request->query->get('eventId'), $dql_filter );
        $fields = $this->entity['list']['fields'];
        $paginator = $this->findAll($this->entity['class'], $this->request->query->get('page', 1), $this->config['list']['max_results'], $this->request->query->get('sortField'), $this->request->query->get('sortDirection'), $dql_filter);

        $this->dispatch(EasyAdminEvents::POST_LIST, array('paginator' => $paginator));

        return $this->render($this->entity['templates']['list'], array(
            'paginator' => $paginator,
            'fields' => $fields,
            'delete_form_template' => $this->createDeleteForm($this->entity['name'], '__id__')->createView(),
        ));
    }
    public function preUpdateEventInscriptionEntity($entity)
    {
        $validated = $this->request->query->get('property');
        $newValue = $this->request->query->get('newValue');
        if ( (isset($validated) && $validated === 'validated' ) && (isset($newValue) && $newValue === 'true') ){
            $this->sendContactMail();
        }
        parent::preUpdateEntity($entity);
    }

    private function sendContactMail()
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('coucou')
            ->setFrom('test@test.fr')
            ->setTo(array('receipe@test.fr'))
            ->setBody(
                $this->renderView(
                    'event/registrationValidated.html.twig'),
                'text/html'
                )
        ;
        $this->get('mailer')->send($message);

    }
}