<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteRelationParentModal.php
 * User: Tomáš Babický
 * Date: 27.10.2020
 * Time: 2:18
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteRelationParentModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteRelationParentModal
{
    /**
     * @param int $personId
     * @param int $relationId
     */
    public function handleDeleteParentsRelationItem($personId, $relationId)
    {
        if ($this->isAjax()) {
            $this['deleteParentsRelationForm']->setDefaults(
                [
                    'relationId' => $relationId,
                    'personId' => $personId
                ]
            );

            $relationModalItem = $this->relationFacade->getByPrimaryKey($relationId);

            $this->template->modalName = 'deleteParentsRelationItem';
            $this->template->relationModalItem = $relationModalItem;

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteParentsRelationForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteParentsRelationFormOk');

        $form->addHidden('relationId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteParentsRelationFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->relationManager->deleteByPrimaryKey($values->relationId);

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $this->prepareParentsRelations($person->father, $person->mother);

            $this->template->modalName = 'deleteParentsRelationItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('father_relations');
            $this->redrawControl('mother_relations');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
