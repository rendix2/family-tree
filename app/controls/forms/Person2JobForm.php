<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Person2JobForm.php
 * User: Tomáš Babický
 * Date: 07.10.2020
 * Time: 13:47
 */

namespace Rendix2\FamilyTree\App\Controls\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\PersonJobSettings;

/**
 * Class Person2JobForm
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
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
     * @param PersonJobSettings $personJobSettings
     * @return Form
     */
    public function create(PersonJobSettings $personJobSettings)
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addGroup('person_job_person_job');

        $form->addSelect('personId', $this->translator->translate('person_job_person'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('person_job_select_person'))
            ->setRequired('person_job_person_required')
            ->setAttribute('data-link', $personJobSettings->selectPersonHandle);

        $form->addSelect('jobId', $this->translator->translate('person_job_job'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('person_job_select_job'))
            ->setRequired('person_job_job_required')
            ->setAttribute('data-link', $personJobSettings->selectJobHandle);

        $form->addGroup('person_job_length_person_job');

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

        $form->addSubmit('send', 'person_job_save_person_job');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
