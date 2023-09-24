<?php

namespace Shadow;

abstract class AbstoractJsonStorageObject extends \stdClass
{
    public function __construct()
    {
        (new JsonStorage)
            ->init($this)
            ->copyPropertiesToObject();
    }
}
