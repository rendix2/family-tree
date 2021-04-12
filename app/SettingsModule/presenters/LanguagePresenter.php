<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LnaguagePresenter.php
 * User: Tomáš Babický
 * Date: 09.02.2021
 * Time: 22:49
 */

namespace Rendix2\FamilyTree\SettingsModule\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Model\Managers\LanguageManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class LanguagePresenter
 *
 * @package Rendix2\FamilyTree\SettingsModule\App\Presenters
 */
class LanguagePresenter extends BasePresenter
{
    CONST SETTINGS_LANGUAGE = 'settings_language';

    /**
     * @var LanguageManager $languageManager
     */
    private $languageManager;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * SettingsPresenter constructor.
     *
     * @param LanguageManager $languageManager
     * @param IStorage $storage
     */
    public function __construct(LanguageManager $languageManager, IStorage $storage)
    {
        parent::__construct();

        $this->cache = new Cache($storage, self::class);
        $this->languageManager = $languageManager;
    }

    /**
     * @return void
     */
    public function actionDefault()
    {
        $this['languageForm-'. self::SETTINGS_LANGUAGE]->setItems(
            $this->languageManager
                ->select()
                ->getManager()->pairsForSelect()
        );

        $this['languageForm']->setDefaults(
            [
                self::SETTINGS_LANGUAGE => $this->getHttpRequest()->getCookie(self::SETTINGS_LANGUAGE)
            ]
        );
    }

    /**
     * @return Form
     */
    protected function createComponentLanguageForm()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addSelect(self::SETTINGS_LANGUAGE, 'settings_language')
            ->setPrompt('settings_select_language')
            ->setRequired('settings_language_required');

        $form->addSubmit('send', 'save');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[]= [$this, 'languageFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function languageFormSuccess(Form $form, ArrayHash $values)
    {
        $this->getHttpResponse()->setCookie(self::SETTINGS_LANGUAGE, $values->{self::SETTINGS_LANGUAGE}, '1 year');

        $this->cache->clean([Cache::ALL =>true]);

        $this->redirect(':Settings:Language:default');
    }
}
