<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonBirthModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 19:34
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;


use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait TownDeletePersonBirthModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait TownDeletePersonBirthModal
{
    /**
     * @param int $townId
     * @param int $personId
     */
    public function handleTownDeleteBirthPerson($townId, $personId)
    {
        if ($this->isAjax()) {
            $this['townDeleteBirthPersonForm']->setDefaults(
                [
                    'personId' => $personId,
                    'townId' => $townId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $townFilter = new TownFilter();

            $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'townDeleteBirthPerson';
            $this->template->townModalItem = $townFilter($townModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteBirthPersonForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create([$this, 'townDeleteBirthPersonFormYesOnClick']);

        $form->addHidden('personId');
        $form->addHidden('townId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteBirthPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->personId, ['birthTownId' => null]);

            $birthPersons = $this->personManager->getByBirthTownId($values->personId);

            $this->template->birthPersons = $birthPersons;

            $this->payload->showModal = false;

            $this->flashMessage('person_saved', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('birth_persons');
        } else {
            $this->redirect('Person:edit', $values->townId);
        }
    }
}
