<?php

namespace Wqer1019\AutoUpdate;

class ExcludeResource
{
    private $files = [];

    private $directories = [];

    private $extensions = [];

    public function __construct($config)
    {
        $this->files = $config['files'];

        $this->directories = $config['directories'];

        $this->extensions = $config['extensions'];
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function getFiles()
    {
        return $this->files;
    }
}
