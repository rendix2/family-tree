<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait GenusEditDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait TownDeleteEditModal
{
    /**
     * @param int $townId
     */
    public function handleEditDelete($townId)
    {
        if ($this->isAjax()) {
            $this['editDeleteForm']->setDefaults(['townId' => $townId]);

            $townFilter = new TownFilter();

            $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);

            $this->template->modalName = 'editDelete';
            $this->template->townModalItem = $townFilter($townModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentEditDeleteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create([$this, 'editDeleteFormYesOnClick'], true);

        $form->addHidden('townId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->townManager->deleteByPrimaryKey($values->townId);

            $this->flashMessage('town_was_deleted', self::FLASH_SUCCESS);

            $this->redirect('Town:default');
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
