<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSourceModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 2:23
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteSourceModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteSourceModal
{
    /**
     * @param int $personId
     * @param int $sourceId
     */
    public function handleDeleteSourceItem($personId, $sourceId)
    {
        $this->template->modalName = 'deleteSourceItem';

        $this['deletePersonSourceForm']->setDefaults(
            [
                'personId' => $personId,
                'sourceId' => $sourceId
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
    protected function createComponentDeletePersonSourceForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonSourceFormOk');

        $form->addHidden('personId');
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonSourceFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->sourceManager->deleteByPrimaryKey($values->sourceId);

            $sources = $this->sourceFacade->getByPersonId($values->personId);

            $this->template->sources = $sources;
            $this->template->modalName = 'deleteSourceItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('sources');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}
