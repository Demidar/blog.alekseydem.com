<?php

namespace App\Controller;

use App\Form\LanguageSwitcherFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index-with-no-locale")
     */
    public function indexWithNoLocale()
    {
        return new RedirectResponse($this->generateUrl('homepage'));
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/", name="homepage")
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/switch-language", name="language-switcher")
     */
    public function switchLanguage(Request $request, string $bound_supportedLocales, RouterInterface $router)
    {
        $form = $this->createForm(LanguageSwitcherFormType::class);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            throw new BadRequestException('Language is not valid');
        }

        $data = $form->getData();
        $desiredLang = $data['lang'];

        $request->getSession()->set('_locale', $desiredLang);

        $referer = $request->headers->get('referer');

        if (!$referer) {
            return new RedirectResponse($this->generateUrl('homepage', ['_locale' => $desiredLang]));
        }

        $refererPath = preg_replace('/(https?:\/\/.*?)(\/.*)/', '$2', $referer);
        $refererPathWithoutBase = str_replace($request->getBasePath(), '', $refererPath);

        try {
            $routeParameters = $router->match($refererPathWithoutBase);
        } catch (ResourceNotFoundException $exception) {
            return new RedirectResponse($this->generateUrl('homepage', ['_locale' => $desiredLang]));
        }

        $routeParameters['_locale'] = $desiredLang;
        $route = $routeParameters['_route'];
        unset($routeParameters['_route']);

        return new RedirectResponse($this->generateUrl($route, $routeParameters));
    }
}
