<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPartnerSecondModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:20
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\RelationForm;

/**
 * Trait PersonAddPartnerSecondModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddPartnerSecondModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handleAddPartnerSecond($personId)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $this['addPartnerSecondForm-_maleId']->setDefaultValue($personId);
        $this['addPartnerSecondForm-maleId']->setItems($persons)->setDisabled()->setDefaultValue($personId);
        $this['addPartnerSecondForm-femaleId']->setItems($persons);

        $this->template->modalName = 'addPartnerSecond';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddPartnerSecondForm()
    {
        $formFactory = new RelationForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_maleId');
        $form->onAnchor[] = [$this, 'anchorAddPartnerSecondForm'];
        $form->onValidate[] = [$this, 'validateAddPartnerSecondForm'];
        $form->onSuccess[] = [$this, 'saveAddPartnerSecondForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function anchorAddPartnerSecondForm()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function validateAddPartnerSecondForm(Form $form)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $maleHiddenControl = $form->getComponent('_maleId');

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($persons);
        $maleControl->setValue($maleHiddenControl->getValue());
        $maleControl->validate();

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($persons);
        $femaleControl->validate();

        $form->removeComponent($maleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveAddPartnerSecondForm(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $this->prepareRelations($values->maleId);

        $this->payload->showModal = false;

        $this->flashMessage('relation_added', self::FLASH_SUCCESS);

        $this->redrawControl('relation_females');
        $this->redrawControl('flashes');
    }
}
