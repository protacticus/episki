<?php

/*
 * This file is part of episki core.
 *
 * (c) Justin Leapline <justin@episki.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Defines the method that 'listens' to the 'kernel.controller' event, which is
 * triggered whenever a controller is executed in the application.
 *
 * @author Justin Leapline <justin@episki.org>
 * @author Justin Leapline <justin@episki.org>
 */
class ControllerSubscriber implements EventSubscriberInterface
{
    private $twigExtension;

    public function __construct()
    {
        
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'registerCurrentController',
        ];
    }

    public function registerCurrentController(FilterControllerEvent $event)
    {
        
    }
}
