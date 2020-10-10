<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2JobForm.php
 * User: Tomáš Babický
 * Date: 07.10.2020
 * Time: 13:47
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;

/**
 * Class Person2JobForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class Person2JobForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * Person2JobForm constructor.
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

        $form->addSelect('personId', $this->translator->translate('person_job_person'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('person_job_select_person'))
            ->setRequired('person_job_person_required');

        $form->addSelect('jobId', $this->translator->translate('person_job_job'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('person_job_select_job'))
            ->setRequired('person_job_job_required');

        $form->addCheckbox('untilNow', 'person_job_until_now')
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