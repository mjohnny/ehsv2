<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NewsletterReceiver;
use AppBundle\Form\NewsletterReceiverType;
use AppBundle\Services\EhsNewsletterService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NewsletterReceiverController.
 *
 * @Route("newsletterreceiver")
 * @package AppBundle\Controller
 */
class NewsletterReceiverController extends Controller
{

    /**
     * Creates a new newsletterReceiver entity.
     *
     * @Route("/new", name="newsletterreceiver_new")
     * @Method({"GET", "POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Services\EhsNewsletterService $newsletterService
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request, EhsNewsletterService $newsletterService)
    {
        $newsletterReceiver = new Newsletterreceiver();
        $form = $this->createForm(
          NewsletterReceiverType::class,
          $newsletterReceiver,
          ['action' => $this->generateUrl('newsletterreceiver_new')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $verif = $em->getRepository(NewsletterReceiver::class)
              ->findOneBy(['email' => $newsletterReceiver->getEmail()]);

            if (!$verif) {
                $em->persist($newsletterReceiver);
                $em->flush();
            }
            $newsletterService->addNewReceiver($newsletterReceiver->getEmail());
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * Stop newsletter receiver.
     *
     * @Route("/stop", name="newsletter_stop")
     * @Method({"GET","POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Services\EhsNewsletterService $newsletterService
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function stopAction(Request $request, EhsNewsletterService $newsletterService)
    {
        $newsletterReceiver = new Newsletterreceiver();
        $form = $this->createForm(
          NewsletterReceiverType::class, $newsletterReceiver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->removeAction($newsletterReceiver->getEmail(), $newsletterService);
        }

        return $this->render('newsletterreceiver/stop.html.twig',
        [
          'form' =>$form->createView(),
        ]
          );

    }

    /**
     * Remove neseletter receiver.
     *
     * @Route("/stop/{email}", name="newsletter_remove")
     * @Method("GET")
     *
     * @param $email
     * @param \AppBundle\Services\EhsNewsletterService $newsletterService
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction($email, EhsNewsletterService $newsletterService)
    {
        $newsletterService->removeReceiver($email);
        return $this->redirectToRoute('homepage');
    }
}
