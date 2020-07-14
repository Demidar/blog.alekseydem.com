<?php

namespace App\Admin\Service\Menu;

use App\Model\Link;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MenuBuilder
{
    public const TRANSLATABLE = 'translatable';

    /**
     * @var Link[]
     */
    private $links;
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function add($entityFqcn, $title): self
    {
        $link = new Link();
        $link->title = $title;
        $link->url = $this->urlGenerator->generate('admin_entity_index', ['entity' => $entityFqcn]);

        $this->links[] = $link;

        return $this;
    }

    public function getLinks()
    {

    }
}
