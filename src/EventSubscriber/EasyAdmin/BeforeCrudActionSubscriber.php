<?php

namespace App\EventSubscriber\EasyAdmin;

use App\Entity\Section;
use App\Repository\SectionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BeforeCrudActionSubscriber implements EventSubscriberInterface
{
    private $sectionRepository;

    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeCrudActionEvent::class => ['setCorrectEntity']
        ];
    }

    public function setCorrectEntity(BeforeCrudActionEvent $event)
    {
//        $context = $event->getAdminContext();
//        if (!$context) return;
//
//        $entityDto = $context->getEntity();
//        if (!$entityDto) return;
//
//        $entity = $entityDto->getInstance();
//
//        if (!($entity instanceof Section)) {
//            return;
//        }
//
//        $lang = $event->getAdminContext()->getRequest()->attributes->get('lang', 'en');
//
//        $section = $this->sectionRepository->findForAdmin($entity->getId(), $lang);
//
//        $event->getAdminContext()->getEntity()->setInstance($section);
    }
}
