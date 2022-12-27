<?php
namespace App\Exports\Attendance;

ini_set('memory_limit', '-1');

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Str;

class AttendanceRecapSheet implements FromCollection, WithCustomStartCell, WithColumnWidths, WithEvents, WithTitle
{
    protected array $data;
    private string $title;

    public function __construct($title, $data)
    {
        $this->data  = $data;
        $this->title = $title;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return collect($this->data['data']);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells("A2:" . $highestColumn . "2");
                $sheet->mergeCells("A3:" . $highestColumn . "3");
                $sheet->mergeCells("A4:" . $highestColumn . "4");

                $sheet->getStyle("A2:" . $highestColumn . "4")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->setCellValue("A2", Str::upper($this->data['headerTitle']));
                $sheet->setCellValue("A3", Str::upper($this->data['headerSubtitle']));
                $sheet->setCellValue("A4", Str::upper($this->data['additional_title']));

                $sheet->getRowDimension(6)->setRowHeight(30);
                $sheet->getRowDimension(7)->setRowHeight(30);

                $sheet->mergeCells("A6:A7");
                $sheet->mergeCells("B6:B7");
                $sheet->mergeCells("C6:C7");
                $sheet->mergeCells("D6:D7");
                $sheet->mergeCells("E6:E7");
                $sheet->mergeCells("F6:F7");
                $sheet->mergeCells("G6:G7");
                $sheet->mergeCells("H6:H7");
                $sheet->mergeCells("I6:I7");
                $sheet->mergeCells("J6:J7");
                $sheet->mergeCells("K6:K7");
                $sheet->mergeCells("L6:L7");
                $sheet->mergeCells("M6:M7");
                $sheet->mergeCells("N6:N7");
                $sheet->mergeCells("O6:O7");
                $sheet->mergeCells("P6:P7");
                $sheet->mergeCells("Q6:Q7");
                $sheet->mergeCells("R6:R7");
                $sheet->mergeCells("S6:S7");
                $sheet->mergeCells("T6:T7");

                $sheet->setCellValue("A6", "No");
                $sheet->setCellValue("B6", "Nama Pegawai");
                $sheet->setCellValue("C6", "Jabatan");
                $sheet->setCellValue("D6", "PT");
                $sheet->setCellValue("E6", "PC");
                $sheet->setCellValue("F6", "TPT");
                $sheet->setCellValue("G6", "A");
                $sheet->setCellValue("H6", "I");
                $sheet->setCellValue("I6", "C");
                $sheet->setCellValue("J6", "S");
                $sheet->setCellValue("K6", "IB");
                $sheet->setCellValue("L6", "DL");
                $sheet->setCellValue("M6", "HEF");
                $sheet->setCellValue("N6", "HDR");
                $sheet->setCellValue("O6", "JH");
                $sheet->setCellValue("P6", "JA");
                $sheet->setCellValue("Q6", "JK");
                $sheet->setCellValue("R6", "HK");
                $sheet->setCellValue("S6", "TA");
                $sheet->setCellValue("T6", "HUKUMAN");

                $sheet->getDelegate()->getStyle('C8:C'.$highestRow)->getAlignment()->setWrapText(true);

                $styleArray = [
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" =>
                                Border::BORDER_THIN,
                            "color" => ["argb" => "000000"],
                        ],
                    ],
                ];

                $cellRange = "A6:" . $highestColumn.$highestRow; // All headers
                $sheet
                    ->getDelegate()
                    ->getStyle($cellRange)
                    ->applyFromArray($styleArray);
                $sheet->getDelegate()->getStyle("A6:".$highestColumn."7")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getStyle("A6:".$highestColumn."7")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getDelegate()->getStyle("A6:".$highestColumn."7")->getAlignment()->setWrapText(true);

                $lastRow = count($this->data['data']) + 9;
                $sheet->mergeCells("A".$lastRow.":".$highestColumn.$lastRow);
                $sheet->setCellValue("A".$lastRow, "Keterangan:");
                $lastRow++;
                $sheet->mergeCells("A".$lastRow.":".$highestColumn.$lastRow);
                $sheet->setCellValue("A".$lastRow, "PT : Total potongan keterlambatan | PC : Total potongan pulang sebelum waktu | TPT : Jml. Total PT + PC |");
                $lastRow++;
                $sheet->mergeCells("A".$lastRow.":".$highestColumn.$lastRow);
                $sheet->setCellValue("A".$lastRow, "A : Jml.total alpa | I : Jml. total Izin | C : jml. total Cuti | S : Jml. total Sakit | DL : Jml.total Dinas Luar |");
                $lastRow++;
                $sheet->mergeCells("A".$lastRow.":".$highestColumn.$lastRow);
                $sheet->setCellValue("A".$lastRow, "IB : Jml. Total Izin Belajar | HEF : Jml. Hari Efektif | HDR : Jml. Hadir | JH : Jml. Jam Hadir Normal |");
                $lastRow++;
                $sheet->mergeCells("A".$lastRow.":".$highestColumn.$lastRow);
                $sheet->setCellValue("A".$lastRow, "JA : Jml. Jam Aktif | JK : Jml. Kekurangan Jam (Tand + utk kekurangan jam, tanda - untuk kelebihan jam |");
                $lastRow++;
                $sheet->mergeCells("A".$lastRow.":".$highestColumn.$lastRow);
                $sheet->setCellValue("A".$lastRow, "HK : Konversi Jam Kurang jadi Hari");

            },
        ];
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 40,
            'C' => 40,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10,
            'N' => 10,
            'O' => 10,
            'P' => 10,
            'Q' => 10,
            'R' => 10,
            'S' => 10,
            'T' => 30,
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}

