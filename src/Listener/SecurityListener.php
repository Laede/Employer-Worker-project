<?php

namespace App\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    private $router;
    private $dispatcher;
    private $checker;

    public function __construct(RouterInterface $router, EventDispatcherInterface $dispatcher, AuthorizationCheckerInterface $checker)
    {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->checker = $checker;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if($this->checker->isGranted('ROLE_WORKER'))
        {
            $response = new RedirectResponse($this->router->generate('worker_projects'));
        } elseif ($this->checker->isGranted('ROLE_EMPLOYER'))
        {
            $response  = new RedirectResponse($this->router->generate('project_index'));
        }
        $event->setResponse($response);
    }
}