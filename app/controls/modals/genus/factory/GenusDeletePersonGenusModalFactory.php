<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPersonGenusDeleteModal.php
 * User: Tomáš Babický
 * Date: 29.11.2020
 * Time: 4:17
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusDeletePersonGenusModal;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Interface GenusDeletePersonGenusModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory
 */
interface GenusDeletePersonGenusModalFactory
{
    /**
     * @return GenusDeletePersonGenusModal
     */
    public function create();
}
