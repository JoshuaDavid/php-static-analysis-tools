<?php

namespace Stan\Commands;

use Stan\Services\PhpFileFinder;
use Stan\Services\CodebaseModel\Project;

use PhpParser\Error as ParseError;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FindUsagesCommand extends Command {
    protected static $defaultName = 'usages';

    public function __construct(
        $defaultName = 'usages',
        PhpFileFinder $finder,
        Project $project
    ) {
        parent::__construct($defaultName);
        $this->finder = $finder;
        $this->project = $project;
    }

    protected function configure() {
        $this->setDefinition(new InputDefinition([
            new InputArgument('path', InputArgument::REQUIRED),
        ]));
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $path = $input->getArgument('path');
        foreach ($this->finder->findPhpFiles($path) as $phpFile) {
            $this->project->addFileToModel($phpFile);
        }
        return 0;
    }
}

