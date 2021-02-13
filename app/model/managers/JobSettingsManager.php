<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobSettingsManager.php
 * User: Tomáš Babický
 * Date: 11.02.2021
 * Time: 18:11
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Fluent;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\JobPresenter;

/**
 * Class JobSettingsManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class JobSettingsManager extends JobManager
{
    /**
     * @return Fluent
     */
    public function getAllFluent()
    {
        $setting = (int)$this->getRequest()->getCookie(JobPresenter::JOB_ORDERING);
        $orderWay = $this->getRequest()->getCookie(JobPresenter::JOB_ORDERING_WAY);

        if ($setting === JobPresenter::JOB_ORDERING_ID) {
            return parent::getAllFluent()
                ->orderBy($this->getPrimaryKey(), $orderWay);
        } elseif ($setting === JobPresenter::JOB_ORDERING_COMPANY) {
            return parent::getAllFluent()
                ->orderBy('company', $orderWay);
        } elseif ($setting === JobPresenter::JOB_ORDERING_POSITION) {
            return parent::getAllFluent()
                ->orderBy('position', $orderWay);
        } elseif ($setting === JobPresenter::JOB_ORDERING_COMPANY_POSITION) {
            return parent::getAllFluent()
                ->orderBy('company', $orderWay)
                ->orderBy('position', $orderWay);
        } elseif ($setting === JobPresenter::JOB_ORDERING_POSITION_COMPANY) {
            return parent::getAllFluent()
                ->orderBy('position', $orderWay)
                ->orderBy('company', $orderWay);
        } else {
            return parent::getAllFluent()
                ->orderBy($this->getPrimaryKey());
        }
    }

    /**
     * @return false|string
     */
    public function getClassName()
    {
        return Tables::JOB_TABLE;
    }
}
