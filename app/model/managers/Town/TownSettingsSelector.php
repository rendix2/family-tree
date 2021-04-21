<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownSettingsSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 12:29
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Town;

use Dibi\Connection;
use Dibi\Fluent;
use Nette\Http\IRequest;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Model\Interfaces\ICachedSelector;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\TownPresenter;

/**
 * Class TownSettingsSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Town
 */
class TownSettingsSelector extends TownSelector implements ICachedSelector
{
    /**
     * @var IRequest $request
     */
    private $request;

    /**
     * @var TownSelector $selector
     */
    private $selector;

    /**
     * TownSettingsSelector constructor.
     *
     * @param Connection   $connection
     * @param TownTable    $table
     * @param TownFilter   $townFilter
     * @param TownSelector $townSelector
     * @param IRequest     $request
     */
    public function __construct(
        Connection $connection,
        TownTable $table,
        TownFilter $townFilter,
        TownSelector $townSelector,
        IRequest $request
    ) {
        parent::__construct($connection, $table, $townFilter);

        $this->selector = $townSelector;
        $this->request = $request;
    }

    public function __destruct()
    {
        $this->request = null;
        $this->selector = null;

        parent::__destruct();
    }

    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $setting = (int)$this->request->getCookie(TownPresenter::TOWN_ORDERING);
        $orderWay = (int)$this->request->getCookie(TownPresenter::TOWN_ORDERING_WAY);

        if ($setting === TownPresenter::TOWN_ORDERING_ID) {
            return $this->selector->getAllFluent()
                ->orderBy($this->getTable()->getPrimaryKey(), $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME) {
            return $this->selector->getAllFluent()
                ->orderBy('name', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP) {
            return $this->selector->getAllFluent()
                ->orderBy('zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME_ZIP) {
            return $this->selector->getAllFluent()
                ->orderBy('name', $orderWay)
                ->orderBy('zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP_NAME) {
            return $this->selector->getAllFluent()
                ->orderBy('zip', $orderWay)
                ->orderBy('name', $orderWay);
        } else {
            return $this->selector->getAllFluent()
                ->orderBy($this->getTable()->getPrimaryKey());
        }
    }
}
