<?php declare(strict_types=1);

namespace App\Command;


use App\Service\ItemRepository\ItemRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearRepoCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:item-repo-clear';

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * ClearRepoCommand constructor.
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
        $this->setDescription('Clear the item repository.');
        $this->setHelp('This command allows you clear the item repository.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Clearing item repository...");
        $this->itemRepository->flush();
        $output->writeLn("Item repository clean!");
    }
}