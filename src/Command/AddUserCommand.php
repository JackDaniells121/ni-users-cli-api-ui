<?php

namespace App\Command;

use App\Utils\UserCreate;
use App\Utils\Validator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'app:add-user',
    description: 'Creates a new user.',
    aliases: ['app:new-user'],
    hidden: false
)]
class AddUserCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private Validator $validator,
        private UserCreate $userCreate
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getCommandHelp())
            ->addArgument('name', InputArgument::REQUIRED, 'User name')
            ->addArgument('surname', InputArgument::REQUIRED, 'User surname')
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('pesel', InputArgument::REQUIRED, 'User pesel')
            ->addArgument('skills', InputArgument::REQUIRED, 'Programming languages : c++,php,c#,js')
        ;
    }

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('name')
            && null !== $input->getArgument('surname')
            && null !== $input->getArgument('email')
            && null !== $input->getArgument('pesel')
            && null !== $input->getArgument('skills')) {
            return;
        }

        $this->io->title('Add User Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ app:add-user name surname email@example.com 12345678901 php,html,css',
            '',
            'EXIT = Ctrl + c + enter',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the arguments if they aren't defined
        $name = $input->getArgument('name');
        if (null !== $name) {
            $this->io->text(' > <info>Name</info>: '.$name);
        } else {
            $name = $this->io->ask('name', null, [$this->validator, 'validateName']);
            $input->setArgument('name', $name);
        }

        $surname = $input->getArgument('surname');
        if (null !== $surname) {
            $this->io->text(' > <info>Surame</info>: '.$surname);
        } else {
            $surname = $this->io->ask('surname', null, [$this->validator, 'validateName']);
            $input->setArgument('surname', $surname);
        }

        $email = $input->getArgument('email');
        if (null !== $email) {
            $this->io->text(' > <info>Email</info>: '.$email);
        } else {
            $email = $this->io->ask('Email', null, [$this->validator, 'validateEmail']);
            $input->setArgument('email', $email);
        }

        $pesel = $input->getArgument('pesel');
        if (null !== $pesel) {
            $this->io->text(' > <info>Pesel</info>: '.$pesel);
        } else {
            $pesel = $this->io->ask('pesel', null, null);
            $input->setArgument('pesel', $pesel);
        }

        $skills = $input->getArgument('skills');
        if (null !== $skills) {
            $this->io->text(' > <info>Języki programowania</info>: '.$skills);
        } else {
            $skills = $this->io->ask('skills', null, null);
            $input->setArgument('skills', $skills);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-user-command');

        $name = $input->getArgument('name');
        $surname = $input->getArgument('surname');
        $email = $input->getArgument('email');
        $pesel = $input->getArgument('pesel');
        $skills = $input->getArgument('skills');

        $user = $this->userCreate->saveData($name, $surname, $email, $pesel, $skills);

        $this->io->success(sprintf('%s was successfully created: %s (%s)',  'User', $user->getName(), $user->getEmail()));

        $event = $stopwatch->stop('add-user-command');
        if ($output->isVerbose()) {
            $this->io->comment(sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $user->getId(), $event->getDuration(), $event->getMemory() / (1024 ** 2)));
        }

        return Command::SUCCESS;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command creates new users and saves them in the database:

              <info>php %command.full_name%</info> <comment>name surname pesel skills</comment>
              
            If you omit any of the required arguments, the command will ask you to
            provide the missing values:

              # command will ask you for the pesel and skills
              <info>php %command.full_name%</info> <comment>name surname</comment>

              # command will ask you for the skills
              <info>php %command.full_name%</info> <comment>name surname pesel</comment>

              # command will ask you for all arguments
              <info>php %command.full_name%</info>
            HELP;
    }
}