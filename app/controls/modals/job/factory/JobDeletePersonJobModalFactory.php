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

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobDeletePersonJobModal;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait JobDeletePersonModal
 */
interface JobDeletePersonJobModalFactory
{
    /**
     * @return JobDeletePersonJobModal
     */
    public function create();
}
