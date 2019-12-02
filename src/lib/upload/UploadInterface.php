<?php

namespace pwp\lib\upload;

interface UploadInterface
{
    /**
     * Class constructor.
     */
    public function __construct($config);

    public function save($file);

    public function getError();
}