<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
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
 * Class NameDeleteNameFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name
 */
class NameDeleteNameFromListModal extends Control
{
    /**
     * @param int $nameId
     * @param int $personId
     */
    public function handleNameDeleteNameFromList($nameId, $personId)
    {
        $presenter = $this->presenter;

        if ($this->isAjax()) {
            $this['nameDeleteNameFromListForm']->setDefaults(
                [
                    'personId' => $personId,
                    'nameId' => $nameId
                ]
            );

            $personFilter = $this->personFilter;
            $nameFilter = $this->nameFilter;

            $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($nameId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'nameDeleteNameFromList';
            $this->template->nameModalItem = $nameFilter($nameModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentNameDeleteNameFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'nameDeleteNameFromListFormYesOnClick']);
        $form->addHidden('nameId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function nameDeleteNameFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->nameManager->deleteByPrimaryKey($values->nameId);

            $this->flashMessage('name_deleted', BasePresenter::FLASH_SUCCESS);

            $this->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $this->redrawControl('flashes');
        }
    }
}