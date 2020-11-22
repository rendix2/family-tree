<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteWeddingParentModal.php
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
 * Trait PersonDeleteWeddingParentModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteWeddingParentModal
{
    /**
     * @param int $personId
     * @param int $weddingId
     */
    public function handleDeleteParentsWeddingItem($personId, $weddingId)
    {
        if ($this->isAjax()) {
            $this['deleteParentsWeddingForm']->setDefaults(
                [
                    'weddingId' => $weddingId,
                    'personId' => $personId
                ]
            );

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $this->template->modalName = 'deleteParentsWeddingItem';
            $this->template->weddingModalItem = $weddingModalItem;

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteParentsWeddingForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteParentsWeddingFormOk');

        $form->addHidden('weddingId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteParentsWeddingFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);
            $father = $person->father;
            $mother = $person->mother;

            $this->prepareParentsWeddings($father, $mother);

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('father_weddings');
            $this->redrawControl('mother_weddings');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
