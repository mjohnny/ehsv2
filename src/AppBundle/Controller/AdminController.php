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

class AdminController extends BaseAdminController
{
    public function answerAction()
    {
        $contact = $this->em->getRepository('AppBundle:Contact')->find($this->request->get('id'));
        return $this->render('easyAdminBundle/contactAnswer.html.twig', array('contact' => $contact));
    }

    public function prePersistEventEntity(Event $event)
    {
       $program = new Program();
       $event->setProgram($program);
       parent::prePersistEntity($event);
    }

    public function prePersistArticleEntity(Article $article)
    {
        $article->setUser($this->getUser());
        parent::prePersistEntity($article);
    }

    public function preUpdateArticleEntity(Article $article)
    {
        $article->setUser($this->getUser());
        $article->setCreateDate(new \DateTime());
        parent::prePersistEntity($article);
    }
}