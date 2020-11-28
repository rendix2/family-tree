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
    public function handleDeleteBrotherItem($personId, $brotherId)
    {
        if ($this->isAjax()) {
            $this['deletePersonBrotherForm']->setDefaults(
                [
                    'personId' => $personId,
                    'brotherId' => $brotherId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $brotherModalItem = $this->personManager->getByPrimaryKey($brotherId);

            $this->template->modalName = 'deleteBrotherItem';
            $this->template->brotherModalItem = $personFilter($brotherModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeletePersonBrotherForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonBrotherFormOk');

        $form->addHidden('personId');
        $form->addHidden('brotherId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonBrotherFormOk(SubmitButton $submitButton, ArrayHash $values)
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