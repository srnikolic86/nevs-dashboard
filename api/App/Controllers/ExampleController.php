<?php

namespace App\Controllers;

use Nevs\Controller;
use Nevs\Response;

class ExampleController extends Controller
{
    public function Select(): Response
    {
        $options = [];
        for ($i = 1; $i <= 1000; $i++) {
            $options[] = [
                'label' => 'option' . $i,
                'value' => $i
            ];
        }
        return new Response(json_encode($options));
    }

    public function Autocomplete(): Response
    {
        $options = [];
        for ($i = 1; $i <= 1000; $i++) {
            $options[] = 'option' . $i;
        }
        return new Response(json_encode($options));
    }

    public function Table(): Response
    {
        $sort = json_decode($this->request->data['sort'], true);
        error_log(json_encode($sort));
        $current_page = $this->request->data['currentPage'];
        $rows_per_page = $this->request->data['rowsPerPage'];

        $items = [];
        $total_records = 1000;
        $total_pages = ceil($total_records / $rows_per_page);

        $first_record = $rows_per_page * ($current_page - 1) + 1;
        $last_record = $first_record + $rows_per_page - 1;

        for ($i = $first_record; $i <= $last_record; $i++) {
            $items[] = [
                'field1' => 'value ' . $i . '-1',
                'field2' => 'value ' . $i . '-2',
                'field3' => 'value ' . $i . '-3',
                'field4' => 'value ' . $i . '-4',
                'field5' => 'value ' . $i . '-5'
            ];
        }

        return new Response(json_encode([
            'items' => $items,
            'totalRecords' => $total_records,
            'totalPages' => $total_pages
        ]));
    }
}