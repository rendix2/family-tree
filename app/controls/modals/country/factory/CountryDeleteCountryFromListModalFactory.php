<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Factory;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteCountryFromListModal;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Interface CountryDeleteCountryFromListModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Factory
 */
interface CountryDeleteCountryFromListModalFactory
{
    /**
     * @return CountryDeleteCountryFromListModal
     */
    public function create();
}
