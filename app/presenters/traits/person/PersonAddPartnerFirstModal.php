<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPartnerFirstModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:20
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\RelationForm;

/**
 * Trait PersonAddPartnerFirstModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddPartnerFirstModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handleAddPartnerFirst($personId) 
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $this['addPartnerFirstForm-maleId']->setItems($persons);
        $this['addPartnerFirstForm-_femaleId']->setDefaultValue($personId);
        $this['addPartnerFirstForm-femaleId']->setItems($persons)->setDisabled()->setDefaultValue($personId);

        $this->template->modalName = 'addPartnerFirst';
        
        $this->payload->showModal = true;
        
        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddPartnerFirstForm()
    {
        $formFactory = new RelationForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_femaleId');
        $form->onAnchor[] = [$this, 'anchorAddPartnerFirstForm'];
        $form->onValidate[] = [$this, 'validateAddPartnerFirstForm'];
        $form->onSuccess[] = [$this, 'saveAddPartnerFirstForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function anchorAddPartnerFirstForm()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function validateAddPartnerFirstForm(Form $form)
    {
        $maleControl = $form->getComponent('maleId');

        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $maleControl->setItems($persons);
        $maleControl->validate();

        $femaleControl = $form->getComponent('femaleId');
        $femaleHiddenControl = $form->getComponent('_femaleId');

        $femaleControl->setItems($persons);
        $femaleControl->setValue($femaleHiddenControl->getValue());
        $femaleControl->validate();
        
        $form->removeComponent($femaleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveAddPartnerFirstForm(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $this->flashMessage('relation_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}