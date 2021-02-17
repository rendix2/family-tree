<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonGravedModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 19:35
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait TownDeletePersonGravedModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait TownDeletePersonGravedModal
{

    /**
     * @param int $townId
     * @param int $personId
     */
    public function handleTownDeleteGravedPerson($townId, $personId)
    {
        if ($this->isAjax()) {
            $this['townDeleteGravedPersonForm']->setDefaults(
                [
                    'personId' => $personId,
                    'townId' => $townId
                ]
            );

            $personFilter = $this->personFilter;
            $townFilter = $this->townFilter;

            $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'townDeleteGravedPerson';
            $this->template->townModalItem = $townFilter($townModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteGravedPersonForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'townDeleteGravedPersonFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('townId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteGravedPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->personId, ['gravedTownId' => null]);

            $gravedPersons = $this->personSettingsManager->getByGravedTownId($values->personId);

            $this->template->gravedPersons = $gravedPersons;

            $this->payload->showModal = false;

            $this->flashMessage('person_saved', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('graved_persons');
        } else {
            $this->redirect('Person:edit', $values->townId);
        }
    }
}
