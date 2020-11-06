<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteAddressModal.php
 * User: Tomáš Babický
 * Date: 26.10.2020
 * Time: 1:26
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits
 */
trait PersonDeleteAddressModal
{
    /**
     * @param int $personId
     * @param int $addressId
     */
    public function handleDeleteAddressItem($personId, $addressId)
    {
        $this->template->modalName = 'deleteAddressItem';

        $this['deletePersonAddressForm']->setDefaults(
            [
                'personId' => $personId,
                'addressId' => $addressId
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
    protected function createComponentDeletePersonAddressForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonAddressFormOk');

        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonAddressFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->person2AddressManager->deleteByLeftIdAndRightId($values->personId, $values->addressId);

            $addresses = $this->person2AddressManager->getAllByLeftJoined($values->personId);

            $this->template->addresses = $addresses;
            $this->template->modalName = 'deleteAddressItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('addresses');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}
