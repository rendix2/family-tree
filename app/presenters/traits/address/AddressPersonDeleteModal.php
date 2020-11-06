<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressPersonDeleteModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 1:41
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait AddressPersonDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressPersonDeleteModal
{
    /**
     * @param int $addressId
     * @param int $personId
     */
    public function handleDeletePersonItem($addressId, $personId)
    {
        $this->template->modalName = 'deletePersonItem';

        $this['deletePersonForm']->setDefaults(
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
    protected function createComponentDeletePersonForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonFormOk');

        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->personManager->deleteByPrimaryKey($values->personId);

            $birthPersons = $this->personManager->getByBirthAddressId($values->personId);
            $deathPersons = $this->personManager->getByDeathAddressId($values->personId);
            $gravedPersons = $this->personManager->getByGravedAddressId($values->personId);

            $this->payload->showModal = false;

            $this->template->modalName = 'deletePersonItem';
            $this->template->birthPersons = $birthPersons;
            $this->template->deathPersons = $deathPersons;
            $this->template->gravedPersons = $gravedPersons;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('birth_persons');
            $this->redrawControl('death_persons');
            $this->redrawControl('graved_persons');
        } else {
            $this->redirect(':edit', $values->addressId);
        }
    }
}
