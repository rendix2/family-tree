<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPersonNameDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 0:25
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusDeletePersonNameModal;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Interface GenusDeletePersonNameModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory
 */
interface GenusDeletePersonNameModalFactory
{
    /**
     * @return GenusDeletePersonNameModal
     */
    public function create();
}
