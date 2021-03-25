<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class TownDeleteTownFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteTownFromEditModal extends Control
{
    /**
     * @param int $townId
     */
    public function handleTownDeleteTownFromEdit($townId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['townDeleteTownFromEditForm']->setDefaults(['townId' => $townId]);

            $townFilter = $this->townFilter;

            $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);

            $presenter->template->modalName = 'townDeleteTownFromEdit';
            $presenter->template->townModalItem = $townFilter($townModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteTownFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'townDeleteTownFromEditFormYesOnClick'], true);

        $form->addHidden('townId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteTownFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->townManager->deleteByPrimaryKey($values->townId);

            $this->flashMessage('town_deleted', BasePresenter::FLASH_SUCCESS);

            $this->redirect('Town:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
                $presenter->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
