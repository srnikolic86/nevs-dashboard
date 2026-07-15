<?php

namespace App;

class Helpers
{
    /**
     * Options for an ajax select route. Returns at most `limit` (default 100) rows matching the `search` term,
     * always including the currently-selected value(s) (`protected`) so the field keeps its label even when the
     * selection isn't among the capped matches. Search columns should be reflected in the option label so the
     * select components' client-side filter stays consistent with the server-side search.
     *
     * $config keys: `table`, `data` (the request data), `label` (callable(row): string), and optionally
     * `base_where`, `search` (array of columns), `order`, `limit`.
     */
    static function SelectOptions(array $config): array
    {
        global $DB;

        $table = $config['table'];
        $data = $config['data'] ?? [];
        $base_where = $config['base_where'] ?? '1=1';
        $search_columns = $config['search'] ?? [];
        $order = $config['order'] ?? '`id` ASC';
        $limit = (int)($config['limit'] ?? 100);
        $label = $config['label'];

        $search = isset($data['search']) ? trim((string)$data['search']) : '';

        $protected_ids = [];
        if (isset($data['protected'])) {
            $protected = is_array($data['protected']) ? $data['protected'] : [$data['protected']];
            $protected_ids = array_values(array_filter(array_map('intval', $protected)));
        }

        $options = [];

        $where = $base_where;
        $params = [];
        if ($search !== '' && count($search_columns) > 0) {
            $likes = [];
            foreach ($search_columns as $column) {
                $likes[] = '`' . $column . '` LIKE ?';
                $params[] = '%' . $search . '%';
            }
            $where .= ' AND (' . implode(' OR ', $likes) . ')';
        }
        foreach ($DB->ExecuteSelect('SELECT * FROM `' . $table . '` WHERE ' . $where . ' ORDER BY ' . $order . ' LIMIT ' . $limit, $params) as $row) {
            $options[$row['id']] = ['label' => $label($row), 'value' => $row['id']];
        }

        // The currently-selected value(s) are always returned so the field renders its label.
        if (count($protected_ids) > 0) {
            $placeholders = implode(',', array_fill(0, count($protected_ids), '?'));
            foreach ($DB->ExecuteSelect('SELECT * FROM `' . $table . '` WHERE `id` IN (' . $placeholders . ')', $protected_ids) as $row) {
                if (!isset($options[$row['id']])) {
                    $options[$row['id']] = ['label' => $label($row), 'value' => $row['id']];
                }
            }
        }

        return array_values($options);
    }

    /**
     * Row count for a paginated list, capped for performance: returns the exact count when it is <= $cap, or
     * -1 when there are more than $cap matching rows. Counting stops after $cap+1 rows (a LIMIT-ed derived
     * table) so it never scans a whole large table. `$id_query` selects the row id(s) with the WHERE/join (and
     * DISTINCT if the join can multiply rows) already applied, but NO ORDER BY / LIMIT. The frontend NevsTable
     * renders -1 as "<cap pages>+".
     */
    static function CappedCount(string $id_query, array $params = [], int $cap = 10000): int
    {
        global $DB;
        $rows = $DB->ExecuteSelect('SELECT COUNT(*) AS `c` FROM (' . $id_query . ' LIMIT ' . ($cap + 1) . ') `t`', $params);
        $count = (is_array($rows) && count($rows) > 0) ? (int)$rows[0]['c'] : 0;
        return $count > $cap ? -1 : $count;
    }


    static function CheckEmail(string $email): bool
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL)) ;
    }

    static function GenerateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}