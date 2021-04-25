<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DefaultFacadeSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 17:45
 */

namespace Rendix2\FamilyTree\App\Model\Facades\DefaultFacade;


use Nette\InvalidStateException;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\IFilter;

class DefaultFacadeSelector
{

    /**
     * @var IFilter|null $filter
     */
    private $filter;

    /**
     * DefaultFacadeSelector constructor.
     *
     * @param IFilter|null $filter
     */
    public function __construct(IFilter $filter = null)
    {
        $this->filter = $filter;
    }

    /**
     * @param array $rows
     * @param string $column
     *
     * @return array
     */
    public function getIds(array $rows, $column)
    {
        $ids = [];

        foreach ($rows as $row) {
            $ids[] = $row->{$column};
        }

        return array_unique($ids);
    }

    public function uniqueIds(array $ids)
    {
        $ids = array_unique($ids);

        foreach ($ids as $key => $value) {
            if ($value === null) {
                unset($ids[$key]);
            }
        }

        return $ids;
    }

    public function isOnlyNull(array $ids)
    {
        $ids = array_unique($ids);

        return count($ids) === 1 && $ids[0] === null;
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    protected function applyFilter(array $rows)
    {
        $filter = $this->filter;

        if ($filter === null) {
            throw new InvalidStateException();
        }

        $resultRows = [];

        foreach ($rows as $row) {
            /** @var IFilter|AddressFilter $filter  BIG HACK */
            $resultRows[$row->id] = $filter($row);
        }

        return $resultRows;
    }
}