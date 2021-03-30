<?php

/**
 *
 * Created by PhpStorm.
 * Filename: JobDeletePersonModal.php
 * User: Tomáš Babický
 * Date: 28.10.2020
 * Time: 16:42
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job\Factory;

use Rendix2\FamilyTree\App\Controls\Modals\Job\JobDeletePersonJobModal;

/**
 * Interface JobDeletePersonJobModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job\Factory
 */
interface JobDeletePersonJobModalFactory
{
    /**
     * @return JobDeletePersonJobModal
     */
    public function create();
}
