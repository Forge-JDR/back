<?php

namespace App\Domain;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SheetTemplate
{
    public static function fields(mixed $fields): array
    {
        if (!is_array($fields) || !array_is_list($fields) || count($fields) > 40) {
            throw new BadRequestHttpException('Le modèle doit contenir au maximum 40 champs.');
        }
        $keys = [];
        foreach ($fields as $field) {
            if (!is_array($field) || array_diff(array_keys($field), ['key', 'label', 'type']) ||
                !is_string($field['key'] ?? null) || !preg_match('/^[a-z][a-z0-9_]{0,39}$/', $field['key']) ||
                isset($keys[$field['key']]) || !is_string($field['label'] ?? null) ||
                trim($field['label']) === '' || mb_strlen($field['label']) > 100 ||
                !in_array($field['type'] ?? null, ['text', 'number', 'boolean'], true)) {
                throw new BadRequestHttpException('Champ de fiche invalide : clé unique, libellé et type requis.');
            }
            $keys[$field['key']] = true;
        }
        return $fields;
    }

    public static function values(mixed $values, array $fields): array
    {
        if (!is_array($values) || array_diff(array_keys($values), array_column($fields, 'key'))) {
            throw new BadRequestHttpException('Valeurs de fiche invalides.');
        }
        foreach ($fields as $field) {
            if (!array_key_exists($field['key'], $values)) {
                continue;
            }
            $value = $values[$field['key']];
            $valid = match ($field['type']) {
                'text' => is_string($value) && mb_strlen($value) <= 10000,
                'number' => (is_int($value) || is_float($value)) && is_finite((float) $value),
                'boolean' => is_bool($value),
                default => false,
            };
            if (!$valid) {
                throw new BadRequestHttpException('Valeur incompatible avec le champ '.$field['label'].'.');
            }
        }
        return $values;
    }
}
