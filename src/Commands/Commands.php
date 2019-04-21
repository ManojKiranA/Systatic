<?php

namespace Damcclean\Systatic\Commands;

use Symfony\Component\Console\Application;
use Damcclean\Systatic\Commands\BuildCommand;
use Damcclean\Systatic\Commands\ClearSiteCommand;

class Commands
{
    public function console()
    {
        $application = new Application('Systatic');

        $application->add(new BuildCommand());
        $application->add(new ClearSiteCommand());

        $application->run();
    }
}
