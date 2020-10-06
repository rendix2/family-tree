<?php
/**
 *
 * Created by PhpStorm.
 * Filename: BasePresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 22:24
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\Forms\LanguageSwitcherForm;
use Rendix2\FamilyTree\App\Managers\LanguageManager;
use Translator;

/**
 * Class BasePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class BasePresenter extends Presenter
{
    /**
     * @var string
     */
    const FLASH_SUCCESS = 'success';

    /**
     * @var string
     */
    const FLASH_DANGER = 'danger';

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var LanguageManager $languageManager
     * @inject
     */
    public $languageManager;

    /**
     * BasePresenter constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->translator = new Translator('cs.CZ');
    }

    /**
     * @return void
     */
    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->setTranslator($this->translator);
    }

    /**
     * @return ITranslator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return LanguageSwitcherForm
     */
    public function createComponentLanguageSwitcher()
    {
        return new LanguageSwitcherForm($this->translator, $this->languageManager);
    }
}
