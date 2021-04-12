<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:38
 */

namespace Rendix2\FamilyTree\App\Controls\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class HistoryNoteForm
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
 */
class HistoryNoteForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * AddressForm constructor.
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

        $form->addSelect('personId', $this->translator->translate('history_note_person_name'))
            ->setTranslator()
            ->setDisabled();

        $form->addTextArea('text', 'history_note_text')
            ->setAttribute('class', 'form-control tinyMCE');

        $form->addSubmit('send', 'history_note_save_history_note');
        $form->addSubmit('use', 'history_note_apply_history_note')->onClick[] = [$this, 'useNote'];

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
