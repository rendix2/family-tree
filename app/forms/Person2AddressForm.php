<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2AddressForm.php
 * User: Tomáš Babický
 * Date: 07.10.2020
 * Time: 12:57
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;

/**
 * Class Person2AddressForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class Person2AddressForm
{
    /**
     * @var ITranslator $tranlator
     */
    private $translator;

    /**
     * Person2AddressForm constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addSelect('personId', $this->translator->translate('person_address_person'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('person_address_select_person'))
            ->setRequired('person_address_person_required');

        $form->addSelect('addressId', $this->translator->translate('person_address_address'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('person_address_select_address'))
            ->setRequired('person_address_address_required');

        $form->addCheckbox('untilNow', 'person_address_until_now')
            ->addCondition(Form::EQUAL, true)
            ->toggle('date-to', false);

        $form->addTbDatePicker('dateSince', 'date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'date_to')
            ->setOption('id', 'date-to')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addSubmit('send', 'save');

        return $form;
    }
}
