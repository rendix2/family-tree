<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteGenusModal.php
 * User: Tomáš Babický
 * Date: 06.11.2020
 * Time: 16:20
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteGenusModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteGenusModal
{
    /**
     * @param int $personId
     */
    public function handleDeleteGenusItem($personId)
    {
        $this['deletePersonGenusForm']->setDefaults(
            [
                'personId' => $personId,
            ]
        );

        $daughterModalItem = $this->manager->getByPrimaryKey($personId);

        $this->template->personModalItem = $daughterModalItem;
        $this->template->modalName = 'deleteGenusItem';

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));

        if ($this->isAjax()) {
            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeletePersonGenusForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonGenusFormOk');

        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonGenusFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->manager->updateByPrimaryKey($values->personId,
                [
                    'genusId' => null,
                ]
            );

            $personModalItem = $this->manager->getByPrimaryKey($values->personId);

            $genusPersons = [];

            if ($personModalItem->genusId) {
                $genusPersons = $this->manager->getByGenusId($personModalItem->genusId);
            }

            $this->template->modalName = 'deleteGenusItem';
            $this->template->personModalItem = $personModalItem;
            $this->template->genusPersons = $genusPersons;

            $this->template->addFilter('person', new PersonFilter($this->getTranslator()));

            $this->payload->showModal = false;

            $this->flashMessage('item_updated', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('genus_persons');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}
