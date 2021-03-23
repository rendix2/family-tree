<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusAddNameModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 0:45
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus\Factory;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\GenusAddNameModal;
use Rendix2\FamilyTree\App\Forms\NameForm;

/**
 * Trait GenusAddNameModal
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Genus
 */
interface GenusAddNameModalFactory
{
    /**
     * @return GenusAddNameModal
     */
    public function create();
}
