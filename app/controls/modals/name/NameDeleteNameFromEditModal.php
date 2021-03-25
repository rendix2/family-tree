<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Name;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class NameDeleteNameFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name
 */
class NameDeleteNameFromEditModal extends Control
{
    /**
     * @param int $nameId
     * @param int $personId
     */
    public function handleNameDeleteNameFromEdit($nameId, $personId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['nameDeleteNameFromEditForm']->setDefaults(
                [
                    'personId' => $personId,
                    'nameId' => $nameId
                ]
            );

            $personFilter = $this->personFilter;
            $nameFilter = $this->nameFilter;

            $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($nameId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'nameDeleteNameFromEdit';
            $this->template->nameModalItem = $nameFilter($nameModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentNameDeleteNameFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'nameDeleteNameFromEditFormYesOnClick'], true);
        $form->addHidden('nameId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function nameDeleteNameFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->nameManager->deleteByPrimaryKey($values->nameId);

            $this->flashMessage('name_deleted', BasePresenter::FLASH_SUCCESS);

            $this->redirect('Name:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                $this->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
