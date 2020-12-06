<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPartnerFemaleModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:20
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\RelationForm;

/**
 * Trait PersonAddPartnerFemaleModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddPartnerFemaleModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPartnerFemale($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $females = $this->personManager->getFemalesPairsCached($this->getTranslator());

        $this['personAddPartnerFemaleForm-_maleId']->setDefaultValue($personId);
        $this['personAddPartnerFemaleForm-maleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this['personAddPartnerFemaleForm-femaleId']->setItems($females);

        $this->template->modalName = 'personAddPartnerFemale';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPartnerFemaleForm()
    {
        $formFactory = new RelationForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_maleId');
        $form->onAnchor[] = [$this, 'personAddPartnerFemaleFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPartnerFemaleFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPartnerFemaleFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPartnerFemaleFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPartnerFemaleFormValidate(Form $form)
    {
        $females = $this->personManager->getFemalesPairsCached($this->getTranslator());
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $maleHiddenControl = $form->getComponent('_maleId');

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($persons);
        $maleControl->setValue($maleHiddenControl->getValue())
            ->validate();

        $femaleControl = $form->getComponent('femaleId');
        $femaleControl->setItems($females)
            ->validate();

        $form->removeComponent($maleHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPartnerFemaleFormSuccess(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $this->prepareRelations($values->maleId);

        $this->payload->showModal = false;

        $this->flashMessage('relation_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('relation_females');
    }
}
