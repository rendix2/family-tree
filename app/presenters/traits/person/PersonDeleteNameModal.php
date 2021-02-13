<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteNameModal.php
 * User: Tomáš Babický
 * Date: 29.10.2020
 * Time: 23:34
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteNameModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteNameModal
{
    /**
     * @param int $personId
     * @param int $nameId
     */
    public function handlePersonDeletePersonName($personId, $nameId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->isAjax()) {
            $this['personDeleteNameForm']->setDefaults(
                [
                    'nameId' => $nameId,
                    'personId' => $personId
                ]
            );

            $personFilter = new PersonFilter($this->translator, $this->getHttpRequest());
            $nameFilter = new NameFilter();

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($nameId);

            $this->template->modalName = 'personDeleteName';
            $this->template->personModalItem = $personFilter($personModalItem);
            $this->template->nameModalItem = $nameFilter($nameModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteNameForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'personDeleteNameFormYesOnClick']);

        $form->addHidden('personId');
        $form->addHidden('nameId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteNameFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->nameManager->deleteByPrimaryKey($values->nameId);

            $names = $this->nameManager->getByPersonId($values->personId);

            $this->template->names = $names;

            $this->payload->showModal = false;

            $this->flashMessage('name_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('names');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
