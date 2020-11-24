<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
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
 * Trait SourceListDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Source
 */
trait SourceListDeleteModal
{
    /**
     * @param int $sourceId
     */
    public function handleListDeleteItem($sourceId)
    {
        if ($this->isAjax()) {
            $this['listDeleteForm']->setDefaults(['sourceId' => $sourceId]);

            $sourceFilter = new SourceFilter();

            $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

            $this->template->modalName = 'listDeleteItem';
            $this->template->sourceModalItem = $sourceFilter($sourceModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentListDeleteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create($this, 'listDeleteFormOk');
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function listDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->sourceManager->deleteByPrimaryKey($values->sourceId);

            $this->flashMessage('source_was_deleted', self::FLASH_SUCCESS);

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