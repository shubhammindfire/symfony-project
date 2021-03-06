<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
// use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordHashSubscriber implements EventSubscriberInterface
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE]
        ];
    }

    // public function hashPassword(GetResponseForControllerResultEvent $event)
    // GetResponseForControllerResultEvent was renamed to ViewEvent Symfony 4.3
    public function hashPassword(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        // $method = $event->getRequests()->getMethod();
        // getRequests() method was renamed to getRequest()
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || Request::METHOD_POST !== $method) {
            return;
        }

        // It is a User and method is also valid
        // Hash the password
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
    }
}
