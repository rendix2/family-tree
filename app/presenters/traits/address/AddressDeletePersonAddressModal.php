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
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait AddressPersonDeleteModal
 * @package Nette\PhpGenerator\Traits\Address
 */
trait AddressDeletePersonAddressModal
{
    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleAddressDeleteAddressPerson($personId, $addressId)
    {
        if ($this->isAjax()) {
            $this['addressDeleteAddressPersonForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'personId' => $personId
                ]
            );

            $personFilter = new PersonFilter($this->translator, $this->getHttpRequest());
            $addressFilter = new AddressFilter();

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $this->template->modalName = 'addressDeleteAddressPerson';
            $this->template->addressModalItem = $addressFilter($addressModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteAddressPersonForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'addressDeleteAddressPersonFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteAddressPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->person2AddressManager->deleteByLeftIdAndRightId($values->personId, $values->addressId);

            $persons = $this->person2AddressFacade->getByRight($values->addressId);

            $this->template->persons = $persons;

            $this->payload->showModal = false;

            $this->flashMessage('person_address_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('address_persons');
        } else {
            $this->redirect('Address:edit', $values->addressId);
        }
    }
}
