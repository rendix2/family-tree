<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonDeathModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 19:35
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownDeletePersonDeathModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeletePersonDeathModal extends Control
{
    /**
     * @param int $townId
     * @param int $personId
     */
    public function handleTownDeleteDeathPerson($townId, $personId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['townDeleteDeathPersonForm']->setDefaults(
                [
                    'personId' => $personId,
                    'townId' => $townId
                ]
            );

            $personFilter = $this->personFilter;
            $townFilter = $this->townFilter;

            $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'townDeleteDeathPerson';
            $this->template->townModalItem = $townFilter($townModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteDeathPersonForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'townDeleteDeathPersonFormYesOnClick']);

        $form->addHidden('personId');
        $form->addHidden('townId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteDeathPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->personId, ['deathTownId' => null]);

            $deathPersons = $this->personSettingsManager->getByDeathTownId($values->personId);

            $this->template->deathPersons = $deathPersons;

            $presenter->payload->showModal = false;

            $this->flashMessage('person_saved', BasePresenter::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('death_persons');
        } else {
            $this->redirect('Person:edit', $values->townId);
        }
    }
}
