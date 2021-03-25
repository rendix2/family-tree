<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusDeleteGenusFromListModal;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusDeletePersonGenusModal;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Interface GenusDeleteGenusFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory
 */
interface GenusDeleteGenusFromListModalFactory
{
    /**
     * @return GenusDeleteGenusFromListModal
     */
    public function create();
}