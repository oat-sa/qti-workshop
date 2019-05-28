<?php declare(strict_types=1);


namespace App\Tests\Integration\Service\ItemRepository;

use App\Service\ItemRepository\ItemRepository;
use qtism\data\storage\xml\XmlDocument;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ItemRepositoryTest extends KernelTestCase
{
    const MINIMAL_CONTENT = '<responseProcessing xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1  http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd" template="xxx"/>';

    /** @var ItemRepository */
    private $itemRepository;

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();

        $this->itemRepository = static::$container->get(ItemRepository::class);
        $this->itemRepository->flush();
    }

    public function testStore()
    {
        $this->itemRepository->store('myId', self::MINIMAL_CONTENT);
        $this->assertTrue(true, 'Should not have thrown an exception.');
    }

    /**
     * @depends testStore
     */
    public function testHas()
    {
        $this->assertFalse($this->itemRepository->has('myId'));
        $this->itemRepository->store('myId', self::MINIMAL_CONTENT);
    }

    /**
     * @depends testStore
     */
    public function testGet()
    {
        $this->itemRepository->store('myId', self::MINIMAL_CONTENT);
        $doc = $this->itemRepository->get('myId');

        $this->assertInstanceOf(XmlDocument::class, $doc);
    }

    /**
     * @depends testStore
     */
    public function testCount()
    {
        $this->assertSame(0, $this->itemRepository->count());
        $this->itemRepository->store('countId', self::MINIMAL_CONTENT);
        $this->assertSame(1, $this->itemRepository->count());
    }
}