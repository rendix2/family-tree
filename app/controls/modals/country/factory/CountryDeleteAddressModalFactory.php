<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddresdDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 1:14
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Factory;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Modals\Country\CountryDeleteAddressModal;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Interface CountryDeleteAddressModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Factory
 */
interface CountryDeleteAddressModalFactory
{
    /**
     * @return CountryDeleteAddressModal
     */
    public function create();
}
