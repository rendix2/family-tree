<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:39
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;

/**
 * Class AddressForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class AddressForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var AddressSettings $addressSettings
     */
    private $addressSettings;

    /**
     * AddressForm constructor.
     *
     * @param ITranslator $translator
     * @param AddressSettings $addressSettings
     */
    public function __construct(
        ITranslator $translator,
        AddressSettings $addressSettings
    ) {
        $this->translator = $translator;
        $this->addressSettings = $addressSettings;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addGroup('address_address_group');

        $form->addSelect('countryId', $this->translator->translate('address_country'))
            ->setAttribute('data-link', $this->addressSettings->selectCountryHandle)
            ->setTranslator(null)
            ->setRequired('address_country_required')
            ->setPrompt($this->translator->translate('address_select_country'));

        $form->addSelect('townId', $this->translator->translate('address_town'))
            ->setTranslator(null)
            ->setRequired('address_town_required')
            ->setPrompt($this->translator->translate('address_select_town'));

        $form->addText('street', 'address_street')
            // ->setRequired('address_street_required')
            ->setNullable();

        $form->addInteger('streetNumber', 'address_street_number')
            ->setNullable();

        $form->addInteger('houseNumber', 'address_house_number')
            ->setNullable();

        $form->addGroup('address_gps_group');

        $form->addText('gps', 'address_gps')
            ->setNullable();

        $form->addSubmit('send', 'address_save_address');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
