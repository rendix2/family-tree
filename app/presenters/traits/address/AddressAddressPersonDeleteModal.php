<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressPersonDeleteModal.php
 * User: Tomáš Babický
 * Date: 28.10.2020
 * Time: 20:26
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait AddressPersonDeleteModal
 * @package Nette\PhpGenerator\Traits\Address
 */
trait AddressAddressPersonDeleteModal
{
    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleDeleteAddressPersonItem($personId, $addressId)
    {
        $this->template->modalName = 'deleteAddressPersonItem';

        $this['deleteAddressPersonForm']->setDefaults(
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
    protected function createComponentDeleteAddressPersonForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteAddressPersonFormOk');

        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteAddressPersonFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->person2AddressManager->deleteByLeftIdAndRightId($values->personId, $values->addressId);

            $persons = $this->person2AddressManager->getAllByRightJoined($values->addressId);

            $this->payload->showModal = false;
            $this->template->modalName = 'deleteAddressPersonItem';
            $this->template->persons = $persons;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('address_persons');
        } else {
            $this->redirect(':edit', $values->addressId);
        }
    }
}
