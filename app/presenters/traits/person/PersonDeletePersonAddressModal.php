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
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits
 */
trait PersonDeletePersonAddressModal
{
    /**
     * @param int $personId
     * @param int $addressId
     */
    public function handlePersonDeletePersonAddress($personId, $addressId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->isAjax()) {
            $this['personDeletePersonAddressForm']->setDefaults(
                [
                    'personId' => $personId,
                    'addressId' => $addressId
                ]
            );

            $addressFilter = new AddressFilter();
            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

             $this->template->modalName = 'personDeletePersonAddress';
            $this->template->addressModalItem = $addressFilter($addressModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeletePersonAddressForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'personDeletePersonAddressFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeletePersonAddressFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->person2AddressManager->deleteByLeftIdAndRightId($values->personId, $values->addressId);

            $addresses = $this->person2AddressFacade->getByLeft($values->personId);

            $this->template->addresses = $addresses;

            $this->payload->showModal = false;

            $this->flashMessage('person_address_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('addresses');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
