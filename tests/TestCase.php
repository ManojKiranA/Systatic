<?php

namespace Tests;

use Damcclean\Systatic\Cache\Cache;
use PHPUnit\Framework\TestCase as Base;
use Damcclean\Systatic\Filesystem\Filesystem;

define('BASE', './tests/fixtures');
define('CONFIGURATION', './tests/fixtures/config.php');

class TestCase extends Base
{
    public function setUp() : void
    {
        parent::setUp();

        $cache = new Cache();
        $cache->clearViewCache();
        $cache->clearSiteOutput();

        $filesystem = new Filesystem();
        $filesystem->delete('./tests/fixtures/config.php');
        $filesystem->copy('./tests/fixtures/real-config.php', './tests/fixtures/config.php');
        $filesystem->createFile('./tests/fixtures/storage/collections.json');
        $filesystem->createFile('./tests/fixtures/storage/plugins.json');
    }
}
