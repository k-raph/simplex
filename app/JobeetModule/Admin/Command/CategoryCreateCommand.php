<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/07/2019
 * Time: 18:55
 */

namespace App\JobeetModule\Admin\Command;


use App\JobeetModule\Entity\Category;
use Simplex\DataMapper\EntityManager;
use Simplex\Helper\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CategoryCreateCommand extends Command
{

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * CategoryCreateCommand constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        parent::__construct('jobeet:category:create');
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'The category name'),
            new InputArgument('slug', InputArgument::OPTIONAL, 'The category slug')
        ])
            ->setDescription('Create a new category');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = new QuestionHelper();
        $default = Str::slugify($input->getArgument('name'));
        $slug = $helper->ask($input, $output, new Question("Please enter slug [$default] : ", $default));
        $input->setArgument('slug', $slug);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $category = new Category($input->getArgument('name'));
        $category->setSlug($input->getArgument('slug'));

        $this->manager->persist($category);
        $this->manager->flush();

        $output->writeln("Category {$category->getName()}:{$category->getSlug()} successfully created");
    }
}