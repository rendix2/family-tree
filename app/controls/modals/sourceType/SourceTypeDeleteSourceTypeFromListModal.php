<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceTypeFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:34
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
 * Class SourceTypeDeleteSourceTypeFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\SourceType
 */
class SourceTypeDeleteSourceTypeFromListModal extends \Nette\Application\UI\Control
{
    /**
     * @param int $sourceTypeId
     */
    public function handleSourceTypeDeleteSourceTypeFromList($sourceTypeId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['sourceTypeDeleteSourceTypeFromListForm']->setDefaults(['sourceTypeId' => $sourceTypeId]);

            $sourceTypeModalItem = $this->sourceTypeManager->getByPrimaryKeyCached($sourceTypeId);

            $sourceTypeFilter = $this->sourceTypeFilter;

            $this->template->modalName = 'sourceTypeDeleteSourceTypeFromList';
            $this->template->sourceTypeModalItem = $sourceTypeFilter($sourceTypeModalItem);

            $presenter->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeDeleteSourceTypeFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'sourceTypeDeleteSourceTypeFromListFormYesOnClick']);
        $form->addHidden('sourceTypeId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function sourceTypeDeleteSourceTypeFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->sourceTypeManager->deleteByPrimaryKey($values->sourceTypeId);

            $this->flashMessage('source_type_deleted', BasePresenter::FLASH_SUCCESS);

            $this->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $this->redrawControl('flashes');
        }
    }
}
