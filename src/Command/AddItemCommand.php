<?php declare(strict_types=1);

namespace App\Command;


use App\Service\ItemRepository\ItemRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddItemCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:item-repo-add';

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * AddItemCommand constructor.
     * @param ItemRepository $itemRepository
     */
    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;

        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('Add an item to the repository.');
        $this->setHelp('This command allows you to add an item to the repository.');

        $this->addArgument('identifier', InputArgument::REQUIRED, "The identifier that the Item will be given in the repository.");
        $this->addArgument('file', InputArgument::REQUIRED, "The path to the file containing the Item content.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \App\Service\ItemRepository\ItemRepositoryException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('identifier');
        $file = $input->getArgument('file');

        // TODO Validate QTI contents
        $output->writeln("Processing file '${file}' to be ingested as '${id}'...");

        $this->itemRepository->store($id, file_get_contents($file));
    }
}