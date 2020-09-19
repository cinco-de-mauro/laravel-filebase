<?php


namespace LaravelFilebase\Filebase;

use Filebase\Config as BaseConfig;

class Config extends BaseConfig
{
    protected $subDir = null;

    public function appendDir($dir)
    {
        if ($this->subDir) {
            $this->dir = rtrim($this->dir, '/' . $this->subDir);
        }

        $this->dir .= '/' . $dir;

        $this->subDir = $dir;
    }
}