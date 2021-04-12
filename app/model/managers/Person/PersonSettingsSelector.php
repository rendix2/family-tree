<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonSettingsSelector.php
 * User: Tomáš Babický
 * Date: 03.04.2021
 * Time: 1:57
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Person;

use Dibi\Connection;
use Dibi\Fluent;
use Nette\Http\IRequest;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Model\Interfaces\ICachedSelector;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\PersonPresenter;

/**
 * Class PersonSettingsSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Person
 */
class PersonSettingsSelector extends PersonSelector implements ICachedSelector
{
    /**
     * @var IRequest $request
     */
    private $request;

    /**
     * @var PersonSelector $personSelector
     */
    private $personSelector;

    /**
     * PersonSettingsSelector constructor.
     *
     * @param Connection     $connection
     * @param PersonTable    $table
     * @param PersonFilter   $personFilter
     * @param PersonSelector $personSelector
     * @param IRequest       $request
     */
    public function __construct(
        Connection $connection,
        PersonTable $table,
        PersonFilter $personFilter,
        PersonSelector $personSelector,
        IRequest $request
    ) {
        parent::__construct($connection, $table, $personFilter);

        $this->request = $request;
        $this->personSelector = $personSelector;
    }

    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $setting = (int)$this->request->getCookie(PersonPresenter::PERSON_ORDERING);
        $orderWay = $this->request->getCookie(PersonPresenter::PERSON_ORDERING_WAY);

        if ($setting === PersonPresenter::PERSON_ORDERING_ID) {
            return $this->personSelector->getAllFluent()
                ->orderBy($this->getTable()->getPrimaryKey(), $orderWay);
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_NAME) {
            return $this->personSelector->getAllFluent()
                ->orderBy('name', $orderWay);
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_SURNAME) {
            return $this->personSelector->getAllFluent()
                ->orderBy('surname', $orderWay);
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_NAME_SURNAME) {
            return $this->personSelector->getAllFluent()
                ->orderBy('name', $orderWay)
                ->orderBy('surname', $orderWay);
        } elseif ($setting === PersonPresenter::PERSON_ORDERING_SURNAME_NAME) {
            return $this->personSelector->getAllFluent()
                ->orderBy('surname', $orderWay)
                ->orderBy('name', $orderWay);
        } else {
            return $this->personSelector->getAllFluent()
                ->orderBy($this->getTable()->getPrimaryKey());
        }
    }
}
