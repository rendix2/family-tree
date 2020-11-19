<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceFacade.php
 * User: Tomáš Babický
 * Date: 12.11.2020
 * Time: 5:11
 */

namespace Rendix2\FamilyTree\App\Model\Facades;


use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\SourceEntity;
use Rendix2\FamilyTree\App\Model\Entities\SourceTypeEntity;

class SourceFacade
{
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var PersonManager
     */
    private $personManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var SourceManager $sourceManager
     */
    private $sourceManager;

    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * SourceFacade constructor.
     *
     * @param IStorage $storage
     * @param PersonManager $personManager
     * @param SourceTypeManager $sourceTypeManager
     * @param SourceManager $sourceManager
     */
    public function __construct(
        IStorage $storage,
        PersonManager $personManager,
        SourceTypeManager $sourceTypeManager,
        SourceManager $sourceManager
    ) {
        $this->cache = new Cache($storage, self::class);
        $this->personManager = $personManager;
        $this->sourceTypeManager = $sourceTypeManager;
        $this->sourceManager = $sourceManager;
    }

    /**
     * @param SourceEntity[] $sources
     * @param SourceTypeEntity[] $sourceTypes
     * @param PersonEntity[] $persons
     *
     * @return SourceEntity[]
     */
    public function join(array $sources, array $sourceTypes, array $persons)
    {
        foreach ($sources as $source) {
            foreach ($sourceTypes as $sourceType) {
                if ($source->_sourceTypeId === $sourceType->id) {
                    $source->sourceType = $sourceType;
                    break;
                }
            }

            foreach ($persons as $person) {
                if ($source->_personId === $person->id) {
                    $source->person = $person;
                    break;
                }
            }

            $source->clean();
        }

        return $sources;
    }

    /**
     * @return SourceEntity[]
     */
    public function getAll()
    {
        $sources = $this->sourceManager->getAll();
        $sourceTypes = $this->sourceTypeManager->getAll();
        $persons = $this->personManager->getAll();

        return $this->join($sources, $sourceTypes, $persons);
    }

    /**
     * @return SourceEntity[]
     */
    public function getAllCached()
    {
        return $this->cache->call([$this, 'getAll']);
    }

    /**
     * @param int $sourceId
     *
     * @return SourceEntity
     */
    public function getByPrimaryKey($sourceId)
    {
        $source = $this->sourceManager->getByPrimaryKey($sourceId);
        $sourceTypes = $this->sourceTypeManager->getAll();
        $persons = $this->personManager->getAll();

        return $this->join([$source], $sourceTypes, $persons)[0];
    }

    /**
     * @param int $sourceId
     *
     * @return SourceEntity
     */
    public function getByPrimaryKeyCached($sourceId)
    {
        return $this->cache->call([$this, 'getByPrimaryKey'], $sourceId);
    }

    /**
     * @param int $personId
     *
     * @return SourceEntity[]
     */
    public function getByPersonId($personId)
    {
        $sources = $this->sourceManager->getByPersonId($personId);
        $sourceTypes = $this->sourceTypeManager->getAll();
        $person = $this->personManager->getByPrimaryKey($personId);

        return $this->join($sources, $sourceTypes, [$person]);
    }

    /**
     * @param int $personId
     *
     * @return SourceEntity[]
     */
    public function getByPersonIdCached($personId)
    {
        return $this->cache->call([$this, 'getByPersonId'], $personId);
    }

    /**
     * @param int $sourceTypeId
     *
     * @return SourceEntity[]
     */
    public function getBySourceTypeId($sourceTypeId)
    {
        $sources = $this->sourceManager->getBySourceTypeId($sourceTypeId);
        $sourceType = $this->sourceTypeManager->getByPrimaryKey($sourceTypeId);
        $persons = $this->personManager->getAll();

        return $this->join($sources, [$sourceType], $persons);
    }

    /**
     * @param int $sourceTypeId
     *
     * @return SourceEntity[]
     */
    public function getBySourceTypeCached($sourceTypeId)
    {
        return $this->cache->call([$this, 'getBySourceTypeId'], $sourceTypeId);
    }
}
