<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class InvoicesExport implements FromArray
{
        protected $invoices;

        public function collection()
        {
            return [
                [1, 2, 3],
                [4, 5, 6]
            ];
        }
        public function __construct(array $invoices)
        {
            $this->invoices = $invoices;
        }

        public function array(): array
        {
            return $this->invoices;
        }
}