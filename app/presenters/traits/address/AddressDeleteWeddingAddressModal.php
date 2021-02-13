<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteWeddingAddressModal.php
 * User: Tomáš Babický
 * Date: 28.11.2020
 * Time: 1:09
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait AddressDeleteWeddingAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressDeleteWeddingAddressModal
{
    /**
     * @param int $addressId
     * @param int $weddingId
     */
    public function handleAddressDeleteWeddingAddress($addressId, $weddingId)
    {
        if ($this->isAjax()) {
            $this['addressDeleteWeddingAddressForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'weddingId' => $weddingId
                ]
            );

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);
            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $personFilter = new PersonFilter($this->translator, $this->getHttpRequest());
            $weddingFilter = new WeddingFilter($personFilter);
            $addressFilter = new AddressFilter();

            $this->template->modalName = 'addressDeleteWeddingAddress';
            $this->template->weddingModalItem = $weddingFilter($weddingModalItem);
            $this->template->addressModalItem = $addressFilter($addressModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteWeddingAddressForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'addressDeleteWeddingAddressFormYesOnClick']);
        $form->addHidden('addressId');
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteWeddingAddressFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $this->weddingManager->updateByPrimaryKey($values->weddingId, ['addressId' => null]);

        $weddings = $this->weddingFacade->getByAddressId($values->addressId);

        $this->template->weddings = $weddings;

        $this->payload->showModal = false;

        $this->flashMessage('wedding_address_deleted', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('weddings');
    }
}
