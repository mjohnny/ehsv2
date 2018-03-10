<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Services\EhsSendMailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contact controller.
 *
 * @Route("contact")
 */
class ContactController extends Controller
{

    /** @var \AppBundle\Services\EhsSendMailService $mailerService */
    protected $mailerService;

    /**
     * ContactController constructor.
     *
     * @param \AppBundle\Services\EhsSendMailService $mailService
     */
    public function __construct(EhsSendMailService $mailService)
    {
        $this->mailerService = $mailService;
    }

    /**
     * Lists all contact entities.
     *
     * @Route("/", name="contact_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contacts = $em->getRepository('AppBundle:Contact')->findAll();

        return $this->render(
          'contact/index.html.twig',
          [
            'contacts' => $contacts,
          ]
        );
    }

    /**
     * Creates a new contact entity.
     *
     * @Route("/new", name="contact_new")
     * @Method({"GET", "POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function newAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(
          'AppBundle\Form\ContactType',
          $contact,
          ['action' => $this->generateUrl('contact_new')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $contact->setMessageDate(new \DateTime());
            $em->persist($contact);
            $em->flush();
            $template = 'contact/contactEmail.html.twig';
            $context = [
              'contact' => $contact,
            ];
            $toEmail = $this->getParameter('mailer_contact');
            $this->mailerService->sendMessage(
              $template,
              $context,
              $contact->getEmail(),
              $toEmail
            );

            // on tente de rediriger vers la page d'origine
            $url = $request->headers->get('referer');
            if (empty($url)) {
                $url = $this->generateUrl('homepage');
            }

            return new RedirectResponse($url);
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * Finds and displays a contact entity.
     *
     * @Route("/{id}", name="contact_show")
     * @Method("GET")
     */
    public function showAction(Contact $contact)
    {

        return $this->render(
          'contact/show.html.twig',
          [
            'contact' => $contact,
          ]
        );
    }

    /**
     * Answer contact.
     *
     * @Route("/answer/{id}", name="contact_answer")
     * @Method("POST")
     *
     * @IsGranted("ROLE_ADMIN")
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function answerAction(Contact $contact)
    {
        $context = [
          'contact' => $contact,
          'response' => $_POST['response'],
        ];
        $sendFrom = [$this->getParameter('mailer_user') => $this->getParameter('site')];
        $this->mailerService->sendMessage('contact/response.html.twig', $context,$sendFrom, $contact->getEmail() );

        return $this->redirectToRoute(
          'easyadmin',
          [
            'action' => 'list',
            'entity' => 'Contact',
          ]
        );
    }
}
