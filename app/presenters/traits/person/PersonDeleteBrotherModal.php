<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteBrotherModal.php
 * User: Tomáš Babický
 * Date: 05.11.2020
 * Time: 15:56
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteBrotherModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteBrotherModal
{
    /**
     * @param int $personId
     * @param int $brotherId
     */
    public function handlePersonDeleteBrother($personId, $brotherId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->isAjax()) {
            $this['personDeleteBrotherForm']->setDefaults(
                [
                    'personId' => $personId,
                    'brotherId' => $brotherId
                ]
            );

            $personFilter = $this->personFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $brotherModalItem = $this->personSettingsManager->getByPrimaryKeyCached($brotherId);

            $this->template->modalName = 'personDeleteBrother';
            $this->template->brotherModalItem = $personFilter($brotherModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteBrotherForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'personDeleteBrotherFormYesOnClick']);

        $form->addHidden('personId');
        $form->addHidden('brotherId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteBrotherFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->brotherId,
                [
                    'fatherId' => null,
                    'motherId' => null
                ]
            );

            $brother = $this->personFacade->getByPrimaryKeyCached($values->brotherId);

            $this->prepareBrothersAndSisters($values->brotherId, $brother->father, $brother->mother);

            $this->payload->showModal = false;

            $this->flashMessage('person_brother_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('brothers');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}