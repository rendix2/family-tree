<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddTownModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 0:49
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job\Factory;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobAddTownModal;
use Rendix2\FamilyTree\App\Forms\TownForm;

/**
 * Interface JobAddTownModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job\Factory
 */
interface JobAddTownModalFactory
{
    /**
     * @return JobAddTownModal
     */
    public function create();
}
