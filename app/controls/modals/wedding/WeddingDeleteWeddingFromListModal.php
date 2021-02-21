<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingDeleteWeddingFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:26
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class WeddingDeleteWeddingFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingDeleteWeddingFromListModal extends Control
{
    /**
     * @param int $weddingId
     */
    public function handleWeddingDeleteWeddingFromList($weddingId)
    {
        if ($this->isAjax()) {
            $this['weddingDeleteWeddingFromListForm']->setDefaults(['weddingId' => $weddingId]);

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $weddingFilter = $this->weddingFilter;

            $this->template->modalName = 'weddingDeleteWeddingFromList';
            $this->template->weddingModalItem = $weddingFilter($weddingModalItem);

            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingDeleteWeddingFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'weddingDeleteWeddingFromListFormYesOnClick']);
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function weddingDeleteWeddingFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $this->flashMessage('wedding_deleted', self::FLASH_SUCCESS);

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
