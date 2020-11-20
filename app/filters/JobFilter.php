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

use Rendix2\FamilyTree\App\Model\Entities\JobEntity;

/**
 * Class JobFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class JobFilter
{
    /**
     * @param JobEntity $job
     *
     * @return string
     */
    public function __invoke(JobEntity $job)
    {
        if ($job->company && $job->position) {
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
