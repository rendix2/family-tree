<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Source;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait SourceDeleteSourceFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Source
 */
trait SourceDeleteSourceFromEditModal
{
    /**
     * @param int $sourceId
     */
    public function handleSourceDeleteSourceFromEdit($sourceId)
    {
        if ($this->isAjax()) {
            $this['sourceDeleteSourceFromEditForm']->setDefaults(['sourceId' => $sourceId]);

            $sourceFilter = new SourceFilter();

            $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

            $this->template->modalName = 'sourceDeleteSourceFromEdit';
            $this->template->sourceModalItem = $sourceFilter($sourceModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentSourceDeleteSourceFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        
        $form = $formFactory->create([$this, 'sourceDeleteSourceFromEditFormYesOnClick'], true);
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function sourceDeleteSourceFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->sourceManager->deleteByPrimaryKey($values->sourceId);

            $this->flashMessage('source_deleted', self::FLASH_SUCCESS);

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
