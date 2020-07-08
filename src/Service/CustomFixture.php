<?php

namespace App\Service;

use Doctrine\Bundle\FixturesBundle\Fixture;

abstract class CustomFixture extends Fixture
{
    protected function getReferencesByPrefix(string $prefixBeforeColon): array
    {
        $references = $this->referenceRepository->getReferences();

        $resultReferences = [];

        foreach ($references as $referenceName => $referenceValue) {
            if (substr($referenceName, 0, strpos($referenceName, ':')) === $prefixBeforeColon) {
                $resultReferences[$referenceName] = $referenceValue;
            }
        }

        return $resultReferences;
    }
}
