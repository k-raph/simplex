<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/07/2019
 * Time: 18:46
 */

namespace App\JobeetModule\Admin\Command;

use App\JobeetModule\Admin\Repository\AffiliateRepository;
use App\JobeetModule\Entity\Affiliate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AffiliatesListCommand extends Command
{

    /**
     * @var AffiliateRepository
     */
    private $repository;

    /**
     * AffiliatesListCommand constructor.
     * @param AffiliateRepository $repository
     */
    public function __construct(AffiliateRepository $repository)
    {
        parent::__construct('jobeet:affiliates:list');
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('List all affiliates');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $affiliates = $this->repository->findAll();
        $affiliates = array_map(function (Affiliate $affiliate) {
            return [
                'id' => $affiliate->getId(),
                'name' => $affiliate->getName(),
                'email' => $affiliate->getEmail(),
                'status' => $affiliate->isActive() ? 'active' : 'inactive'
            ];
        }, $affiliates);

        $style = new SymfonyStyle($input, $output);
        $style->table(['Id', 'Name', 'Email', 'Status'], $affiliates);
    }
}
