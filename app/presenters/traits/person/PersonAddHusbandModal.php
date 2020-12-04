<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddHusbandModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:19
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\WeddingForm;

/**
 * Trait PersonAddHusbandModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddHusbandModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddHusband($personId)
    {
        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();
        $addresses = $this->addressFacade->getPairs();

        $this['personAddHusbandForm-husbandId']->setItems($males);
        $this['personAddHusbandForm-_wifeId']->setDefaultValue($personId);
        $this['personAddHusbandForm-wifeId']->setItems($females)->setDisabled()->setDefaultValue($personId);
        $this['personAddHusbandForm-townId']->setItems($towns);
        $this['personAddHusbandForm-addressId']->setItems($addresses);

        $this->template->modalName = 'personAddHusband';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddHusbandForm()
    {
        $formFactory = new WeddingForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_wifeId');
        $form->onAnchor[] = [$this, 'personAddHusbandFormAnchor'];
        $form->onValidate[] = [$this, 'personAddHusbandFormValidate'];
        $form->onSuccess[] = [$this, 'personAddHusbandFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddHusbandFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddHusbandFormValidate(Form $form)
    {
        $males = $this->personManager->getMalesPairs($this->getTranslator());

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($males)
            ->validate();

        $females = $this->personManager->getFemalesPairs($this->getTranslator());

        $wifeHiddenControl = $form->getComponent('_wifeId');

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($females);
        $wifeControl->setValue($wifeHiddenControl->getValue())
            ->validate();

        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->getPairs();

        $townControl = $form->getComponent('addressId');
        $townControl->setItems($addresses)
            ->validate();

        $form->removeComponent($wifeHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddHusbandFormSuccess(Form $form, ArrayHash $values)
    {
        $this->weddingManager->add($values);

        $person = $this->personFacade->getByPrimaryKey($this->getParameter('id'));

        $this->prepareWeddings($values->wifeId);
        $this->prepareParentsWeddings($person->father, $person->mother);

        $this->payload->showModal = false;

        $this->flashMessage('wedding_added', self::FLASH_SUCCESS);

        $this->redrawControl('husbands');
        $this->redrawControl('mother_husbands');
        $this->redrawControl('flashes');
    }
}
