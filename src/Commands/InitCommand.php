<?php

namespace Damcclean\Systatic\Commands;

use Illuminate\Console\Command;
use Damcclean\Systatic\Filesystem\Filesystem;

class InitCommand extends Command
{
    protected $signature = 'init';
    protected $description = 'Initialize Systatic';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle()
    {
        $filesystem = new Filesystem();
        $base = getcwd();

        $filesystem->copyDir($base . '/vendor/damcclean/systatic/stubs/site', $base);

        if($this->confirm('Do you want to copy the Laravel Valet driver?')) {
            $filesystem->copyDir($base . '/vendor/damcclean/systatic/stubs/valet', $base);
        }

        $this->info("All that's left now is for you to build your site! - php systatic build");
    }
}
