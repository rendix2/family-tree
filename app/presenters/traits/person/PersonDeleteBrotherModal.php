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
        $this['deletePersonBrotherForm']->setDefaults(
            [
                'personId' => $personId,
                'brotherId' => $brotherId
            ]
        );

        $brotherModalItem = $this->manager->getByPrimaryKey($brotherId);

        $this->template->brotherModalItem = $brotherModalItem;
        $this->template->modalName = 'deleteBrotherItem';

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));

        if ($this->isAjax()) {
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
            $this->manager->updateByPrimaryKey($values->brotherId,
                [
                    'fatherId' => null,
                    'motherId' => null
                ]
            );

            $brother = $this->manager->getByPrimaryKey($values->brotherId);
            $father = $this->manager->getByPrimaryKey($brother->fatherId);
            $mother = $this->manager->getByPrimaryKey($brother->motherId);

            $this->prepareBrothersAndSisters($values->brotherId, $father, $mother);

            $this->template->brotherModalItem = $brother;
            $this->template->modalName = 'deleteBrotherItem';
            $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('brothers');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}