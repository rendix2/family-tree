<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeletePersonFromEditModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 16:00
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait PersonDeletePersonFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeletePersonFromEditModal
{
    /**
     * @param int $personId
     * @param int $deletePersonId
     */
    public function handlePersonDeletePersonFromEdit($personId, $deletePersonId)
    {
        if ($this->isAjax()) {
            $this['personDeletePersonFromEditForm']->setDefaults(
                [
                    'deletePersonId' => $deletePersonId,
                    'personId' => $personId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'personDeletePersonFromEdit';
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeletePersonFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'personDeletePersonFromEditFormYesOnClick'], true);
        $form->addHidden('deletePersonId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeletePersonFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->personManager->deleteByPrimaryKey($values->personId);
            
            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }

        if ($values->personId === $values->deletePersonId) {
            $this->redirect(':default');
        } else {
            $this->redrawControl();
        }
    }
}
