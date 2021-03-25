<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressDeletePersonAddressFromEditModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:50
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonAddress;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class PersonAddressDeletePersonAddressFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonAddress
 */
class PersonAddressDeletePersonAddressFromEditModal extends \Nette\Application\UI\Control
{
    /**
     * @param int $personId
     * @param int $addressId
     */
    public function handlePersonAddressDeletePersonAddressFromEdit($personId, $addressId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {

            $this['personAddressDeletePersonAddressFromEditForm']->setDefaults(
                [
                    'personId' => $personId,
                    'addressId' => $addressId
                ]
            );

            $addressFilter = $this->addressFilter;
            $personFilter = $this->personFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

            $presenter->template->modalName = 'personAddressDeletePersonAddressFromEdit';
            $presenter->template->addressModalItem = $addressFilter($addressModalItem);
            $presenter->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddressDeletePersonAddressFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'personAddressDeletePersonAddressFromEditFormYesOnClick'], true);

        $form->addHidden('personId');
        $form->addHidden('addressId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personAddressDeletePersonAddressFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->person2AddressManager->deleteByLeftIdAndRightId($values->personId, $values->addressId);

            $this->flashMessage('person_address_deleted', BasePresenter::FLASH_SUCCESS);

            $this->redirect('PersonAddress:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                $presenter->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}