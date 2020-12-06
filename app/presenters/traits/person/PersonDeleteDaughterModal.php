<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteDaughterModal.php
 * User: Tomáš Babický
 * Date: 05.11.2020
 * Time: 15:57
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteDaughterModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteDaughterModal
{
    /**
     * @param int $personId
     * @param int $daughterId
     */
    public function handlePersonDeleteDaughter($personId, $daughterId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->isAjax()) {
            $this['personDeleteDaughterForm']->setDefaults(
                [
                    'personId' => $personId,
                    'daughterId' => $daughterId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $daughterModalItem = $this->personManager->getByPrimaryKey($daughterId);

            $this->template->modalName = 'personDeleteDaughter';
            $this->template->personModalItem = $personFilter($personModalItem);
            $this->template->daughterModalItem = $personFilter($daughterModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteDaughterForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'personDeleteDaughterFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('daughterId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteDaughterFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $parent = $this->personManager->getByPrimaryKey($values->personId);

            if ($parent->gender === 'm') {
                $this->personManager->updateByPrimaryKey($values->daughterId, ['fatherId' => null,]);
            } elseif ($parent->gender === 'f') {
                $this->personManager->updateByPrimaryKey($values->daughterId, ['motherId' => null,]);
            }

            $daughters = $this->personManager->getDaughtersByPersonCached($parent);

            $this->template->daughters = $daughters;

            $this->payload->showModal = false;

            $this->flashMessage('person_daughter_deleted');

            $this->redrawControl('daughters');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
