<?php

namespace App\Controller;

use App\Form\LanguageSwitcherFormType;
use App\Repository\ArticleRepository;
use App\Repository\Modifier\ArticleQueryModifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class IndexController extends AbstractController
{
    private $articleRepository;

    public function __construct(
        ArticleRepository $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/", name="homepage")
     *
     * @return Response
     */
    public function index(): Response
    {
        $articles = $this->articleRepository->findArticles(new ArticleQueryModifier([
            'withImages' => true,
            'withSection' => true,
            'orderDirection' => 'DESC',
            'fallback' => 1,
            'limit' => 10
        ]));
        return $this->render('index/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/switch-language", name="language-switcher")
     */
    public function switchLanguage(Request $request, RouterInterface $router)
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

        $refRequest = Request::create($referer);
        try {
            $context = (new RequestContext())->fromRequest($refRequest);
            $router->setContext($context);
            $routeParameters = $router->match($refRequest->getPathInfo());
        } catch (ResourceNotFoundException | MethodNotAllowedException $exception) {
            return new RedirectResponse($this->generateUrl('homepage', ['_locale' => $desiredLang]));
        }

        $routeParameters['_locale'] = $desiredLang;
        $route = $routeParameters['_route'];
        unset($routeParameters['_route']);

        $routeParameters = array_merge($routeParameters, $refRequest->query->all());

        return new RedirectResponse($this->generateUrl($route, $routeParameters));
    }
}
