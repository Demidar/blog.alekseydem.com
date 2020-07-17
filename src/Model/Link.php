<?php

namespace App\Model;

class Link
{
    public $title;
    public $url;

    public function __construct(
        $title = null,
        $url = null
    ) {
        $this->title = $title;
        $this->url = $url;
    }
}
