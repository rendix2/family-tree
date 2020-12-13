<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonBirthModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 19:34
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;


use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait TownDeletePersonBirthModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressDeleteBirthPersonModal
{
    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleAddressDeleteBirthPerson($addressId, $personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Address:edit', $addressId);
        }

        $this['addressDeleteBirthPersonForm']->setDefaults(
            [
                'personId' => $personId,
                'addressId' => $addressId
            ]
        );

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
        $addressFilter = new AddressFilter();

        $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

        $this->template->modalName = 'addressDeleteBirthPerson';
        $this->template->addressModalItem = $addressFilter($addressModalItem);
        $this->template->personModalItem = $personFilter($personModalItem);

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteBirthPersonForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'addressDeleteBirthPersonFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteBirthPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->personId, ['birthAddressId' => null]);

            $birthPersons = $this->personManager->getByBirthAddressId($values->personId);

            $this->template->birthPersons = $birthPersons;

            $this->payload->showModal = false;

            $this->flashMessage('person_saved', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('birth_persons');
        } else {
            $this->redirect('Person:edit', $values->addressId);
        }
    }
}
