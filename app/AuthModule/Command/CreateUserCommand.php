<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14/07/2019
 * Time: 20:50
 */

namespace App\AuthModule\Command;

use App\AuthModule\Entity\User;
use App\AuthModule\Provider\DatabaseUserProvider;
use Simplex\Helper\Str;
use Simplex\Validation\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends Command
{

    /**
     * @var DatabaseUserProvider
     */
    private $provider;
    /**
     * @var Validator
     */
    private $validator;

    public function __construct(DatabaseUserProvider $provider, Validator $validator)
    {
        parent::__construct('auth:user-create');
        $this->provider = $provider;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([
            new InputArgument('username', InputArgument::REQUIRED, 'User username'),
            new InputArgument('email', InputArgument::REQUIRED, 'User email'),
            new InputArgument('password', InputArgument::REQUIRED, 'User password'),
        ])
            ->setDescription('Create a new admin user');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = new SymfonyQuestionHelper();
        $username = $helper->ask($input, $output, (new Question('Username'))
            ->setValidator(function (string $username) {
                $validation = $this->validator
                    ->validate(compact('username'), ['username' => 'alpha_num']);
                return $validation->passes()
                    ? $validation->getValidData()['username']
                    : null;
            }));

        $email = $helper->ask($input, $output, (new Question('Email'))
            ->setValidator(function (string $email) {
                $validation = $this->validator
                    ->validate(compact('email'), ['email' => 'email']);
                return $validation->passes()
                    ? $validation->getValidData()['email']
                    : null;
            }));

        $password = $helper->ask($input, $output, (new Question('Password'))
            ->setHidden(true)
            ->setValidator(function (string $password) {
                $validation = $this->validator
                    ->validate(compact('password'), ['password' => 'min:8']);
                return $validation->passes()
                    ? $validation->getValidData()['password']
                    : null;
            }));

        $helper->ask($input, $output, (new Question('Confirm password'))
            ->setHidden(true)
            ->setValidator(function (string $confirm) use ($password) {
                return $password === $confirm ? true : null;
            }));

        $input->setArgument('username', $username);
        $input->setArgument('email', $email);
        $input->setArgument('password', password_hash($password, PASSWORD_BCRYPT));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = Str::random(28);
        $user = new User($name = $input->getArgument('username'), $token, $input->getArgument('password'));
        $user->setEmail($input->getArgument('email'));
        $output->writeln("Creating user '$name'");
        $this->provider->insert($user);
        $output->writeln("User '$name' successfully created");
    }
}
