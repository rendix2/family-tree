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
use Rendix2\FamilyTree\App\Filters\GenusFilter;
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
     * @param int $genusId
     * @param int $deleteGenusPersonId
     */
    public function handleDeleteGenusItem($personId, $genusId, $deleteGenusPersonId)
    {
        if ($this->isAjax()) {
            $this['deletePersonGenusForm']->setDefaults(
                [
                    'genusId' => $genusId,
                    'personId' => $personId,
                    'deleteGenusPersonId' => $deleteGenusPersonId,
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $genusFilter = new GenusFilter();

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($deleteGenusPersonId);
            $genusModalItem = $this->genusManager->getByPrimaryKeyCached($genusId);

            $this->template->personModalItem = $personFilter($personModalItem);
            $this->template->genusModalItem = $genusFilter($genusModalItem);
            $this->template->modalName = 'deleteGenusItem';

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

        $form->addHidden('genusId');
        $form->addHidden('deleteGenusPersonId');
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
            $this->personManager->updateByPrimaryKey($values->deleteGenusPersonId, ['genusId' => null]);

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $genusPersons = [];

            if ($person->genus) {
                $genusPersons = $this->personFacade->getByGenusIdCached($person->genus->id);
            }

            $this->template->genusPersons = $genusPersons;

            $this->payload->showModal = false;

            $this->flashMessage('item_updated', self::FLASH_SUCCESS);

            if ($values->personId === $values->deleteGenusPersonId) {
                $this['form-genusId']->setDefaultValue(null);

                $this->redrawControl('formWrapper');
            }

            $this->redrawControl('flashes');
            $this->redrawControl('genus_persons');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
