<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusAddNameModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 0:45
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Genus;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\NameForm;

/**
 * Trait GenusAddNameModal
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Genus
 */
class GenusAddNameModal extends Control
{
    /**
     * @param int $genusId
     *
     * @return void
     */
    public function handleGenusAddName($genusId)
    {
        $persons = $this->personSettingsManager->getAllPairs($this->translator);
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['genusAddNameForm-personId']->setItems($persons);
        $this['genusAddNameForm-_genusId']->setValue($genusId);
        $this['genusAddNameForm-genusId']->setItems($genuses)
            ->setDisabled()
            ->setDefaultValue($genusId);

        $this->template->modalName = 'genusAddName';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentGenusAddNameForm()
    {
        $formFactory = new NameForm($this->translator);

        $form = $formFactory->create();

        $form->addHidden('_genusId');
        $form->onAnchor[] = [$this, 'genusAddNameFormAnchor'];
        $form->onValidate[] = [$this, 'genusAddNameFormValidate'];
        $form->onSuccess[] = [$this, 'genusAddNameFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function genusAddNameFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function genusAddNameFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->translator);

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->validate();

        $genuses = $this->genusManager->getPairsCached('surname');

        $genusHiddenControl = $form->getComponent('_genusId');

        $genusControl = $form->getComponent('genusId');
        $genusControl->setItems($genuses)
            ->setValue($genusHiddenControl->getValue())
            ->validate();

        $form->removeComponent($genusHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function genusAddNameFormNameFormSuccess(Form $form, ArrayHash $values)
    {
        $this->nameManager->add($values);

        $genusNamePersons = $this->nameFacade->getByGenusIdCached($values->genusId);

        $this->template->genusNamePersons = $genusNamePersons;

        $this->payload->showModal = false;

        $this->flashMessage('name_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('genus_name_persons');
    }
}
