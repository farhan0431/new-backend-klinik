<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    <style>
        @media print {
            @page {size: landscape;margin: 20px; }
   
        }
        
        body, p {
            font-family: Roboto, system-ui, sans-serif;
        }
        #logo {
            margin-top: 10px;
            margin-left: 15px;
            height: 120px;
            display: block;
        }

        .head {
            width:100%;
            height:120px;
        }
        .head div {
            height: inherit;
            float: left;
            box-sizing: border-box;
            padding:0 10px;

        }

        .head hr {
            box-sizing: border-box;
            border: 1px solid black;
            width: 100%;
        }

        .head .img-logo {
            width: 30%;
            text-align:right;
        }
        .head .img-logo img{
            padding-right:65px;
        }

        .head .title-document {
            width: 70%;
            padding-left:20px;
            text-align: left;
        }

        .head .title-document p {
            font-weight: bold;
            margin: 5px !important;
            font-size:13px;
        }
        .content-data{
            padding:0 25px;
            margin-top:40px;
        }
        table {
            width: 100%;
        }
        table thead tr{
            /* background: rgba(0,114,201, .7); */
            background:#3AC8F8;
            color:#fff;
        }

        table, th {
            border-collapse: collapse;
        }


        table th {
            height: 30px;
            font-size: 12px;
            text-align:left;
            padding:3px 10px;

        }

        table td {
            font-size: 12px;
            padding:7px 10px;
            border-bottom:.5px solid rgba(58,200,248,.2)
            /* border-right: 1px solid black;
            border-bottom-style: dotted; */
            
        }
        .border-total{
            border-bottom: 2px solid black;
        }

        .right-teks {
            text-align: right;
        }
        .center-teks {
            text-align: center;
        }

        .type-pajak td, tfoot tr td {
            padding: 10px;
            border: 1px solid black;
            font-weight: bold;
            font-style: italic;
            text-decoration: underline;
        }

        .tertanda {
            width: 100%;
            margin-top:30px;
        }

        .tertanda .col {
            text-align: center;
            width: 50%;
            float: right;
            height: 150px;
        }

        .tertanda .col p {
            font-size: 12px;
            margin:none !important;
            margin-block-start: 0 !important;
            margin-block-end: 0 !important;
            margin-top: 10px !important;
        }

        hr {
            width: 250px;
            border: 1.5px solid black;
            border-collapse: collapse;
        }

        .data-info {
            margin-bottom: 100px;
        }
        

        .jumlah-data td {
            padding: 8px;
            font-size: 10px;
            border-bottom-style: double !important;
        }

        .left-teks {
            text-align: left;
        }

        .clearfix {
            overflow: auto;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        /* .footer{
            margin:0 25px;
            bottom:20;
            width:100%;
            position: fixed;
            z-index:2;
        }
        .footer-text-1,
        .footer-text-2{
            font-size:12px;
            margin:0;
            padding:0;
        }
        .footer-text-1{
            margin-bottom:-5px;
        }
        .footer-text-2{
            margin-top:-5px;
        }
        .footer hr{
            width:100%;
            border:1px solid black;
        } */

        #footer {
            position: fixed;
            left: 0;
            right: 0;
            color: black;
            font-size: 12px;
            bottom: 0;
            border-top: 1px solid black;
            margin:0 25px;
            padding-top:5px;
        }
        #footer .footer-total{
            margin-top:-25px
        }
        .page-number{
            margin-top:-50px
        }
        .page-number:before {
            content: "Page " counter(page);
        }

        
    </style>
</head>
<body>
<div id="footer">
    <!-- <div class="footer-total">Daftar Penerimaan Pajak</div> -->
    <div class="footer-nama-tgl">
    </div>
    <div class="right-teks page-number"></div>
</div>
    <div class="head">
        <div class="title-document">
        <h2>Easy Dent</h2>
            <p>{{ $setting->rumah_sakit }}</p>
            <p>{{ $setting->alamat }}</p>
            <p>LAPORAN PEMASUKAN</p>
            @if($jenis == 0)         
                <p><small class="center-teks">Tanggal {{ $tanggal }}</small></p>
            @else <p><small class="center-teks">{{ $tanggal }}</small></p>     
            @endif
        </div>
        <div class="img-logo">
            <img src="../public/img/logo.png" id="logo" alt="">
        </div>
        <!-- <hr> -->
    </div>
    <div class="content-data">
        <table> 
            <thead>
                <tr>
                    <th style="width:5%" >No</th>
                    <th style="width:10%">Tanggal</th>
                    <th style="width:20%">Nama Dokter</th>
                    <th style="width:50%" >Keterangan</th>
                    <th style="width:15%"class="right-teks">Biaya</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataJanji as $row)
                <tr>
                    <td class="center-teks">1</td>
                    <td>{{ $row->tanggal_janji }}</td>
                    <td>{{ $row->data_dokter->nama }}</td>
                    <td>Perawatan umum</td>
                    <td class="right-teks">Rp. {{ number_format($row->kartu_berobat->biaya) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="right-teks border-total" style="padding-right: 50px;"><h2>Total</h2></td>
                    <td colspan="1" class="right-teks border-total"><h2>Rp. {{ number_format($jumlahPemasukan) }}</h2></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="tertanda clearfix">
        
        <div class="col clearfix">
           
        </div>
        <div class="col">
        </div>
    </div>

</body>
</html>