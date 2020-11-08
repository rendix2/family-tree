<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddresdDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 1:14
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Country;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait CountryAddressDeleteModal
 *
 * @package Nette\PhpGenerator\Traits\Country
 */
trait CountryAddressDeleteModal
{
    /**
     * @param int $addressId
     * @param int $countryId
     */
    public function handleDeleteAddressItem($addressId, $countryId)
    {
        $this->template->modalName = 'deleteAddressItem';

        $this['deleteCountryAddressForm']->setDefaults(
            [
                'addressId' => $addressId,
                'countryId' => $countryId
            ]
        );

        if ($this->isAjax()) {
            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteCountryAddressForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteCountryAddressFormOk');

        $form->addHidden('addressId');
        $form->addHidden('countryId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteCountryAddressFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->addressManager->deleteByPrimaryKey($values->addressId);

            $addresses = $this->addressManager->getAllByCountryIdJoinedTown($values->countryId);

            $this->template->addresses = $addresses;
            $this->template->modalName = 'deleteAddressItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('addresses');
        } else {
            $this->redirect(':edit', $values->countryId);
        }
    }
}
