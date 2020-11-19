<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:38
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class HistoryNoteForm
 *
 * @package Rendix2\FamilyTree\App\Forms
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

        $form->addSelect('personId', $this->translator->translate('note_history_person_name'))
            ->setTranslator(null)
            ->setDisabled();

        $form->addTextArea('text', 'note_history_text')
            ->setAttribute('class', 'form-control tinyMCE');

        $form->addSubmit('send', 'save');
        $form->addSubmit('use', 'note_history_apply_note_history')->onClick[] = [$this, 'useNote'];

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
