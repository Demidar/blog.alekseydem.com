<?php

namespace App\Repository;

use App\Entity\FileReferenceArticle;
use App\Repository\ModifierParams\FileReferenceQueryModifierParams;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FileReferenceArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileReferenceArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileReferenceArticle[]    findAll()
 * @method FileReferenceArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method FileReferenceArticle|null findFileReferenceById(int $id, ?FileReferenceQueryModifierParams $modifier = null)
 * @method FileReferenceArticle[]    findFileReferences(?FileReferenceQueryModifierParams $modifier = null)
 */
class FileReferenceArticleRepository extends FileReferenceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileReferenceArticle::class);
    }
}
