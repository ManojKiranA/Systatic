<?php

namespace Damcclean\Systatic\Commands;

use Symfony\Component\Console\Command\Command as Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Damcclean\Systatic\Build\Build;

class BuildCommand extends Command
{
    protected static $defaultName = 'build';

    protected function configure()
    {
        $this
            ->setDescription('Build Systatic site')
            ->setHelp('This command builds your static site.');

        $this->build = new Build();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Message
        $output->writeln('Building site...');

        // Build the site
        $this->build->build();
    }
}
