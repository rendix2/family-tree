<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddWifeModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:19
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\WeddingForm;

trait PersonAddWifeModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddWife($personId)
    {
        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();
        $addresses = $this->addressFacade->getPairs();

        $this['personAddWifeForm-_husbandId']->setDefaultValue($personId);
        $this['personAddWifeForm-husbandId']->setItems($males)->setDisabled()->setDefaultValue($personId);
        $this['personAddWifeForm-wifeId']->setItems($females);
        $this['personAddWifeForm-townId']->setItems($towns);
        $this['personAddWifeForm-addressId']->setItems($addresses);

        $this->template->modalName = 'personAddWife';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddWifeForm()
    {
        $formFactory = new WeddingForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_husbandId');
        $form->onAnchor[] = [$this, 'personAddWifeFormAnchor'];
        $form->onValidate[] = [$this, 'personAddWifeFormValidate'];
        $form->onSuccess[] = [$this, 'personAddWifeFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddWifeFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddWifeFormValidate(Form $form)
    {
        $persons = $this->personManager->getMalesPairs($this->getTranslator());

        $husbandHiddenControl = $form->getComponent('_husbandId');

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons)
            ->setValue($husbandHiddenControl->getValue())
            ->validate();

        $persons = $this->personManager->getFemalesPairs($this->getTranslator());

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($persons)
            ->validate();

        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->getPairs();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();

        $form->removeComponent($husbandHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddWifeFormSuccess(Form $form, ArrayHash $values)
    {
        $this->weddingManager->add($values);

        $person = $this->personFacade->getByPrimaryKey($this->getParameter('id'));

        $this->prepareWeddings($values->husbandId);
        $this->prepareParentsWeddings($person->father, $person->mother);

        $this->payload->showModal = false;

        $this->flashMessage('wedding_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('husbands');
        $this->redrawControl('father_wives');
    }
}
