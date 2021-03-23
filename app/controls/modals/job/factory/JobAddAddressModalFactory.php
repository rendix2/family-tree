<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddAddressModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 0:46
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job\Factory;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Modals\Job\JobAddAddressModal;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;

/**
 * Trait JobAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
interface JobAddAddressModalFactory
{
    /**
     * @return JobAddAddressModal
     */
    public function create();
}
