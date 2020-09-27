<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJob.php
 * User: Tomáš Babický
 * Date: 26.09.2020
 * Time: 16:46
 */

namespace Rendix2\FamilyTree\App\Filters;


use Dibi\Row;
use Nette\Localization\ITranslator;

class PersonJobFilter
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * JobFilter constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Row $job
     *
     * @return string
     */
    public function __invoke(Row $job)
    {
        $date = '';

        if ($job->dateSince && $job->dateTo) {
            $date = '(' . $job->dateSince->format('d.m.Y') . '-' . $job->dateTo->format('d.m.Y') . ')';
        } elseif ($job->dateSince && !$job->dateTo) {
            if ($job->untilNow) {
                $date = '(' . $job->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_until_now') . ')';
            } else {
                $date = '(' . $job->dateSince->format('d.m.Y') . ' - ' . $this->translator->translate('person_na') . ')';
            }
        } elseif (!$job->dateSince && $job->dateTo) {
            $date = '(' . $this->translator->translate('person_na') . ' - ' . $job->dateTo->format('d.m.Y') . ')';
        } else {
            if ($job->untilNow) {
                $date = '(' . $this->translator->translate('person_na') . ' - ' . $this->translator->translate('person_until_now') . ')';
            } else {
                $date = '';
            }
        }

        if ($job->company && $job->position) {
            return $job->company . ' ' . $job->position . ' ' . $date;
        } elseif (!$job->company && $job->position) {
            return $job->position . ' ' . $date;
        } elseif ($job->company && !$job->position) {
            return $job->company . ' ' . $date;
        } else {
            return '';
        }
    }
}