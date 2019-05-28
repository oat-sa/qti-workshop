<?php declare(strict_types=1);

namespace App\Service\ItemRepository;

use League\Flysystem\FilesystemInterface;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\XmlStorageException;

class ItemRepository
{
    /**
     * @var FilesystemInterface
     */
    private $storage;

    /**
     * ItemRepository constructor.
     * @param FilesystemInterface $defaultStorage
     */
    public function __construct(FilesystemInterface $defaultStorage)
    {
        $this->storage = $defaultStorage;
    }

    /**
     * @param string $id
     * @param string $content
     * @throws ItemRepositoryException
     */
    public function store(string $id, string $content)
    {
        $doc = new XmlDocument();

        try {
            $doc->loadFromString($content, true);
            $this->storage->put("items/${id}", $content);

        } catch (XmlStorageException $e) {
            throw new ItemRepositoryException("Unable to store QTI content with id '${id}'.");
        }
    }

    /**
     * @param $id
     * @throws ItemRepositoryException
     * @throws XmlStorageException
     * @throws \League\Flysystem\FileNotFoundException
     * @return XmlDocument
     */
    public function get($id)
    {
        if ($this->storage->has("items/${id}")) {
            $data = $this->storage->read("items/${id}");
            $doc = new XmlDocument();
            $doc->loadFromString($data);

            return $doc;
        } else {
            throw new ItemRepositoryException("Unable to retrieve QTI content with id '${id}'.");
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function has($id)
    {
        return $this->storage->has("items/${id}");
    }

    /**
     *
     */
    public function flush()
    {
        $this->storage->deleteDir('items');
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->storage->listContents('items'));
    }
}