<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddRelationModal.php
 * User: Tomáš Babický
 * Date: 25.11.2020
 * Time: 0:56
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Relation;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\RelationForm;

/**
 * Trait AddRelationModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait AddRelationModal
{
    /**
     * @return void
     */
    public function handleRelationAddRelation()
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $this['relationAddRelationForm-maleId']->setItems($persons);
        $this['relationAddRelationForm-femaleId']->setItems($persons);

        $this->template->modalName = 'relationAddRelation';
        
        $this->payload->showModal = true;
        
        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentRelationAddRelationForm()
    {
        $formFactory = new RelationForm($this->getTranslator());
        
        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'relationAddRelationFormAnchor'];
        $form->onValidate[] = [$this, 'relationAddRelationFormValidate'];
        $form->onSuccess[] = [$this, 'relationAddRelationSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');
        
        return $form;
    }

    /**
     * @return void
     */
    public function relationAddRelationFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function relationAddRelationFormValidate(Form $form)
    {
        $maleControl = $form->getComponent('maleId');

        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $maleControl->setItems($persons)
            ->validate();

        $femaleControl = $form->getComponent('femaleId');

        $femaleControl->setItems($persons)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function relationAddRelationSuccess(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $this->payload->showModal = false;

        $this->flashMessage('relation_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
    }
}
