<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceTypeFromEditModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:33
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class SourceTypeDeleteSourceTypeFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\SourceType
 */
class SourceTypeDeleteSourceTypeFromEditModal extends \Nette\Application\UI\Control
{
    /**
     * @param int $sourceTypeId
     */
    public function handleSourceTypeDeleteSourceTypeFromEdit($sourceTypeId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
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
        $presenter = $this->presenter;

        try {
            $this->sourceTypeManager->deleteByPrimaryKey($values->sourceTypeId);

            $this->flashMessage('source_type_deleted', BasePresenter::FLASH_SUCCESS);

            $this->redirect(':default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                $this->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
