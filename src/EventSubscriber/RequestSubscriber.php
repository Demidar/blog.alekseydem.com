<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    private $supportedLocales;
    private $router;
    private $em;
    private $authorizationChecker;
    private $security;

    public function __construct(
        string $bound_supportedLocales,
        RouterInterface $router,
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        Security $security
    ) {
        $supportedLocalesRegex = $bound_supportedLocales;
        $this->supportedLocales = explode('|', $supportedLocalesRegex);
        $this->router = $router;
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->security = $security;
    }

    public function determineLocale(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
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
            //if (in_array($request->getMethod(), [Request::METHOD_GET, Request::METHOD_HEAD], true)) {
                $this->redirectIfLocaleInPathIsNotSpecified($request, $event);
            //}
        }
    }

    public function settingDoctrineFilters(RequestEvent $event): void
    {
//        if (!$event->getRequest()->hasPreviousSession()) {
//            return;
//        }
        $filters = $this->em->getFilters();

        $user = $this->security->getUser();
        if ($user instanceof UserInterface) {
            $userId = $user->getId();
        } else {
            $userId = null;
        }

        if (!$user || !$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $filters->enable('section')->setParameter('status', 'visible');
            $filters->enable('article')->setParameter('status', 'published');
            $filters->enable('comment')->setParameter('status', 'visible');
            if ($userId) {
                $filters->getFilter('article')->setParameter('ownerId', $userId);
                $filters->getFilter('comment')->setParameter('ownerId', $userId);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                // must be registered before (i.e. with a higher priority than) the default Locale listener
                ['determineLocale', 50],
                ['settingDoctrineFilters']
            ]
        ];
    }

    private function redirectIfLocaleInPathIsNotSpecified(Request $request, RequestEvent $event): void
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
