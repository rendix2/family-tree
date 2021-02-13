<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobFilter.php
 * User: Tomáš Babický
 * Date: 22.09.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Filters;

use Nette\Http\IRequest;
use Rendix2\FamilyTree\App\Model\Entities\JobEntity;
use Rendix2\FamilyTree\SettingsModule\App\Presenters\JobPresenter;

/**
 * Class JobFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class JobFilter
{
    /**
     * @var IRequest $request
     */
    private $request;

    /**
     * JobFilter constructor.
     *
     * @param IRequest $request
     */
    public function __construct(IRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @param JobEntity $job
     *
     * @return string
     */
    public function __invoke(JobEntity $job)
    {
        $jobNameOrder = (int)$this->request->getCookie(JobPresenter::JOB_NAME_ORDER);

        if ($job->company && $job->position) {
            if ($jobNameOrder === JobPresenter::JOB_ORDER_NAME_COMPANY_POSITION) {
                return $job->company . ' ' . $job->position;
            } elseif ($jobNameOrder === JobPresenter::JOB_ORDER_NAME_POSITION_COMPANY) {
                return $job->position. ' ' . $job->company;
            }

            return $job->company . ' ' . $job->position;
        } elseif (!$job->company && $job->position) {
            return $job->position;
        } elseif ($job->company && !$job->position) {
            return $job->company;
        } else {
            return '';
        }
    }
}
