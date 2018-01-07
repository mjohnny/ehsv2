<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contact controller.
 *
 * @Route("contact")
 */
class ContactController extends Controller
{
    /**
     * Lists all contact entities.
     *
     * @Route("/", name="contact_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contacts = $em->getRepository('AppBundle:Contact')->findAll();

        return $this->render('contact/index.html.twig', array(
            'contacts' => $contacts,
        ));
    }

    /**
     * Finds and displays a contact entity.
     *
     * @Route("/{id}", name="contact_show")
     * @Method("GET")
     */
    public function showAction(Contact $contact)
    {

        return $this->render('contact/show.html.twig', array(
            'contact' => $contact,
        ));
    }

    /**
     * answer contact
     *
     * @Route("/answer/{id}", name="contact_answer")
     * @Method("POST")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function answerAction(Contact $contact)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('RE:'. $contact->getSubject())
            ->setFrom($this->getParameter('mailer_contact'))
            ->setTo(array($contact->getEmail(),$this->getParameter('mailer_contact')))
            ->setBody(
                $this->renderView(
                    'contact/response.html.twig',
                    array('message' => $contact->getMessage(),
                        'response'=>$_POST['response'])
                ),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'entity' => 'Contact',
        ));
    }
}
