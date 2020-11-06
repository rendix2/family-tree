<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSonModal.php
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
 * Trait PersonDeleteSonModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteSonModal
{
    /**
     * @param int $personId
     * @param int $sonId
     */
    public function handleDeleteSonItem($personId, $sonId)
    {
        $this['deletePersonSonForm']->setDefaults(
            [
                'personId' => $personId,
                'sonId' => $sonId
            ]
        );

        $sonModalItem = $this->manager->getByPrimaryKey($sonId);

        $this->template->sonModalItem = $sonModalItem;
        $this->template->modalName = 'deleteSonItem';

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));

        if ($this->isAjax()) {
            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeletePersonSonForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonSonFormOk');

        $form->addHidden('personId');
        $form->addHidden('sonId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonSonFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $sonModalItem = $this->manager->getByPrimaryKey($values->personId);

            $this->template->modalName = 'deleteSonItem';
            $this->template->sonModalItem = $sonModalItem;

            $this->payload->showModal = false;

            $parent = $this->manager->getByPrimaryKey($values->personId);

            if ($parent->gender === 'm') {
                $this->manager->updateByPrimaryKey($values->sonId,
                    [
                        'fatherId' => null,
                    ]
                );
            } elseif ($parent->gender === 'f') {
                $this->manager->updateByPrimaryKey($values->sonId,
                    [
                        'motherId' => null,
                    ]
                );
            }

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('sons');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}
