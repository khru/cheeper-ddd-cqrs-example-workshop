<?php

namespace App\Command;

use Cheeper\Application\Command\Author\SignUp;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//snippet signup-command
final class SignupCommand extends Command
{
    protected static $defaultName = 'app:signup';

    protected function configure(): void
    {
        $this
            ->setDescription('Signs up an author')
            ->addArgument('username', InputArgument::REQUIRED, 'Author\'s username')
            ->addArgument('name', InputArgument::REQUIRED, 'Author\'s name')
            ->addArgument('biography', InputArgument::REQUIRED, 'Author\'s biography')
            ->addArgument('location', InputArgument::REQUIRED, 'Author\'s location')
            ->addArgument('website', InputArgument::REQUIRED, 'Author\'s website')
            ->addArgument('birthdate', InputArgument::REQUIRED, 'Author\'s birthdate')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $name = $input->getArgument('name');
        $biography = $input->getArgument('biography');
        $location = $input->getArgument('location');
        $website = $input->getArgument('website');
        $birthdate = $input->getArgument('birthdate');

        if (!is_string($username) || !is_string($name) || !is_string($biography) || !is_string($location) || !is_string($website) || !is_string($birthdate)) {
            return 2;
        }

        $command = new SignUp(
            Uuid::uuid4()->toString(),
            $username,
            $name,
            $biography,
            $location,
            $website,
            $birthdate,
        );

        //ignore
        dump($command);
        //end-ignore

        return 0;
    }
}
//end-snippet
