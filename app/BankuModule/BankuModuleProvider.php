<?php

namespace App\BankuModule;

use App\BankuModule\DataSource\Mapper\AccountMapper;
use App\BankuModule\DataSource\Mapper\BranchMapper;
use App\BankuModule\DataSource\Mapper\CustomerMapper;
use App\BankuModule\DataSource\Mapper\EmployeeMapper;
use App\BankuModule\DataSource\Mapper\TransactionMapper;
use App\BankuModule\Entity\Account;
use App\BankuModule\Entity\Branch;
use App\BankuModule\Entity\Customer;
use App\BankuModule\Entity\Employee;
use App\BankuModule\Entity\Transaction;
use Simplex\Configuration\Configuration;
use Simplex\Module\AbstractModule;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouteCollection;

class BankuModuleProvider extends AbstractModule
{

    /**
     * @var string
     */
    private $host;

    public function configure(Configuration $configuration)
    {
        $this->host = $configuration->get('app_host', 'localhost');
    }

    /**
     * @param RouteCollection $collection
     */
    public function getSiteRoutes(RouteCollection $collection): void
    {
        $collection->import(__DIR__ . '/resources/routes.yml', [
            'host' => 'banku.' . $this->host,
            'name_prefix' => 'banku_'
        ]);
    }

    /**
     * @param TwigRenderer $renderer
     * @throws \Twig_Error_Loader
     */
    public function registerTemplates(TwigRenderer $renderer)
    {
        $renderer->addPath(__DIR__ . '/views', 'banku');
    }

    public function getMappings(): array
    {
        return [
            'connection' => 'banku',
            'mappings' => [
                Branch::class => BranchMapper::class,
                Employee::class => EmployeeMapper::class,
                Customer::class => CustomerMapper::class,
                Account::class => AccountMapper::class,
                Transaction::class => TransactionMapper::class
            ]
        ];
    }

    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string
    {
        return 'banku';
    }

    /**
     * @return string|null
     */
    public function getMigrationsConfig(): ?string
    {
        return __DIR__ . '/resources/db/phinx.yml';
    }
}
