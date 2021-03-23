<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryDeleteTownModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 0:50
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Factory;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteTownModal;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Interface CountryDeleteTownModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Factory
 */
interface CountryDeleteTownModalFactory
{
    /**
     * @return CountryDeleteTownModal
     */
    public function create();
}
