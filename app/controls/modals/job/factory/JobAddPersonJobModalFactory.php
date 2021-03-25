<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddPersonJobModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 1:21
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Job\JobAddPersonJobModal;

/**
 * Interface JobAddPersonJobModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job\Factory
 */
interface JobAddPersonJobModalFactory
{
    /**
     * @return JobAddPersonJobModal
     */
    public function create();
}
