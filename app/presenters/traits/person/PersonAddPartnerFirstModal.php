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
    public function handlePersonAddPartnerFirst($personId)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $this['personAddPartnerFirstForm-maleId']->setItems($persons);
        $this['personAddPartnerFirstForm-_femaleId']->setDefaultValue($personId);
        $this['personAddPartnerFirstForm-femaleId']->setItems($persons)->setDisabled()->setDefaultValue($personId);

        $this->template->modalName = 'personAddPartnerFirst';
        
        $this->payload->showModal = true;
        
        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPartnerFirstForm()
    {
        $formFactory = new RelationForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_femaleId');
        $form->onAnchor[] = [$this, 'personAddPartnerFirstFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPartnerFirstFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPartnerFirstFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPartnerFirstFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPartnerFirstFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($persons)
            ->validate();

        $femaleHiddenControl = $form->getComponent('_femaleId');

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($persons);
        $femaleControl->setValue($femaleHiddenControl->getValue())
            ->validate();

        $form->removeComponent($femaleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPartnerFirstFormSuccess(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $this->prepareRelations($values->femaleId);

        $this->payload->showModal = false;

        $this->flashMessage('relation_added', self::FLASH_SUCCESS);

        $this->redrawControl('relation_males');
        $this->redrawControl('flashes');
    }
}