<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobSettingsSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 15:16
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Job;


use Dibi\Connection;
use Dibi\Fluent;
use Nette\Http\IRequest;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\JobPresenter;

/**
 * Class JobSettingsSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Job
 */
class JobSettingsSelector extends JobSelector
{
    /**
     * @var JobSelector
     */
    private $selector;

    /**
     * @var IRequest $request
     */
    private $request;

    /**
     * JobSettingsSelector constructor.
     *
     * @param Connection  $connection
     * @param JobFilter   $jobFilter
     * @param JobTable    $table
     * @param JobSelector $jobSelector
     * @param IRequest    $request
     */
    public function __construct(
        Connection $connection,
        JobFilter $jobFilter,
        JobTable $table,
        JobSelector $jobSelector,
        IRequest $request
    ) {
        parent::__construct($connection, $jobFilter, $table);

        $this->selector = $jobSelector;
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
        $setting = (int)$this->request->getCookie(JobPresenter::JOB_ORDERING);
        $orderWay = $this->request->getCookie(JobPresenter::JOB_ORDERING_WAY);

        if ($setting === JobPresenter::JOB_ORDERING_ID) {
            return $this->selector->getAllFluent()
                ->orderBy($this->getTable()->getPrimaryKey(), $orderWay);
        } elseif ($setting === JobPresenter::JOB_ORDERING_COMPANY) {
            return $this->selector->getAllFluent()
                ->orderBy('company', $orderWay);
        } elseif ($setting === JobPresenter::JOB_ORDERING_POSITION) {
            return $this->selector->getAllFluent()
                ->orderBy('position', $orderWay);
        } elseif ($setting === JobPresenter::JOB_ORDERING_COMPANY_POSITION) {
            return $this->selector->getAllFluent()
                ->orderBy('company', $orderWay)
                ->orderBy('position', $orderWay);
        } elseif ($setting === JobPresenter::JOB_ORDERING_POSITION_COMPANY) {
            return $this->selector->getAllFluent()
                ->orderBy('position', $orderWay)
                ->orderBy('company', $orderWay);
        } else {
            return $this->selector->getAllFluent()
                ->orderBy($this->getTable()->getPrimaryKey());
        }
    }
}
