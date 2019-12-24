<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/07/2019
 * Time: 16:43
 */

namespace Simplex\Console\Command;

use DateTime;
use Keiryo\Queue\Event\JobFailedEvent;
use Keiryo\Queue\Event\JobStartingEvent;
use Keiryo\Queue\Event\JobSuccessEvent;
use Keiryo\Queue\Worker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WorkerRunCommand extends Command
{

    /**
     * @var Worker
     */
    private $worker;

    /**
     * WorkerRunCommand constructor.
     * @param Worker $worker
     */
    public function __construct(Worker $worker)
    {
        parent::__construct('queue:listen');
        $this->worker = $worker;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([
            new InputArgument('queue', InputArgument::OPTIONAL, 'The queue to listen to'),
            new InputOption('connection', 'c', InputOption::VALUE_OPTIONAL, 'The connection to use'),
            new InputOption('sleep', 's', InputOption::VALUE_OPTIONAL, 'Time to sleep when there is no job to process')
        ])
            ->setDescription('Listen to a single queue and run jobs');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $eventManager = $this->worker->getEventManager();
        $eventManager->on(JobStartingEvent::class, function (JobStartingEvent $event) use ($output) {
            $job = get_class($event->getJob());
            $id = $event->getJob()->getId();
            $now = (new DateTime())->format('m-d-Y H:i:s');
            $output->writeln("$now : Job $job '$id' started");
        });
        $eventManager->on(JobSuccessEvent::class, function (JobSuccessEvent $event) use ($output) {
            $job = get_class($event->getJob());
            $id = $event->getJob()->getId();
            $now = (new DateTime())->format('m-d-Y H:i:s');
            $output->writeln("$now : Job $job '$id' processed");
        });
        $eventManager->on(JobFailedEvent::class, function (JobFailedEvent $event) use ($output) {
            $job = get_class($event->getJob());
            $id = $event->getJob()->getId();
            $now = (new DateTime())->format('m-d-Y H:i:s');
            $output->writeln("$now : Job $job '$id' failed");
        });
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $input->getArgument('queue') ?? 'default';
        $connection = $input->getOption('connection') ?? null;
        $sleep = $input->getOption('sleep') ?? 5;
        $this->worker->setSleep($sleep);

        $this->worker->listen($queue, $connection);
    }
}
