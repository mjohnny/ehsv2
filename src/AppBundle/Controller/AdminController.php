<?php
/**
 * Created by PhpStorm.
 * User: macej
 * Date: 03/01/2018
 * Time: 21:33
 */

namespace AppBundle\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class AdminController extends BaseAdminController
{
    public function answerAction()
    {
        $contact = $this->em->getRepository('AppBundle:Contact')->find($this->request->get('id'));
        return $this->render('easyAdminBundle/contactAnswer.html.twig', array('contact' => $contact));
    }
}