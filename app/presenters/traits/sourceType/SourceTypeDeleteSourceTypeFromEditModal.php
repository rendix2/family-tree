<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\SourceType;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\SourceTypeFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait SourceDeleteSourceFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\SourceType
 */
trait SourceTypeDeleteSourceTypeFromEditModal
{
    /**
     * @param int $sourceTypeId
     */
    public function handleSourceTypeDeleteSourceTypeFromEdit($sourceTypeId)
    {
        if ($this->isAjax()) {
            $this['sourceTypeDeleteSourceTypeFromEditForm']->setDefaults(['sourceTypeId' => $sourceTypeId]);

            $sourceTypeModalItem = $this->sourceTypeManager->getByPrimaryKeyCached($sourceTypeId);

            $sourceTypeFilter = $this->sourceTypeFilter;

            $this->template->modalName = 'sourceTypeDeleteSourceTypeFromEdit';
            $this->template->sourceTypeModalItem = $sourceTypeFilter($sourceTypeModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeDeleteSourceTypeFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'sourceTypeDeleteSourceTypeFromEditFormYesOnClick'], true);

        $form->addHidden('sourceTypeId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function sourceTypeDeleteSourceTypeFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->sourceTypeManager->deleteByPrimaryKey($values->sourceTypeId);

            $this->flashMessage('source_type_deleted', self::FLASH_SUCCESS);

            $this->redirect(':default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);

                $this->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }


    }
}
