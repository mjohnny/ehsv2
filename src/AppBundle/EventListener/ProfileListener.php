<?php
/**
 * Created by PhpStorm.
 * User: macej
 * Date: 17/03/2018
 * Time: 15:12
 */

namespace AppBundle\EventListener;


use AppBundle\Services\EhsNewsletterService;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProfileListener implements EventSubscriberInterface
{

    /**
     * @var \AppBundle\Services\EhsNewsletterService
     */
    private $newsletterService;

    /**
     * ProfileListener constructor.
     *
     * @param \AppBundle\Services\EhsNewsletterService $newsletterService
     */
    public function __construct(EhsNewsletterService $newsletterService)
    {
        $this->newsletterService = $newsletterService;
    }


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onEditProfileSuccess'];
    }

    public function onEditProfileSuccess(FormEvent $event)
    {
        $userEmail = $event->getForm()->getData()->getEmail();
        if ($event->getForm()->getData()->isNewsletter()) {
            $this->newsletterService->addNewReceiver($userEmail, true);
        }
        else {
            $this->newsletterService->removeReceiver($userEmail);
        }
    }

}