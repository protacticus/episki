<?php

/*
 * This file is part of episki core.
 *
 * (c) Justin Leapline <justin@episki.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Entity\Controls;
use App\Entity\Authority;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Luchaninov\CsvFileLoader\CsvFileLoader;

/**
 * A console command that creates users and stores them in the database.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console episki:add-user
 *
 * To output detailed information, increase the command verbosity:
 *
 *     $ php bin/console episki:add-user -vv
 *
 * See https://symfony.com/doc/current/cookbook/console/console_command.html
 * For more advanced uses, commands can be defined as services too. See
 * https://symfony.com/doc/current/console/commands_as_services.html
 *
 * @author Justin Leapline <justin@episki.org>
 */
class AddControlsCommand extends Command
{
    const MAX_ATTEMPTS = 5;

    private $io;
    private $entityManager;
    private $passwordEncoder;
    private $validator;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Validator $validator)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            // a good practice is to use the 'app:' prefix to group all your custom application commands
            ->setName('episki:add-controls')
            ->setDescription('Mass upload controls via CSV file')
            ->setHelp($this->getCommandHelp())
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
            ->addArgument('filename', InputArgument::OPTIONAL, 'The CSV file of the controls')
        ;
    }

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('filename')) {
            return;
        }

        $this->io->title('Add Controls Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:add-controls filename',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the filename if it's not defined
        $filename = $input->getArgument('filename');
        if (null !== $filename) {
            $this->io->text(' > <info>Filename</info>: '.$filename);
        } else {
            $filename = $this->io->ask('Filename', null, [$this->validator, 'validateFile']);
            $input->setArgument('filename', $filename);
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-controls-command');

        $filename = $input->getArgument('filename');
        
        if (! file_exists($filename)) {
	        throw new \RuntimeException(sprintf('File does not exist: "%s"', $filename));
	    }
        
        // $controls = file_get_contents( $filename, FILE_USE_INCLUDE_PATH);
        
        //$controls = array_map('str_getcsv', file($filename));
        
        //var_dump($controls); exit;
        
        $loader = new CsvFileLoader($filename);

		foreach ($loader->getItems() as $item) {
			// create the control			
	        $control = new Controls();
	        $control->setAuthorityref($this->lookupAuthority($item['authsource']));
	        $control->setNumber($item['ref']);
	        $control->setRequirement($item['control']);
	        $control->setDescription($item['description']);
	        
	        $this->entityManager->persist($control);
	        $this->entityManager->flush();
		}

        $this->io->success(sprintf('The controls were successfully imported.'));

        $event = $stopwatch->stop('add-controls-command');
        if ($output->isVerbose()) {
            $this->io->comment(sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $user->getId(), $event->getDuration(), $event->getMemory() / pow(1024, 2)));
        }
    }
    
    /**
     * This method is a helper for looking up the identifier of the authority source.
     */
    private function lookupAuthority($title)
    {
        $repository = $this->entityManager->getRepository(Authority::class);
                
        /** @var Authority $auth */
        $auth = $repository->findOneBy(['title' => $title]);
        
        if (null === $auth) {
            throw new \RuntimeException(sprintf('There is no authority with the title "%s".', $title));
        }
        
		return $auth;
    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp()
    {
        return <<<'HELP'
The <info>%command.name%</info> command creates new users and saves them in the database:

  <info>php %command.full_name%</info> <comment>username password email</comment>

By default the command creates regular users. To create administrator users,
add the <comment>--admin</comment> option:

  <info>php %command.full_name%</info> username password email <comment>--admin</comment>

If you omit any of the three required arguments, the command will ask you to
provide the missing values:

  # command will ask you for the email
  <info>php %command.full_name%</info> <comment>username password</comment>

  # command will ask you for the email and password
  <info>php %command.full_name%</info> <comment>username</comment>

  # command will ask you for all arguments
  <info>php %command.full_name%</info>

HELP;
    }
}
