<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NameDeletePersonNameModal.php
 * User: Tomáš Babický
 * Date: 29.10.2020
 * Time: 15:52
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
 * Class NameDeletePersonNameModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Name
 */
class NameDeletePersonNameModal extends Control
{
    /**
     * @param int $currentNameId
     * @param int $deleteNameId
     * @param int $personId
     */
    public function handleNameDeletePersonName($currentNameId, $deleteNameId, $personId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['nameDeletePersonNameForm']->setDefaults(
                [
                    'currentNameId' => $currentNameId,
                    'deleteNameId' => $deleteNameId,
                    'personId' => $personId
                ]
            );

            if ($currentNameId === $deleteNameId) {
                $this['nameDeletePersonNameForm-yes']->setAttribute('data-naja-force-redirect', '');
            }

            $personFilter = $this->personFilter;
            $nameFilter = $this->nameFilter;

            $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($deleteNameId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $presenter->template->modalName = 'nameDeletePersonName';
            $presenter->template->nameModalItem = $nameFilter($nameModalItem);
            $presenter->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentNameDeletePersonNameForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'nameDeletePersonNameFormYesOnClick']);
        $form->addHidden('currentNameId');
        $form->addHidden('deleteNameId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function nameDeletePersonNameFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            try {
                $this->nameManager->deleteByPrimaryKey($values->deleteNameId);

                $presenter->payload->showModal = false;

                $this->flashMessage('name_deleted', BasePresenter::FLASH_SUCCESS);

                if ($values->currentNameId === $values->deleteNameId) {
                    $this->redirect('Name:default');
                } else {
                    $this->redrawControl('flashes');
                    $this->redrawControl('names');
                }
            } catch (ForeignKeyConstraintViolationException $e) {
                if ($e->getCode() === 1451) {
                    $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                    $this->redrawControl('flashes');
                } else {
                    Debugger::log($e, ILogger::EXCEPTION);
                }
            }
        } else {
            $this->redirect('Name:edit', $values->deleteNameId);
        }
    }
}