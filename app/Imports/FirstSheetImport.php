<?php 
namespace App\Imports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class FirstSheetImport implements ToModel, WithStartRow, WithLimit, WithCalculatedFormulas
{
    private $currentRow = 5;
    public function startRow(): int { return 5; }
    public function limit(): int { return 11; }

    public function model(array $row)
    {
        // Skip empty rows to avoid "Mines Site cannot be null"
        if (empty($row[0])) {
            return null;
        }
        $rowNumber = $this->currentRow;
        $this->currentRow++; 
   
        if ($rowNumber == 12) {
            return null; 
        }

        if ($rowNumber > 15) {
            return null;
        }
        if ($rowNumber >= 5 && $rowNumber <= 11) {
        return new Report([
            'Mines_Site'  => $row[0],
            'LTI_MTI_act' => $row[1],
            'FAC_act'     => $row[2],
            'OP_tgt'      => $row[3],
            'OP_act'      => $row[4],
            'OP_achv'     => $row[5], 
            'MD_tgt'      => $row[6],
            'MD_act'      => $row[7],
            'MD_achv'     => $row[8],
            'is_exploration' => '0',
        ]);
        }
        if ($rowNumber >= 13 && $rowNumber <= 15) {
            return new Report([
                'Mines_Site'  => $row[0], 
                'LTI_MTI_act' => $row[1], 
                'FAC_act'     => $row[2], 
                'OP_tgt'      => $row[3], 
                'OP_act'      => $row[4], 
                'OP_achv'     => $row[5],
                'is_exploration' => '1',
            ]);
        }
        return null;
    }
}