<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NewsletterReceiver;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Newsletterreceiver controller.
 *
 * @Route("newsletterreceiver")
 */
class NewsletterReceiverController extends Controller
{
    /**
     * Lists all newsletterReceiver entities.
     *
     * @Route("/", name="newsletterreceiver_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $newsletterReceivers = $em->getRepository('AppBundle:NewsletterReceiver')->findAll();

        return $this->render('newsletterreceiver/index.html.twig', array(
            'newsletterReceivers' => $newsletterReceivers,
        ));
    }

    /**
     * Creates a new newsletterReceiver entity.
     *
     * @Route("/new", name="newsletterreceiver_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request){
        $newsletterReceiver = new Newsletterreceiver();
        $form = $this->createForm('AppBundle\Form\NewsletterReceiverType', $newsletterReceiver,
            array('action'=>$this->generateUrl('newsletterreceiver_new')));
        $form->handleRequest($request);

        return $this->redirectToRoute('homepage');
    }

    /**
     * Finds and displays a newsletterReceiver entity.
     *
     * @Route("/{id}", name="newsletterreceiver_show")
     * @Method("GET")
     */
    public function showAction(NewsletterReceiver $newsletterReceiver)
    {

        return $this->render('newsletterreceiver/show.html.twig', array(
            'newsletterReceiver' => $newsletterReceiver,
        ));
    }
}
