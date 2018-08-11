<?php
/**
 * Created by PhpStorm.
 * User: macej
 * Date: 03/01/2018
 * Time: 21:33
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\BaseEntity;
use AppBundle\Entity\Event;
use AppBundle\Entity\EventInscription;
use AppBundle\Entity\Program;
use AppBundle\Services\EhsSendMailService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class AdminController extends BaseAdminController
{

    /**
     * @var \AppBundle\Services\EhsSendMailService
     */
    private $EhsSendMailService;

    /**
     * AdminController constructor.
     *
     * @param \AppBundle\Services\EhsSendMailService $ehsSendMailService
     */
    public function __construct(EhsSendMailService $ehsSendMailService)
    {
        $this->EhsSendMailService = $ehsSendMailService;
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function answerAction()
    {
        $contact = $this->em->getRepository('AppBundle:Contact')->find($this->request->get('id'));
        return $this->render('easy_admin/contact/contactAnswer.html.twig', array('contact' => $contact));
    }

    /**
     * @param object $entity
     */
    public function preUpdateEntity($entity)
    {
        if ($entity instanceof Article) {
            $content = $entity->getContent();
            $content = preg_replace('/(\.\.\/){1,}/', '/', $content);
            $entity->setContent($content);
        }
        if ($entity instanceof Event) {
            $content = $entity->getPresentation();
            $content = preg_replace('/(\.\.\/){1,}/', '/', $content);
            $entity->setPresentation($content);
        }
        if (method_exists($entity, 'setUser'))  $entity->setUser($this->getUser());
        if (method_exists($entity, 'setModificationDate')) $entity->setModificationDate(new \DateTime());

        parent::prePersistEntity($entity);
    }

    /**
     * @param object $entity
     */
    public function prePersistEntity($entity)
    {
        if ($entity instanceof Event) {
            $program = new Program();
            $entity->setProgram($program);
            $content = $entity->getPresentation();
            $content = preg_replace('/(\.\.\/){1,}/', '/', $content);
            $entity->setPresentation($content);
        }
        if ($entity instanceof Article) {
            $content = $entity->getContent();
            $content = preg_replace('/(\.\.\/){1,}/', '/', $content);
            $entity->setContent($content);
        }
        if (method_exists($entity, 'setAliasPath'))
            $entity->setAliasPath(BaseEntity::createAliasPath($entity->getTitle()));

        if (method_exists($entity, 'setUser')) $entity->setUser($this->getUser());

        parent::prePersistEntity($entity);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function listRegisteredAction()
    {
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
        $paginator = $this->findAll($this->entity['class'],
            $this->request->query->get('page', 1),
            $this->config['list']['max_results'],
            $this->request->query->get('sortField'),
            $this->request->query->get('sortDirection'), $dql_filter);

        $this->dispatch(EasyAdminEvents::POST_LIST, array('paginator' => $paginator));

        return $this->render($this->entity['templates']['list'], array(
            'paginator' => $paginator,
            'fields' => $fields,
            'delete_form_template' => $this->createDeleteForm($this->entity['name'], '__id__')->createView(),
        ));
    }

    /**
     * @param \AppBundle\Entity\EventInscription $entity
     *
     * @throws \Throwable
     */
    public function preUpdateEventInscriptionEntity(EventInscription $entity)
    {
        $validated = $this->request->query->get('property');
        $newValue = $this->request->query->get('newValue');
        if ( (isset($validated) && $validated === 'validated' ) && (isset($newValue) && $newValue === 'true') ){
            $send_from = [$this->getParameter('mailer_user') => $this->getParameter('site')];
            $this->EhsSendMailService->sendMessage('eventinscription/validatedMail.html.twig',
                ['eventinscription' => $entity], $send_from, $entity->getEmail());
        }
        parent::preUpdateEntity($entity);
    }

}