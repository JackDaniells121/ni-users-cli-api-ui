<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\UserManager;
use App\Utils\Validator;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user-activate',
    description: 'Add a short description for your command',
)]
class UserActivateCommand extends Command
{
    private SymfonyStyle $io;
    private integer $userId;
    private User $user;

    public function __construct(
        private Validator $validator,
        private UserManager $userManager,
        private UserRepository $users,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getCommandHelp())
            ->addArgument('search', InputArgument::OPTIONAL, 'Search variable (can be email or surname and name or pesel)')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = false;
        $io = new SymfonyStyle($input, $output);

        $search = $input->getArgument('search');

        if (null === $search) {
            $user = $this->askForUserIdentifier($input, $output);
        }
        else {
            $user = $this->users->findOneBySearchField($search);

            if (!$user) {
                $user = $this->askForUserIdentifier($input, $output);
            }
        }

        if ($user) {
            $this->io->text(' > <info>Found user </info>: '.$user->getId().' '.$user->getName().' '.$user->getSurname().' '.$user->getEmail().' '.$user->getActivatedLabel());

            $question = new ConfirmationQuestion('Activate this user (y/n)?: ', false, '/^(y|j)/i');
            $helper = $this->getHelper('question');

            if ($helper->ask($input, $output, $question)) {

                if ($user->isActivated()) {
                    $output->writeln('<fg=blue>This user has been already active</>');
                }
                else {
                    $this->userManager->activateUser($user);
                    $io->success('User has been activated');
                }

                return Command::SUCCESS;
            }
        }

        $output->writeln('<fg=red>Terminated by You</>');
        return Command::FAILURE;
    }

    private function askForUserIdentifier(InputInterface $input, OutputInterface $output): User | bool
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $question = new Question('Please enter user pesel or email: ');
        $exit = false;

        for ($i = 5; $i > 0 ; $i--) {

            $search = $helper->ask($input, $output, $question);

            if ($search == 'exit') {
                return false;
            }

            if ($search) {
                $user = $this->users->findOneBySearchField($search);

                if ($user) {
                    return $user;
                }
                else {
                    $output->writeln('<fg=yellow>Not found</>');
                }
            }
        }

        return false;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command activates user that can be identified by email or name surname or pesel:

              <info>php %command.full_name%</info> <comment>email</comment>
              <info>php %command.full_name%</info> <comment>name surname</comment>
              <info>php %command.full_name%</info> <comment>pesel</comment>
              
            Command will search for requested user and will ask to approve activation.
            For exit type exit.
            HELP;
    }
}
