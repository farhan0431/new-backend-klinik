<?php
 
namespace App\Exports;
 
use App\User;
use App\PemeriksaanGigi;
use App\RekamMedis;
use App\Identitas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;


use Carbon\Carbon;


// use Maatwebsite\Excel\Concerns\FromQuery;
// use Maatwebsite\Excel\Concerns\WithMapping;




class UsersExport implements WithStyles,WithProperties,WithEvents
{

    private $count;
    private $tanggal;
    private $setting;
    private $jumlah;
    private $data;
    private $jenis;
    private $dokter;

    public function __construct($data,$count,$tanggal,$setting,$jumlah,$jenis,$dokter) {
        $this->data = $data;
        $this->count = $count+3;
        $this->tanggal = $tanggal;
        $this->setting = $setting;
        $this->jumlah = $jumlah;
        $this->jenis = $jenis;
        $this->dokter= $dokter;

        // $this->data = PemeriksaanGigi::count();
        // $this->data = $this->laporanData($tahun,$bulan);


        
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 15
            ],
            // 'borders' => [
            //     'outline' => [
            //         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
            //         'color' => ['argb' => '000'],
            //         'size' => 1
            //     ]
            // ]
            ];

            $headingStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 13
                ],
                // 'borders' => [
                //     'outline' => [
                //         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                //         'color' => ['argb' => '000'],
                //         'size' => 1
                //     ]
                // ]
                ];
        $jumlahStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '000'],
                    'size' => 1
                ]
                ],'font' => [
                    'bold' => true,
                    'size' => 15
                ],
                ];

        $tertandaStyle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
            ];
        
            $namaStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => ['argb' => '000'],
                        'size' => 1
                    ]
                ]
                ];
        
        return [
            // Handle by a closure.
            AfterSheet::class => function(AfterSheet $event) use ($styleArray,$jumlahStyle,$tertandaStyle,$namaStyle,$headingStyle) {

                $event->sheet->mergeCells('A1:T1');
                $event->sheet->getStyle('A1')->applyFromArray($styleArray);

                if($this->jenis == 0)
                {
                    $event->sheet->setCellValue('A1','LAPORAN PEMERIKSAAN HARIAN');

                }else if($this->jenis == 1)
                {
                    $event->sheet->setCellValue('A1','LAPORAN PEMERIKSAAN BULANAN');
                }else if($this->jenis == 2)
                {
                    $event->sheet->setCellValue('A1','LAPORAN PEMERIKSAAN BULANAN '. $this->dokter->nama);   
                }


                // $event->sheet->mergeCells('A2:B2');
                // $event->sheet->setCellValue('A2','PUSKESMAS');

                // $event->sheet->mergeCells('C2:D2');
                // $event->sheet->setCellValue('C2','NAMA');

                $event->sheet->getStyle('2')->applyFromArray($styleArray);
                $event->sheet->getStyle('3')->applyFromArray($headingStyle);




                // $event->sheet->mergeCells('A2:B2');
                // $event->sheet->setCellValue('A2','TANGGAL');

                

                $event->sheet->mergeCells('A2:C2');
                $event->sheet->setCellValue('A2',$this->tanggal);



                $event->sheet->setCellValue('A3','No');

                $event->sheet->mergeCells('B3:D3');
                $event->sheet->setCellValue('B3','No Rekam Medis');

                $event->sheet->mergeCells('E3:F3');
                $event->sheet->setCellValue('E3','Tanggal');


                $event->sheet->mergeCells('G3:H3');
                $event->sheet->setCellValue('G3','Nama Pasien');


                $event->sheet->mergeCells('I3:J3');
                $event->sheet->setCellValue('I3','Nama Dokter');

                $event->sheet->mergeCells('K3:M3');
                $event->sheet->setCellValue('K3','Diagnosa');

                $event->sheet->mergeCells('N3:O3');
                $event->sheet->setCellValue('N3','Perawatan');


                $event->sheet->mergeCells('P3:Q3');
                $event->sheet->setCellValue('P3','Biaya');

                $event->sheet->mergeCells('K8:O8');
                $event->sheet->setCellValue('K8','Total :');
                $event->sheet->getStyle('8')->applyFromArray($headingStyle);

                $event->sheet->mergeCells('P8:Q8');
                $event->sheet->setCellValue('P8','Rp. '.number_format($this->jumlah));


                




                $index = 4;
                $number = 0;

                foreach ($this->data as $row) {

                    $position = $index++;
                    $number++;

                    $event->sheet->setCellValue('A'.$position,$number);

                    $event->sheet->mergeCells('B'.$position.':D'.$position);
                    $event->sheet->setCellValue('B'.$position, str_pad($row->data_pasien->id,3,'0',STR_PAD_LEFT).'_DFCC_'.explode('-',$row->data_pasien->created_at)[1].'_'.explode('-',$row->data_pasien->created_at)[0][2].explode('-',$row->data_pasien->created_at)[0][2]);


                    $event->sheet->mergeCells('E'.$position.':F'.$position);
                    $event->sheet->setCellValue('E'.$position,$row->tanggal_janji);


                    $event->sheet->mergeCells('G'.$position.':H'.$position);
                    $event->sheet->setCellValue('G'.$position,$row->data_pasien->name);

                    $event->sheet->mergeCells('I'.$position.':J'.$position);
                    $event->sheet->setCellValue('I'.$position,$row->data_dokter->nama);



                    $event->sheet->mergeCells('K'.$position.':M'.$position);
                    $event->sheet->setCellValue('K'.$position,$row->kartu_berobat->diagnosa);

                   

                    $event->sheet->mergeCells('N'.$position.':O'.$position);
                    $event->sheet->setCellValue('N'.$position,$row->kartu_berobat->perawatan);

                    $event->sheet->mergeCells('P'.$position.':Q'.$position);
                    $event->sheet->setCellValue('P'.$position,'Rp. '.number_format($row->kartu_berobat->biaya));

                    


                }

                



                // $event->sheet->mergeCells('D3:E3');
                // $event->sheet->setCellValue('D3','Nama Dokter)');

                // $event->sheet->mergeCells('F4:E4');
                // $event->sheet->setCellValue('D4','Keterangan');



             




                

                

                



                




                
                


                


                    

                // $event->sheet->setCellValue('E:','TAHUN');
                // $event->sheet->setColumnFormat('A1','asdasd');

                // $event->sheet->getStyle('A2:E2')->applyFromArray($styleArray);
                // $event->sheet->setCellValue('A'.$this->count,$this->jumlah);
                // $event->sheet->getStyle('A'.$this->count.':E'.$this->count)->applyFromArray($jumlahStyle);

                // $event->sheet->setCellValue('D1',"Tahun $this->tahun Bulan $this->bulan");

                // $event->sheet->setCellValue('D'.($this->count+3),'Tertanda');
                // $event->sheet->getStyle('D'.($this->count+3))->applyFromArray($tertandaStyle);

                // $event->sheet->setCellValue('D'.($this->count+8),'Hardiansyah, S.H');
                // $event->sheet->getStyle('D'.($this->count+8))->applyFromArray($namaStyle);

                // $event->sheet->setCellValue('D'.($this->count+9),'Direktur Utama');
                // $event->sheet->getStyle('D'.($this->count+8))->applyFromArray($tertandaStyle);
            },
                        
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return [];
    }

    public function headings(): array
    {
        return [
            ['LAPORAN KASUS PENYAKIT PENYAKIT GIGI DAN MULUT'],
            ['JUMLAH',
            'KATEGORI',
            'TANGGAL',
            'KETERANGAN',
            'TIPE']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // $sheet->setBorder('A1', 'solid');

        return [
            // Style the first row as bold text.
            1   => ['font' => ['bold' => true,'size' => 20], 'border' => ['solid']],
            // 2    => ['font' => ['bold' => true,'size' => 15], 'border' => ['solid']],
            // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A1' => array(
                'width' => 60,
            ),
            'B' => 5,
            'C' => 5,
            'D' => 5,
            'E' => 5            
        ];
    }

    public function properties(): array
    {
        return [
            'creator'        => 'PT. Garuda Karya Medika',
            'lastModifiedBy' => 'Farhan',
            'title'          => 'Laporan Keuangan',
            'description'    => 'Laporan Keuangan Perusahaan',
            'subject'        => 'Laporan',
            'keywords'       => 'laporan,export,spreadsheet',
            'category'       => 'laporan',
            'manager'        => 'Safari Creative',
            'company'        => 'PT. Garuda Karya Medika',
        ];
    }

 
}