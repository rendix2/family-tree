<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteNameModal.php
 * User: Tomáš Babický
 * Date: 29.10.2020
 * Time: 23:34
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteNameModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteNameModal
{
    /**
     * @param int $personId
     * @param int $nameId
     */
    public function handleDeletePersonNameItem($personId, $nameId)
    {
        $this->template->modalName = 'deleteNameItem';

        $this['deletePersonNameForm']->setDefaults(
            [
                'nameId' => $nameId,
                'personId' => $personId
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
    protected function createComponentDeletePersonNameForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonNameFormOk');

        $form->addHidden('personId');
        $form->addHidden('nameId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonNameFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->nameManager->deleteByPrimaryKey($values->nameId);

            $names = $this->nameManager->getByPersonId($values->personId);

            $this->template->names = $names;
            $this->template->modalName = 'deleteNameItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('names');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}