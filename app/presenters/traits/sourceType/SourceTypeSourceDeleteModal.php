<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeSourceDeleteModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 15:08
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\SourceType;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait SourceTypeSourceDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\SourceType
 */
trait SourceTypeSourceDeleteModal
{
    /**
     * @param int $sourceTypeId
     * @param int $sourceId
     */
    public function handleDeleteSourceItem($sourceTypeId, $sourceId)
    {
        $this->template->modalName = 'deleteSourceItem';

        $this['deleteSourceForm']->setDefaults(
            [
                'sourceTypeId' => $sourceTypeId,
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
    protected function createComponentDeleteSourceForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteSourceFormOk');

        $form->addHidden('sourceTypeId');
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteSourceFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->sourceManager->deleteByPrimaryKey($values->sourceId);

            $sources = $this->sourceFacade->getBySourceTypeId($values->sourceTypeId);

            $this->template->sources = $sources;
            $this->template->modalName = 'deleteSourceItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('sources');
        } else {
            $this->redirect(':edit', $values->sourceTypeId);
        }
    }
}
