<?php

namespace AppBundle\Controller;

use AppBundle\Services\EhsSendMailService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class UserController.
 *
 * @Route("user")
 * @package AppBundle\Controller
 */
class UserController extends Controller
{

    /**
     * @var \AppBundle\Services\EhsSendMailService
     */
    private $mailerService;

    /**
     * UserController constructor.
     *
     * @param \AppBundle\Services\EhsSendMailService $mailService
     */
    public function __construct(EhsSendMailService $mailService)
    {
        $this->mailerService = $mailService;
    }

    /**
     * Validate member Membership
     *
     * @Route("/validated", name="user_validate")
     * @Method("GET")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * {@inheritdoc}
     */
    public function validateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find(
          $id
        );
        $now = new \DateTime();

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        if (!$user->getUptodate()) {
            $url = $this->generateUrl(
              'fos_user_resetting_reset',
              ['token' => $user->getConfirmationToken()],
              UrlGeneratorInterface::ABSOLUTE_URL
            );
            $context = [
              'user' => $user,
              'confirmationUrl' => $url,
            ];
            $sendFrom = [$this->getParameter('mailer_user') => $this->getParameter('site')];
            $template = '@FOSUser/Registration/registrationEmail.html.twig';
            $this->mailerService->sendMessage($template, $context,$sendFrom, (string) $user->getEmail() );
            $user->setPasswordRequestedAt($now);
        }

        $user->setAccepted(true);
        $OneYear = $now->setDate(
          $now->modify('+1 year')->format('Y'),
          '01',
          '01'
        );
        $user->setUptodate($OneYear);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute(
          'easyadmin',
          [
            'action' => 'list',
            'entity' => $request->query->get('entity'),
          ]
        );
    }
}
