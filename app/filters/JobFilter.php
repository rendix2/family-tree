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

use Dibi\Row;
use Nette\Localization\ITranslator;

/**
 * Class JobFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class JobFilter
{
    /**
     * @param Row $job
     *
     * @return string
     */
    public function __invoke(Row $job)
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