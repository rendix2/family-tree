<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusAddNameModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 0:45
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Genus;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\NameForm;

/**
 * Trait GenusAddNameModal
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Genus
 */
trait GenusAddNameModal
{
    /**
     * @param int $genusId
     *
     * @return void
     */
    public function handleAddName($genusId)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());
        $genuses = $this->genusManager->getPairsCached('surname');

        $this['addNameForm-personId']->setItems($persons);
        $this['addNameForm-_genusId']->setValue($genusId);
        $this['addNameForm-genusId']->setItems($genuses)->setDisabled()->setDefaultValue($genusId);

        $this->template->modalName = 'addName';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddNameForm()
    {
        $formFactory = new NameForm($this->getTranslator());

        $form = $formFactory->create();

        $form->addHidden('_genusId');
        $form->onAnchor[] = [$this, 'addNameFormAnchor'];
        $form->onValidate[] = [$this, 'addNameFormValidate'];
        $form->onSuccess[] = [$this, 'saveNameForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addNameFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addNameFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->validate();

        $genuses = $this->genusManager->getPairsCached('surname');

        $genusHiddenControl = $form->getComponent('_genusId');

        $genusControl = $form->getComponent('genusId');
        $genusControl->setItems($genuses)->setValue($genusHiddenControl->getValue())
            ->validate();

        $form->removeComponent($genusHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveNameForm(Form $form, ArrayHash $values)
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
