<?php
/**
 *
 * Created by PhpStorm.
 * Filename: DurationFilter.php
 * User: Tomáš Babický
 * Date: 07.10.2020
 * Time: 22:25
 */

namespace Rendix2\FamilyTree\App\Filters;

use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\Model\Entities\DurationEntity;

/**
 * Class DurationFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class DurationFilter
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * DurationFilter constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param DurationEntity $durationEntity
     * @return string
     */
    public function __invoke(DurationEntity $durationEntity)
    {
        $date = '';

        if ($durationEntity->dateSince && $durationEntity->dateTo) {
            $date = '(' . $durationEntity->dateSince->format('d.m.Y') . '-' . $durationEntity->dateTo->format('d.m.Y') . ')';
        } elseif ($durationEntity->dateSince && !$durationEntity->dateTo) {
            if ($durationEntity->untilNow) {
                $date = '(' . $durationEntity->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('date_until_now') . ')';
            } else {
                $date = '(' . $durationEntity->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('date_na') . ')';
            }
        } elseif (!$durationEntity->dateSince && $durationEntity->dateTo) {
            $date = '(' . $this->translator->translate('date_na') . ' - ' . $durationEntity->dateTo->format('d.m.Y') . ')';
        } else {
            if ($durationEntity->untilNow) {
                $date = '(' . $this->translator->translate('date_na') . ' - ' . $this->translator->translate('date_until_now') . ')';
            } else {
                $date = $this->translator->translate('date_na');
            }
        }

        return $date;
    }
}
