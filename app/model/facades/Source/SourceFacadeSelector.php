<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourecSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 3:12
 */

namespace Rendix2\FamilyTree\App\Model\Facades\Source;

use Dibi\Fluent;
use Nette\NotImplementedException;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Entities\SourceEntity;
use Rendix2\FamilyTree\App\Model\Entities\SourceTypeEntity;
use Rendix2\FamilyTree\App\Model\Facades\DefaultFacade\DefaultFacadeSelector;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\Source\Interfaces\ISourceSelector;
use Rendix2\FamilyTree\App\Model\Managers\SourceManager;
use Rendix2\FamilyTree\App\Model\Managers\SourceTypeManager;

/**
 * Class SourceSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\Source
 */
class SourceFacadeSelector extends DefaultFacadeSelector implements ISourceSelector
{
    /**
     * @var PersonManager
     */
    private $personManager;

    /**
     * @var SourceManager $sourceManager
     */
    private $sourceManager;

    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * SourceSelector constructor.
     *
     * @param PersonManager     $personManager
     * @param SourceFilter      $sourceFilter
     * @param SourceManager     $sourceManager
     * @param SourceTypeManager $sourceTypeManager
     */
    public function __construct(
        PersonManager $personManager,
        SourceFilter $sourceFilter,
        SourceManager $sourceManager,
        SourceTypeManager $sourceTypeManager
    ) {
        parent::__construct($sourceFilter);

        $this->personManager = $personManager;
        $this->sourceManager = $sourceManager;
        $this->sourceTypeManager = $sourceTypeManager;
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

    public function getByPrimaryKey($id)
    {
        $source = $this->sourceManager->select()->getManager()->getByPrimaryKey($id);

        if (!$source) {
            return null;
        }

        $sourceType = $this->sourceTypeManager->select()->getManager()->getByPrimaryKey($source->_sourceTypeId);
        $person = $this->personManager->select()->getManager()->getByPrimaryKey($source->_personId);

        return $this->join([$source], [$sourceType], [$person])[0];
    }

    public function getByPrimaryKeys(array $ids)
    {
        throw new NotImplementedException();
    }

    public function getColumnFluent($column)
    {
        throw new NotImplementedException();
    }

    public function getAll()
    {
        $sources = $this->sourceManager->select()->getCachedManager()->getAll();

        $personIds = $this->sourceManager->select()->getManager()->getColumnFluent('personId');
        $sourceTypeIds = $this->getIds($sources, '_sourceTypeId');

        $persons = $this->personManager->select()->getManager()->getBySubQuery($personIds);
        $sourceTypes = $this->sourceTypeManager->select()->getManager()->getByPrimaryKeys($sourceTypeIds);

        return $this->join($sources, $sourceTypes, $persons);
    }

    public function getPairs($column)
    {
        throw new NotImplementedException();
    }

    public function getBySubQuery(Fluent $query)
    {
        throw new NotImplementedException();
    }

    public function getByPersonId($personId)
    {
        $sources = $this->sourceManager->select()->getManager()->getByPersonId($personId);
        $sourceTypes = $this->sourceTypeManager->select()->getCachedManager()->getAll();
        $person = $this->personManager->select()->getManager()->getByPrimaryKey($personId);

        return $this->join($sources, $sourceTypes, [$person]);
    }

    public function getBySourceTypeId($sourceTypeId)
    {
        $sources = $this->sourceManager->select()->getManager()->getBySourceTypeId($sourceTypeId);
        $sourceType = $this->sourceTypeManager->select()->getManager()->getByPrimaryKey($sourceTypeId);

        $personIds = $this->sourceManager->select()->getManager()->getColumnFluent('personId');

        $persons = $this->personManager->select()->getManager()->getBySubQuery($personIds);

        return $this->join($sources, [$sourceType], $persons);
    }

    public function getAllPairs()
    {
        $rows = $this->getAll();

        return $this->applyFilter($rows);
    }
}
