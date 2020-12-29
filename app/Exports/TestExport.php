<?php
namespace App\Exports;

use App\Invoice;
use App\Http\Model\loginLogs;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class TestExport implements FromQuery
{
    use Exportable;
    protected  $id;
    public function __construct(int $id)
    {
        $this->$id = $id;
    }
    public function Query()
{

    return loginLogs::query()->where('id',$this->id);
}
}