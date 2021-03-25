<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceDeleteSourceFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:40
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Source;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class SourceDeleteSourceFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Source
 */
class SourceDeleteSourceFromListModal extends Control
{
    /**
     * @param int $sourceId
     */
    public function handleSourceDeleteSourceFromList($sourceId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['sourceDeleteSourceFromListForm']->setDefaults(['sourceId' => $sourceId]);

            $sourceFilter = $this->sourceFilter;

            $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

            $presenter->template->modalName = 'sourceDeleteSourceFromList';
            $presenter->template->sourceModalItem = $sourceFilter($sourceModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentSourceDeleteSourceFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'sourceDeleteSourceFromListFormYesOnClick']);
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function sourceDeleteSourceFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->sourceManager->deleteByPrimaryKey($values->sourceId);

            $this->flashMessage('source_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $presenter->redrawControl('flashes');
        }
    }
}
