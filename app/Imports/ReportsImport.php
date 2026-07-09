<?php 
namespace App\Imports;

use App\Models\Report; // Ensure you have a Report model
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsImport implements ToModel, WithStartRow, WithLimit, WithMapping, WithCalculatedFormulas, WithMultipleSheets
{
    private $currentRow = 5;
    /**
     * Start reading from row 5 (A5)
     */
    public function startRow(): int
    {
        return 5;
    }

    /**
     * Limit the import to 7 rows (From row 5 to 11 is 7 rows)
     */
    public function limit(): int
    {
        return 11;
    }

    public function sheets(): array
    {
        return [
            // 0 represents the first sheet in the Excel file
            0 => new FirstSheetImport(),
        ];
    }

    /**
     * Map the columns A through I to your database fields
     */
    public function map($row): array
    {
        // $row[0] is column A, $row[1] is column B, etc.
        return [
            'column_a_data' => $row[0], 
            'column_b_data' => $row[1],
            'column_c_data' => $row[2],
            'column_d_data' => $row[3],
            'column_e_data' => $row[4],
            'column_f_data' => $row[5],
            'column_g_data' => $row[6],
            'column_h_data' => $row[7],
            'column_i_data' => $row[8],
        ];
    }
    
    public function model(array $row)
    {
     return new Report([
            'Mines_Site'   => $row['column_a_data'],
            'LTI_MTI_act'   => $row['column_b_data'],
            'FAC_act'   => $row['column_c_data'],
            'OP_tgt'   => $row['column_d_data'],
            'OP_act'   => $row['column_e_data'],
            'OP_achv'   => $row['column_f_data'],
            'MD_tgt'   => $row['column_g_data'],
            'MD_act'   => $row['column_h_data'],
            'MD_achv'   => $row['column_i_data'],              
        ]);
    }
}