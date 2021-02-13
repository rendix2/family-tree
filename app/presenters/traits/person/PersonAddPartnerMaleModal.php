<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPartnerMaleModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:20
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\RelationForm;

/**
 * Trait PersonAddPartnerMaleModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddPartnerMaleModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPartnerMale($personId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $males = $this->personSettingsManager->getMalesPairsCached($this->translator);
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

        $this['personAddPartnerMaleForm-maleId']->setItems($males);
        $this['personAddPartnerMaleForm-_femaleId']->setDefaultValue($personId);
        $this['personAddPartnerMaleForm-femaleId']->setItems($persons)
            ->setDisabled()
            ->setDefaultValue($personId);

        $this->template->modalName = 'personAddPartnerMale';
        
        $this->payload->showModal = true;
        
        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPartnerMaleForm()
    {
        $formFactory = new RelationForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_femaleId');
        $form->onAnchor[] = [$this, 'personAddPartnerMaleFormFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPartnerMaleFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPartnerMaleFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPartnerMaleFormFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPartnerMaleFormValidate(Form $form)
    {
        $males = $this->personManager->getMalesPairsCached($this->translator);
        $persons = $this->personManager->getAllPairsCached($this->translator);

        $maleControl = $form->getComponent('maleId');
        $maleControl->setItems($males)
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
    public function personAddPartnerMaleFormSuccess(Form $form, ArrayHash $values)
    {
        $this->relationManager->add($values);

        $this->prepareRelations($values->femaleId);

        $this->payload->showModal = false;

        $this->flashMessage('relation_added', self::FLASH_SUCCESS);

        $this->redrawControl('relation_males');
        $this->redrawControl('flashes');
    }
}