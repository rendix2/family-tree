<?php
/**
 *
 * Created by PhpStorm.
 * Filename: StatisticPresenter.php
 * User: Tomáš Babický
 * Date: 09.02.2021
 * Time: 0:15
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Rendix2\FamilyTree\App\Managers\StatisticManager;

/**
 * Class StatisticPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class StatisticPresenter extends BasePresenter
{
    /**
     * @var StatisticManager $statisticManager
     */
    private $statisticManager;

    /**
     * StatisticPresenter constructor.
     *
     * @param StatisticManager $statisticManager
     */
    public function __construct(StatisticManager $statisticManager)
    {
        parent::__construct();

        $this->statisticManager = $statisticManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $personNameCount = $this->statisticManager->getPersonNameCount();
        $this->template->personNameCount = $personNameCount;

        $personSurnameCount = $this->statisticManager->getPersonSurnameCount();
        $this->template->personSurnameCount = $personSurnameCount;

        $personBirthTownCount = $this->statisticManager->getPersonBirthTownCount();
        $this->template->personBirthTownCount = $personBirthTownCount;

        $personDeathTownCount = $this->statisticManager->getPersondeathTownCount();
        $this->template->personDeathTownCount = $personDeathTownCount;

        $personBirthYearCount = $this->statisticManager->getPersonBirthYearCount();
        $this->template->personBirthYearCount = $personBirthYearCount;

        $personDeathYearCount = $this->statisticManager->getPersonDeathYearCount();
        $this->template->personDeathYearCount = $personDeathYearCount;

        $personAgeCount = $this->statisticManager->getPersonAgeCount();
        $this->template->personAgeCount = $personAgeCount;

        // TODO NOT SHOWED YET
        $averagePersonAge = $this->statisticManager->getAveragePersonAge();
        $this->template->averagePersonAge = $averagePersonAge;
    }
}
