<?php


namespace App\Repository\JoinCondition;

use App\Repository\JoinConditionParams\ArticleJoinConditionParams;
use Doctrine\ORM\Query\Expr;

class ArticleJoinCondition
{
    /**
     * @param bool|ArticleJoinConditionParams|null $conditionParams
     * @param string $alias
     * @return string|null
     */
    public function getCondition($conditionParams, string $alias): ?string
    {
        if (!($conditionParams instanceof ArticleJoinConditionParams)) {
            return null;
        }

        $conditions = [];
        if ($conditionParams->findExceptSlugs) {
            $conditions[] = (new Expr)->notIn("$alias.slug", $conditionParams->findExceptSlugs);
        }

        if ($conditionParams->conjunction === 'AND') {
            return (string) call_user_func_array([new Expr, 'andX'], $conditions);
        }
        if ($conditionParams->conjunction === 'OR') {
            return (string) call_user_func_array([new Expr, 'orX'], $conditions);
        }
    }
}
