<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LanguageSwitcherForm.php
 * User: Tomáš Babický
 * Date: 08.09.2020
 * Time: 18:34
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\LanguageManager;

/**
 * Class LanguageSwitcherForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class LanguageSwitcherForm extends Control
{
    /**
     * @var LanguageManager $languageManager
     */
    private $languageManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * LanguageSwitcherForm constructor.
     *
     * @param ITranslator $translator
     * @param LanguageManager $languageManager
     */
    public function __construct(ITranslator $translator, LanguageManager $languageManager)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->languageManager = $languageManager;
    }

    /**
     * @return void
     */
    public function render()
    {
        $languages = $this->languageManager->getAllFluent()->fetchPairs('langName', 'langName');

        $this['form-language']->setItems($languages);

        $sep = DIRECTORY_SEPARATOR;


        $this->template->setFile(__DIR__ . $sep . 'templates' . $sep . 'languageSwitcherForm.latte');
        $this->template->setTranslator($this->translator);
        $this->template->render();
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addSelect('language', 'language');
        $form->addSubmit('send', 'change');

        $form->onSuccess[] = [$this, 'changeLanguage'];
        $form->onRender[]  = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function changeLanguage(Form $form, ArrayHash $values)
    {
        bdump($values);
    }
}