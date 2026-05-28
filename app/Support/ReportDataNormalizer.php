<?php

namespace App\Support;

class ReportDataNormalizer
{
    private const MAX_VERDICT_LIST_ITEMS = 3;
    private const MAX_BADGE_ROWS = 2;
    private const MAX_BADGES = 8;
    private const BADGE_ROW_BUDGET = 135;

    public static function normalize(array $data, string $locale): array
    {
        if (!isset($data['page_one']) || !is_array($data['page_one'])) {
            return $data;
        }

        $data['page_one'] = self::normalizePageOne($data['page_one'], $locale);

        return $data;
    }

    private static function normalizePageOne(array $pageOne, string $locale): array
    {
        $pageOne['badges'] = self::limitBadges($pageOne['badges'] ?? []);

        if (isset($pageOne['verdict']) && is_array($pageOne['verdict'])) {
            $pageOne['verdict']['ideal_for'] = self::limitStringList($pageOne['verdict']['ideal_for'] ?? []);
            $pageOne['verdict']['not_ideal_for'] = self::limitStringList($pageOne['verdict']['not_ideal_for'] ?? []);
        }

        if (isset($pageOne['charts']) && is_array($pageOne['charts'])) {
            $pageOne['charts'] = array_values(array_map(
                fn (array $chart): array => self::normalizeChart($chart, $locale),
                array_filter($pageOne['charts'], 'is_array')
            ));
        }

        return $pageOne;
    }

    private static function normalizeChart(array $chart, string $locale): array
    {
        if (($chart['id'] ?? null) !== 'total_acquisition_cost') {
            return $chart;
        }

        if (!isset($chart['data']['segments']) || !is_array($chart['data']['segments'])) {
            return $chart;
        }

        $chart['data']['segments'] = array_values(array_map(function (array $segment) use ($locale): array {
            if (isset($segment['label']) && is_string($segment['label'])) {
                $segment['label'] = self::normalizeAcquisitionLabel($segment['label'], $locale);
            }

            return $segment;
        }, array_filter($chart['data']['segments'], 'is_array')));

        return $chart;
    }

    private static function limitStringList(mixed $items): array
    {
        if (!is_array($items)) {
            return [];
        }

        $filtered = array_values(array_filter(array_map(function (mixed $item): ?string {
            if (!is_string($item)) {
                return null;
            }

            $item = trim($item);

            return $item === '' ? null : $item;
        }, $items)));

        return array_slice($filtered, 0, self::MAX_VERDICT_LIST_ITEMS);
    }

    private static function limitBadges(mixed $badges): array
    {
        if (!is_array($badges)) {
            return [];
        }

        $kept = [];
        $currentRow = 0;
        $currentRowWidth = 0;

        foreach (array_slice($badges, 0, self::MAX_BADGES) as $badge) {
            if (!is_array($badge)) {
                continue;
            }

            $label = trim((string) ($badge['label'] ?? ''));
            $value = trim((string) ($badge['value'] ?? ''));

            if ($label === '' || $value === '') {
                continue;
            }

            $estimatedWidth = self::estimateBadgeWidth($label, $value);

            if ($currentRowWidth > 0 && ($currentRowWidth + $estimatedWidth) > self::BADGE_ROW_BUDGET) {
                $currentRow++;
                $currentRowWidth = 0;
            }

            if ($currentRow >= self::MAX_BADGE_ROWS) {
                break;
            }

            $kept[] = $badge;
            $currentRowWidth += $estimatedWidth;
        }

        return array_values($kept);
    }

    private static function estimateBadgeWidth(string $label, string $value): int
    {
        $text = $label . ': ' . $value;

        return max(18, min(48, (int) ceil(mb_strlen($text, 'UTF-8') * 0.6) + 12));
    }

    private static function normalizeAcquisitionLabel(string $label, string $locale): string
    {
        $normalized = self::normalizeText($label);
        $locale = $locale === 'en' ? 'en' : 'ro';

        return match (true) {
            str_contains($normalized, 'property'),
            str_contains($normalized, 'pret') => $locale === 'en' ? 'Price' : 'Preț',

            str_contains($normalized, 'notar'),
            str_contains($normalized, 'authentication') => $locale === 'en' ? 'Notary' : 'Notar',

            str_contains($normalized, 'registry'),
            str_contains($normalized, 'intabulare'),
            str_contains($normalized, 'cf') => $locale === 'en' ? 'Registry' : 'Carte',

            str_contains($normalized, 'agency'),
            str_contains($normalized, 'comision') => $locale === 'en' ? 'Agency' : 'Comision',

            str_contains($normalized, 'bank'),
            str_contains($normalized, 'evaluare') => $locale === 'en' ? 'Bank' : 'Bancă',

            str_contains($normalized, 'renov') => $locale === 'en' ? 'Renovation' : 'Renovare',

            str_contains($normalized, 'reserve'),
            str_contains($normalized, 'rezerv') => $locale === 'en' ? 'Reserve' : 'Rezervă',

            default => self::firstWord($label),
        };
    }

    private static function firstWord(string $label): string
    {
        $parts = preg_split('/[\s,.;:()\/\\-]+/u', trim($label)) ?: [];

        return $parts[0] ?? $label;
    }

    private static function normalizeText(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');

        return strtr($value, [
            'ă' => 'a',
            'â' => 'a',
            'î' => 'i',
            'ș' => 's',
            'ş' => 's',
            'ț' => 't',
            'ţ' => 't',
        ]);
    }
}