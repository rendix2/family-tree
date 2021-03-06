<?php
/**
 *
 * Created by PhpStorm.
 * Filename: Translator.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 1:49
 */

use Nette\Http\IRequest;
use Nette\Localization\ITranslator;

/**
 * Class Translator
 */
class Translator implements ITranslator
{
    /**
     * @var string $language
     */
    private $language;

    /**
     * @var array|false $translations
     */
    private $translations;

    /**
     * Translator constructor.
     *
     * @param IRequest $request
     */
    public function __construct(IRequest $request)
    {
        $language = $request->getCookie('settings_language');

        if ($language === null) {
            $language = 'cs.CZ';
        }

        $sep = DIRECTORY_SEPARATOR;
        $path = __DIR__ . $sep . 'translations' . $sep . $language . $sep . 'translation.ini';

        $translations = parse_ini_file($path);

        $this->translations = $translations;
        $this->language = $language;
    }

    /**
     * @inheritDoc
     *
     * @throws TranslationException
     */
    public function translate($message, $count = null)
    {
        if (!isset($this->translations[$message]) && $count === null) {
            $message = sprintf('Unknown message "%s" to translate.', $message);

            throw new TranslationException($message);
        }

        if ($count === null) {
            return $this->translations[$message];
        } else {
            if (is_numeric($count)) { // its count
                if ($this->language === 'cs.CZ') {
                    if ($count === 1) {
                        return sprintf($this->translations[$message . '_1'], $count);
                    } elseif ($count >= 2 && $count < 5) {
                        return sprintf($this->translations[$message . '_2'], $count);
                    } elseif ($count >= 5) {
                        return sprintf($this->translations[$message . '_5'], $count);
                    }
                } elseif ($this->language === 'en.US') {
                    if ($count === 1) {
                        return sprintf($this->translations[$message . '_1'], $count);
                    } elseif ($count > 2) {
                        return sprintf($this->translations[$message . '_2'], $count);
                    }
                }
            } elseif (is_array($count)) { // params to sprintf
                $translated = $this->translations[$message];
                $params = array_merge([$translated], $count);

                return call_user_func_array('sprintf', $params);
            }
        }
    }
}

class TranslationException extends Exception
{
}
