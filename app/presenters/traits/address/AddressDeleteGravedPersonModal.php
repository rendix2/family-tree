<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonGravedModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 19:35
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;


use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

trait AddressDeleteGravedPersonModal
{

    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleAddressDeleteGravedPerson($addressId, $personId)
    {
        if ($this->isAjax()) {
            $this['addressDeleteGravedPersonForm']->setDefaults(
                [
                    'personId' => $personId,
                    'addressId' => $addressId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $addressFilter = new AddressFilter();

            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'addressDeleteGravedPerson';
            $this->template->addressModalItem = $addressFilter($addressModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteGravedPersonForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'addressDeleteGravedPersonFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteGravedPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->personId, ['gravedAddressId' => null]);

            $gravedPersons = $this->personManager->getByGravedAddressId($values->personId);

            $this->template->gravedPersons = $gravedPersons;

            $this->payload->showModal = false;

            $this->flashMessage('person_saved', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('graved_persons');
        } else {
            $this->redirect('Person:edit', $values->addressId);
        }
    }
}
