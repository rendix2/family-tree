<?php

use Nette\Localization\ITranslator;

/**
 *
 * Created by PhpStorm.
 * Filename: Translator.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 1:49
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
     * @param string $language
     */
    public function __construct($language)
    {
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
        if (!isset($this->translations[$message])) {
            $message = sprintf('Unknown message "%s" to translate.', $message);

            throw new TranslationException($message);
        }

        return $this->translations[$message];
    }
}

class TranslationException extends Exception
{
}