<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownManager.php
 * User: Tomáš Babický
 * Date: 19.09.2020
 * Time: 23:58
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Connection;
use Dibi\Fluent;
use Nette\Caching\IStorage;
use Nette\Http\IRequest;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;

/**
 * Class TownManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class TownManager extends CrudManager
{
    /**
     * @var TownFilter $townFilter
     */
    private $townFilter;

    /**
     * TownManager constructor.
     *
     * @param Connection $dibi
     * @param IRequest $request
     * @param IStorage $storage
     * @param TownFilter $townFilter
     */
    public function __construct(
        Connection $dibi,
        IRequest $request,
        IStorage $storage,
        TownFilter $townFilter
    ) {
        parent::__construct($dibi, $request, $storage);

        $this->townFilter = $townFilter;
    }


    /**
     * @return TownEntity[]
     */
    public function getAll()
    {
        return $this->getAllFluent()->execute()->setRowClass(TownEntity::class)->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return TownEntity
     */
    public function getByPrimaryKey($id)
    {
        return $this->getAllFluent()
            ->where('%n = %i', $this->getPrimaryKey(), $id)
            ->execute()
            ->setRowClass(TownEntity::class)
            ->fetch();
    }

    /**
     * @param array $ids
     *
     * @return TownEntity[]|false
     */
    public function getByPrimaryKeys(array $ids)
    {
        $result = $this->checkValues($ids);

        if ($result !== null) {
            return $result;
        }

        return $this->getAllFluent()
            ->where('%n in %in', $this->getPrimaryKey(), $ids)
            ->execute()
            ->setRowClass(TownEntity::class)
            ->fetchAll();
    }

    /**
     * @param Fluent $query
     *
     * @return TownEntity[]
     */
    public function getBySubQuery(Fluent $query)
    {
        return $this->getAllFluent()
            ->where('%n in %sql', $this->getPrimaryKey(), $query)
            ->execute()
            ->setRowClass(TownEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $countryId
     *
     * @return array
     */
    public function getPairsByCountry($countryId)
    {
        $towns = $this->getAllByCountry($countryId);

        return $this->applyTownFilter($towns);
    }

    /**
     * @param int $countryId
     *
     * @return array
     */
    public function getPairsByCountryCached($countryId)
    {
        return $this->getCache()->call([$this, 'getPairsByCountry'], $countryId);
    }

    /**
     * @return array
     */
    public function getAllPairs()
    {
        $towns = $this->getAll();

        return $this->applyTownFilter($towns);
    }

    /**
     * @return array
     */
    public function getAllPairsCached()
    {
        return $this->getCache()->call([$this, 'getAllPairs']);
    }

    /**
     * @param array $towns
     *
     * @return array
     */
    public function applyTownFilter(array $towns)
    {
        $townFilter = $this->townFilter;

        $townsResult = [];

        foreach ($towns as $town) {
            $townsResult[$town->id] = $townFilter($town);
        }

        return $townsResult;
    }

    /**
     * @param int $countryId
     *
     * @return TownEntity[]
     */
    public function getAllByCountry($countryId)
    {
        return $this->getAllFluent()
            ->where('[countryId] = %i', $countryId)
            ->execute()
            ->setRowClass(TownEntity::class)
            ->fetchAll();
    }

    /**
     * @return array
     */
    public function getToMap()
    {
        return $this->getAllFluent()
            ->where('[gps] IS NOT NULL')
            ->execute()
            ->setRowClass(TownEntity::class)
            ->fetchAll();
    }
}
