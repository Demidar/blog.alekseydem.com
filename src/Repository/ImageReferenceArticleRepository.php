<?php

namespace App\Repository;

use App\Entity\ImageReferenceArticle;
use App\Repository\Filter\ImageReferenceFilter;
use App\Repository\Modifier\ImageReferenceQueryModifier;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageReferenceArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageReferenceArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageReferenceArticle[]    findAll()
 * @method ImageReferenceArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method ImageReferenceArticle|null findImageReferenceById(int $id, ?ImageReferenceFilter $filter = null, ?ImageReferenceQueryModifier $modifier = null)
 * @method ImageReferenceArticle[]    findImageReferences(?ImageReferenceFilter $filter = null, ?ImageReferenceQueryModifier $modifier = null)
 */
class ImageReferenceArticleRepository extends ImageReferenceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageReferenceArticle::class);
    }
}
