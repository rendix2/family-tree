<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteWeddingAddressModal.php
 * User: Tomáš Babický
 * Date: 28.11.2020
 * Time: 1:09
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressDeleteWeddingAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteWeddingAddressModal extends Control
{
    /**
     * @param int $addressId
     * @param int $weddingId
     */
    public function handleAddressDeleteWeddingAddress($addressId, $weddingId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['addressDeleteWeddingAddressForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'weddingId' => $weddingId
                ]
            );

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);
            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $weddingFilter = $this->weddingFilter;
            $addressFilter = $this->addressFilter;

            $presenter->template->modalName = 'addressDeleteWeddingAddress';
            $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);
            $presenter->template->addressModalItem = $addressFilter($addressModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
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
        $presenter = $this->presenter;

        $this->weddingManager->updateByPrimaryKey($values->weddingId, ['addressId' => null]);

        $weddings = $this->weddingFacade->getByAddressId($values->addressId);

        $presenter->template->weddings = $weddings;

        $presenter->payload->showModal = false;

        $this->flashMessage('wedding_address_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('weddings');
    }
}
