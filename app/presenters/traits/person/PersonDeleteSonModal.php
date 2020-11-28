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
        if ($this->isAjax()) {
            $this['deletePersonSonForm']->setDefaults(
                [
                    'personId' => $personId,
                    'sonId' => $sonId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $sonModalItem = $this->personFacade->getByPrimaryKeyCached($sonId);

            $this->template->modalName = 'deleteSonItem';
            $this->template->personModalItem = $personFilter($personModalItem);
            $this->template->sonModalItem = $personFilter($sonModalItem);

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
            $parent = $this->personManager->getByPrimaryKey($values->personId);

            if ($parent->gender === 'm') {
                $this->personManager->updateByPrimaryKey($values->sonId, ['fatherId' => null,]);
            } elseif ($parent->gender === 'f') {
                $this->personManager->updateByPrimaryKey($values->sonId, ['motherId' => null,]);
            }

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $sons = $this->personManager->getSonsByPersonCached($person);

            $this->template->sons = $sons;

            $this->payload->showModal = false;

            $this->flashMessage('person_son_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('sons');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
