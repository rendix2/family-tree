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

class TownSettingsSelector extends TownSelector implements ICachedSelector
{
    /**
     * @var IRequest $request
     */
    private $request;

    /**
     * @var TownSelector $townSelector
     */
    private $townSelector;

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

        $this->townSelector = $townSelector;
        $this->request = $request;
    }

    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $setting = (int)$this->request->getCookie(TownPresenter::TOWN_ORDERING);
        $orderWay = (int)$this->request->getCookie(TownPresenter::TOWN_ORDERING_WAY);

        if ($setting === TownPresenter::TOWN_ORDERING_ID) {
            return $this->townSelector->getAllFluent()
                ->orderBy($this->getTable()->getPrimaryKey(), $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME) {
            return $this->townSelector->getAllFluent()
                ->orderBy('name', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP) {
            return $this->townSelector->getAllFluent()
                ->orderBy('zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_NAME_ZIP) {
            return $this->townSelector->getAllFluent()
                ->orderBy('name', $orderWay)
                ->orderBy('zip', $orderWay);
        } elseif ($setting === TownPresenter::TOWN_ORDERING_ZIP_NAME) {
            return $this->townSelector->getAllFluent()
                ->orderBy('zip', $orderWay)
                ->orderBy('name', $orderWay);
        } else {
            return $this->townSelector->getAllFluent()
                ->orderBy($this->getTable()->getPrimaryKey());
        }
    }
}
