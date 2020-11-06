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
    public function handleDeleteDaughterItem($personId, $daughterId)
    {
        $this['deletePersonDaughterForm']->setDefaults(
            [
                'personId' => $personId,
                'daughterId' => $daughterId
            ]
        );

        $daughterModalItem = $this->manager->getByPrimaryKey($daughterId);

        $this->template->daughterModalItem = $daughterModalItem;
        $this->template->modalName = 'deleteDaughterItem';

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));

        if ($this->isAjax()) {
            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeletePersonDaughterForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonDaughterFormOk');

        $form->addHidden('personId');
        $form->addHidden('daughterId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonDaughterFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $daughterModalItem = $this->manager->getByPrimaryKey($values->daughterId);

            $this->payload->showModal = false;

            $this->template->modalName = 'deleteDaughterItem';
            $this->template->daughterModalItem = $daughterModalItem;
            $this->template->addFilter('person', new PersonFilter($this->getTranslator()));

            $parent = $this->manager->getByPrimaryKey($values->personId);

            if ($parent->gender === 'm') {
                $this->manager->updateByPrimaryKey($values->daughterId,
                    [
                        'fatherId' => null,
                    ]
                );
            } elseif ($parent->gender === 'f') {
                $this->manager->updateByPrimaryKey($values->daughterId,
                    [
                        'motherId' => null,
                    ]
                );
            }

            $this->redrawControl('modal');
            $this->redrawControl('daughters');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}
