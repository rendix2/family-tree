<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeletePersonDeathModal.php
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

trait TownDeletePersonDeathModal
{
    /**
     * @param int $townId
     * @param int $personId
     */
    public function handleTownDeleteDeathPerson($townId, $personId)
    {
        if ($this->isAjax()) {
            $this['townDeleteDeathPersonForm']->setDefaults(
                [
                    'personId' => $personId,
                    'townId' => $townId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $townFilter = new TownFilter();

            $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'townDeleteDeathPerson';
            $this->template->townModalItem = $townFilter($townModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteDeathPersonForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
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
        if ($this->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->personId, ['deathTownId' => null]);

            $deathPersons = $this->personManager->getByDeathTownId($values->personId);

            $this->template->deathPersons = $deathPersons;

            $this->payload->showModal = false;

            $this->flashMessage('person_saved', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('death_persons');
        } else {
            $this->redirect('Person:edit', $values->townId);
        }
    }
}
