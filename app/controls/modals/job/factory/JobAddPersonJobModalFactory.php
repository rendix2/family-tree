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

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobAddPersonJobModal;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\PersonJobSettings;

/**
 * Trait JobAddPersonJobModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
interface JobAddPersonJobModalFactory
{
    /**
     * @return JobAddPersonJobModal
     */
    public function create();
}