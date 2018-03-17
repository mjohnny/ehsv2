<?php
/**
 * Created by PhpStorm.
 * User: macej
 * Date: 25/02/2018
 * Time: 20:19
 */

namespace AppBundle\Controller;

use AppBundle\Services\EhsNewsletterService;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class RegistrationController
 *
 * @package AppBundle\Controller
 */
class RegistrationController extends BaseController
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var FactoryInterface
     */
    private $formFactory;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var \AppBundle\Services\EhsNewsletterService
     */
    private $newsletterService;

    /**
     * RegistrationController constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \FOS\UserBundle\Model\UserManagerInterface $userManager
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \AppBundle\Services\EhsNewsletterService $newsletterService
     */
    public function __construct(
      EventDispatcherInterface $eventDispatcher,
      UserManagerInterface $userManager,
      TokenStorageInterface $tokenStorage,
      ContainerInterface $container,
      EhsNewsletterService $newsletterService
    ) {
        $formFactory = $container->get('fos_user.registration.form.factory');
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->newsletterService = $newsletterService;
        parent::__construct(
          $eventDispatcher,
          $formFactory,
          $userManager,
          $tokenStorage
        );
    }


    public function registerAction(Request $request)
    {
        /** @var UserInterface $user */
        $user = $this->userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(
          FOSUserEvents::REGISTRATION_INITIALIZE,
          $event
        );

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user->setUsername($user->getEmail());
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $this->eventDispatcher->dispatch(
                  FOSUserEvents::REGISTRATION_SUCCESS,
                  $event
                );

                $password = new \DateTime();
                $user->setPlainPassword($password->getTimestamp());

                $this->userManager->updateUser($user);

                if ($user->isNewsletter()) {
                    $this->newsletterService->addNewReceiver(
                      $user->getEmail(),
                      true
                    );
                }

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl(
                      'fos_user_registration_confirmed'
                    );
                    $response = new RedirectResponse($url);
                }

                $this->eventDispatcher->dispatch(
                  FOSUserEvents::REGISTRATION_COMPLETED,
                  new FilterUserResponseEvent($user, $request, $response)
                );

                return $response;
            }

            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(
              FOSUserEvents::REGISTRATION_FAILURE,
              $event
            );

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render(
          '@FOSUser/Registration/register.html.twig',
          [
            'form' => $form->createView(),
          ]
        );
    }

}