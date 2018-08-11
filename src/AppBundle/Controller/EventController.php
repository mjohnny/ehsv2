<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\EventInscription;
use AppBundle\Form\EventInscriptionType;
use AppBundle\Services\EhsSendMailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EventController.
 *
 * @Route("event")
 * @package AppBundle\Controller
 */
class EventController extends Controller
{

    /**
     * Lists all event entities.
     *
     * @Route("/", name="event_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('AppBundle:Event')->findBy(
          [],
          ['startDate' => 'DESC']
        );

        return $this->render(
          'event/index.html.twig',
          [
            'events' => $events,
          ]
        );
    }

    /**
     * Finds and displays a event entity.
     *
     * @Route("/{id}", name="event_show")
     * @ParamConverter("event", options={"mapping": {"id": "aliasPath"}})
     * @Method("GET")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Event $event
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Event $event)
    {
        $eventInscription = new EventInscription;
        $form = $this->createForm(
          EventInscriptionType::class,
          $eventInscription,
          [
            'action' => $this->generateUrl(
              'event_eventinscription_new',
              ['id' => $event->getId()]
            ),
            'method' => 'POST',
          ]
        );

        $url = $request->headers->get('referer');
        if (!$url) {
            $url = $this->generateUrl('homepage');
        }

        return $this->render(
          'event/show.html.twig',
          [
            'backUrl' => $url,
            'event' => $event,
            'form' => $form->createView(),
          ]
        );
    }

    /**
     * Finds and displays a program entity.
     *
     * @Route("/{id}/program", name="program_show")
     * @ParamConverter("event", options={"mapping": {"id": "aliasPath"}})
     * @Method("GET")
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function programAction(Event $event)
    {

        return $this->render(
          'program/show.html.twig',
          [
            'event' => $event,
          ]
        );
    }

    /**
     * Creates a new eventInscription entity.
     *
     * @Route("/{id}/eventinscription", name="event_eventinscription_new")
     * @ParamConverter("event", options={"mapping": {"id": "aliasPath"}})
     * @Method({"GET", "POST"})
     *
     *{@inheritdoc}
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Event $event
     * @throws
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newEventInscriptionAction(
      Request $request,
      EhsSendMailService $sendMailService,
      Event $event
    ) {
        $eventInscription = new Eventinscription();
        $form = $this->createForm(
          'AppBundle\Form\EventInscriptionType',
          $eventInscription
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $eventInscription->setEvent($event);
            $em->persist($eventInscription);
            $em->flush();

            $context = [
              'event' => $event,
              'eventinscription' => $eventInscription,
            ];
            $sendFrom = [$this->getParameter('mailer_user') => $this->getParameter('site')];
            $sendMailService->sendMessage(
              'eventinscription/registrationMail.html.twig', $context, $sendFrom, $eventInscription->getEmail());

            $this->addFlash(
              'success',
              $this->get('translator')->trans('event.inscription ok')
            );

            return $this->redirectToRoute(
              'event_show',
              ['id' => $event->getAliasPath()]
            );
        }

        return $this->render(
          'eventinscription/new.html.twig',
          [
            'eventInscription' => $eventInscription,
            'event' => $event,
            'form' => $form->createView(),
          ]
        );
    }
}
