<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonDeathModal.php
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

trait AddressDeleteDeathPersonModal
{
    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleAddressDeleteDeathPerson($addressId, $personId)
    {
        if ($this->isAjax()) {
            $this['addressDeleteDeathPersonForm']->setDefaults(
                [
                    'personId' => $personId,
                    'addressId' => $addressId
                ]
            );

            $personFilter = new PersonFilter($this->translator, $this->getHttpRequest());
            $addressFilter = new AddressFilter();

            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'addressDeleteDeathPerson';
            $this->template->addressModalItem = $addressFilter($addressModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteDeathPersonForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'addressDeleteDeathPersonFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteDeathPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->personId, ['deathAddressId' => null]);

            $deathPersons = $this->personSettingsManager->getByDeathAddressId($values->personId);

            $this->template->deathPersons = $deathPersons;

            $this->payload->showModal = false;

            $this->flashMessage('person_saved', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('death_persons');
        } else {
            $this->redirect('Person:edit', $values->addressId);
        }
    }
}
