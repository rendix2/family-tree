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

        if ($this->isAjax()) {
            $this['sourceDeleteSourceFromListForm']->setDefaults(['sourceId' => $sourceId]);

            $sourceFilter = $this->sourceFilter;

            $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

            $this->template->modalName = 'sourceDeleteSourceFromList';
            $this->template->sourceModalItem = $sourceFilter($sourceModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
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

            $this->flashMessage('source_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $this->redrawControl('flashes');
        }
    }
}
