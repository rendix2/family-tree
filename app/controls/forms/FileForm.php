<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileForm.php
 * User: Tomáš Babický
 * Date: 15.12.2020
 * Time: 11:03
 */

namespace Rendix2\FamilyTree\App\Controls\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;

/**
 * Class FileForm
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
 */
class FileForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * FileForm constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    public function __destruct()
    {
        $this->translator = null;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addSelect('personId', $this->translator->translate('person_person'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('file_select_person'))
            ->setRequired('file_person_required');

        $form->addUpload('file', 'file_file');

        $form->addTextArea('description', 'file_description',null, 5);

        $form->addSubmit('send', 'file_save_file');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return  $form;
    }
}
