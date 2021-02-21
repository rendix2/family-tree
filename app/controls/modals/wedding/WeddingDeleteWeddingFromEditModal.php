<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingDeleteWeddingFromEditModal.php
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
 * Class WeddingDeleteWeddingFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingDeleteWeddingFromEditModal extends Control
{
    /**
     * @param int $weddingId
     */
    public function handleWeddingDeleteWeddingFromEdit($weddingId)
    {
        if ($this->isAjax()) {
            $this['weddingDeleteWeddingFromEditForm']->setDefaults(['weddingId' => $weddingId]);

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $weddingFilter = $this->weddingFilter;

            $this->template->modalName = 'weddingDeleteWeddingFromEdit';
            $this->template->weddingModalItem = $weddingFilter($weddingModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingDeleteWeddingFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'weddingDeleteWeddingFromEditFormYesOnClick'], true);
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function weddingDeleteWeddingFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $this->flashMessage('wedding_deleted', self::FLASH_SUCCESS);

            $this->redirect('Wedding:default');
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
