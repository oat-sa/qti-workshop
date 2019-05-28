<?php declare(strict_types=1);

namespace App\Service\ItemSessionRepository;

use League\Flysystem\FilesystemInterface;

class ItemSessionRepository
{
    /**
     * @var FilesystemInterface
     */
    private $storage;

    /**
     * ItemSessionRepository constructor.
     * @param FilesystemInterface $defaultStorage
     */
    public function __construct(FilesystemInterface $defaultStorage)
    {
        $this->storage = $defaultStorage;
    }

    /**
     * @param string $id
     * @param array $content
     */
    public function store(string $id, array $content)
    {
        $this->storage->put("itemSessions/${id}", json_encode($content));
    }

    /**
     * @param $id
     * @return mixed
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function get($id)
    {
        $data = json_decode($this->storage->read("itemSessions/${id}"), true);

        return $data;
    }

    /**
     * @param $id
     * @return bool
     */
    public function has($id)
    {
        return $this->storage->has("itemSessions/${id}");
    }

    /**
     *
     */
    public function flush()
    {
        $this->storage->deleteDir('itemSessions');
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->storage->listContents('itemSessions'));
    }
}