<?php


namespace LaravelFilebase\Filebase;

use Filebase\Database;
use Filebase\Filesystem\FilesystemException;

class Client extends Database
{
    public function __construct(array $config = [])
    {
        $this->config = new Config($config);

        // if we are set to read only, don't care to look at the directory.
        if ($this->config->read_only === true) {
            return false;
        }

        // Check directory and create it if it doesn't exist
        if (!is_dir($this->config->dir)) {
            if (!@mkdir($this->config->dir, 0777, true)) {
                throw new FilesystemException(
                    sprintf('`%s` doesn\'t exist and can\'t be created.', $this->config->dir)
                );
            }
        } else {
            if (!is_writable($this->config->dir)) {
                throw new FilesystemException(sprintf('`%s` is not writable.', $this->config->dir));
            }
        }
    }

    public function table($table)
    {
        $this->config->appendDir($table);
    }
}