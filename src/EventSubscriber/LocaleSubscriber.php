<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $supportedLocales;
    private $router;

    public function __construct(string $bound_supportedLocales, RouterInterface $router)
    {
        $supportedLocalesRegex = $bound_supportedLocales;
        $this->supportedLocales = explode('|', $supportedLocalesRegex);
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

//        if (!$request->hasPreviousSession()) {
//            return;
//        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->setLocale($locale);
        } else {
            $request->setLocale(
                $request->getSession()->get(
                    '_locale',
                    $request->getPreferredLanguage($this->supportedLocales) ?: $request->getDefaultLocale()
                )
            );

            $this->redirectIfLocaleInPathIsNotSpecified($request, $event);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]]
        ];
    }

    private function redirectIfLocaleInPathIsNotSpecified(Request $request, RequestEvent $event)
    {
        $path = $request->getPathInfo();

        try {
            $this->router->match($path);
        } catch (ResourceNotFoundException $prevException) {
            try {
                $localedPath = '/'.$request->getLocale().$path;
                if ($this->router->match($localedPath)) {
                    $event->setResponse(new RedirectResponse($localedPath));
                }
            } catch (ResourceNotFoundException $exception) {
                throw new ResourceNotFoundException(
                    $prevException->getMessage() . PHP_EOL .
                    $exception->getMessage() . PHP_EOL . PHP_EOL .
                    'At least one of these routes should exists.'
                );
            }
        }
    }
}
