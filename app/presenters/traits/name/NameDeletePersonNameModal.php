<?php

namespace Rendix2\FamilyTree\App\Presenters\Traits\Name;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 *
 * Created by PhpStorm.
 * Filename: NameDeletePersonNameModal.php
 * User: Tomáš Babický
 * Date: 29.10.2020
 * Time: 15:52
 */

trait NameDeletePersonNameModal
{
    /**
     * @param int $currentNameId
     * @param int $deleteNameId
     * @param int $personId
     */
    public function handleNameDeletePersonName($currentNameId, $deleteNameId, $personId)
    {
        if ($this->isAjax()) {
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

            $personFilter = new PersonFilter($this->translator, $this->getHttpRequest());
            $nameFilter = new NameFilter();

            $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($deleteNameId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'nameDeletePersonName';
            $this->template->nameModalItem = $nameFilter($nameModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

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
        if ($this->isAjax()) {
            try {
                $this->nameManager->deleteByPrimaryKey($values->deleteNameId);

                $this->payload->showModal = false;

                $this->flashMessage('name_deleted', self::FLASH_SUCCESS);

                if ($values->currentNameId === $values->deleteNameId) {
                    $this->redirect('Name:default');
                } else {
                    $this->redrawControl('flashes');
                    $this->redrawControl('names');
                }
            } catch (ForeignKeyConstraintViolationException $e) {
                if ($e->getCode() === 1451) {
                    $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);

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