<?php

namespace App\Entity;

use Doctrine\ORM\PersistentCollection;

trait CloneableEntityTrait
{
    /*
     * Seems to be working good...
     */
    public function __clone()
    {
        if (!$this->id) {
            return;
        }
        $this->id = null;
        $vars = get_object_vars($this);
        foreach($vars as $name => $value) {
            if (is_object($value)) {
                if ($value instanceof PersistentCollection) {
                    continue;
                }
                $this->$name = clone $this->$name;
            }
        }
    }
}
