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

/**
 * Class DefaultFacadeSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Facades\DefaultFacade
 */
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
     * DefaultFacadeSelector destructor.
     */
    public function __destruct()
    {
        $this->filter = null;
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
            /** @var IFilter|AddressFilter $filter BIG HACK */
            $resultRows[$row->id] = $filter($row);
        }

        return $resultRows;
    }
}