<?php

namespace App\Entity;

use Doctrine\ORM\PersistentCollection;

trait CloneableEntityTrait
{
    public function __clone()
    {
        if (!$this->id) {
            return;
        }
        $vars = get_object_vars($this);
        foreach($vars as $name => &$value) {
            if (is_object($value)) {
                if ($value instanceof PersistentCollection) {
                    // avoid database querying
                    $value->setInitialized(true);
                }
                $this->$name = clone $value;
            }
        }
    }
}
