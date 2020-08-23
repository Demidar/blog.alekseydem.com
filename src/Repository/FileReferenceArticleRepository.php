<?php

namespace App\Repository;

use App\Entity\FileReferenceArticle;
use App\Repository\Filter\FileReferenceFilter;
use App\Repository\Modifier\FileReferenceQueryModifier;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FileReferenceArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileReferenceArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileReferenceArticle[]    findAll()
 * @method FileReferenceArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method FileReferenceArticle|null findFileReferenceById(int $id, ?FileReferenceFilter $filter = null, ?FileReferenceQueryModifier $modifier = null)
 * @method FileReferenceArticle[]    findFileReferences(?FileReferenceFilter $filter = null, ?FileReferenceQueryModifier $modifier = null)
 */
class FileReferenceArticleRepository extends FileReferenceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileReferenceArticle::class);
    }
}
