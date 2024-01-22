<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use Symfony\Contracts\Cache\CacheInterface;

#[AsCommand('app:step:info')]
class StepInfoCommand extends Command
{
    public function __construct(
        private CacheInterface $cache,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $step = $this->cache->get('time_in_cache', function ($item) {
            $item->expiresAfter(60);

            return printf('time_in_cache: %s', Carbon::now('Europe/Moscow'));
        });
        $output->writeln($step);

        return Command::SUCCESS;
    }
}