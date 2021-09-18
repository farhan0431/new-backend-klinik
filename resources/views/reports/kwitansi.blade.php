<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Daftar Surat Tanda Setoran</title>
    
    <style>
        @media print{@page{size:potrait;margin:0 ;}}
        body{margin-top:-25px}
        *{font-family:Roboto, system-ui, sans-serif !important;}
        #logo{margin-top:15px;margin-left:15px;height:70px;display:block; }
        .head{display:inline-flex; flex-direction:row; align-items: center; text-align:left}
        /* .head div{box-sizing:border-box;padding:0 10px;} */
        .head .img-logo{float:left;  width:10%; margin:0 20px;}
        /* .head .img-logo img{float:right;} */
        .head .title-document{width:90%; padding-top:13px; padding-left:10px}
        .head .title-document p{font-weight:bold;margin:5px !important;font-size:14px;}
        .title-sts{margin-top:-5px; margin-bottom:15px}
        .sts{font-size:15px !important; width:100%} 
        .content-data{padding:0 25px;margin-top:-15px;}
        table{width:100%;}
        table thead tr{color:#000 }
        table, th{border-collapse:collapse;}
        table th{height:30px;font-size:12px;text-align:left;padding:3px 10px;}
        table td{font-size:12px;padding:7px 10px;border-bottom:.5px solid rgba(58,200,248,.2) }
        .border-total{border-bottom:2px solid black;}
        .judul-pajak{padding:14px 0;padding-left:10px;}
        .right-teks{text-align:right !important;}
        .center-teks{text-align:center;}
        .type-pajak td, tfoot tr td{padding:10px;border:1px solid black;font-weight:bold;font-style:italic;text-decoration:underline;}
        .tertanda{width:100%;margin-top:30px;}
        .tertanda .col{text-align:center;width:50%;float:right;height:150px;}
        .tertanda .col p{font-size:13px;margin:none;margin-block-start:0;margin-block-end:0;margin-top:10px;}
        /* .data-info{margin-bottom:100px;} */
        .data-info, .data-name{width:80%;}
        .jumlah-data td{padding:8px;font-size:10px;border-bottom-style:double !important;}
        .left-teks{text-align:left;}
        .clearfloat{clear:both}
        .clearfix{overflow:auto;}
        .clearfix::after{content:"";clear:both;display:table;}
        #footer{position:fixed;left:0;right:0;color:black;font-size:12px;bottom:0;border-top:1px solid black;margin:0 25px;padding-top:5px;}
        #footer .footer-total{margin-top:-25px }
        .page-number{margin-top:-50px }
        .page-number:before{content:"Page " counter(page);}
        td, th{border:1px solid black !important;}
        .desc{width:100%;font-size:13px !important;padding:0 25px;margin-top:0;margin-bottom:25px;}
        .desc table tr td{padding:5px 0 !important;border:none !important;}
        .desc .name-data{width:200px;}
        .desc tr td span{text-align:right;}
        .content-data .desc-data{font-size:14px; margin:8px 0 5px;width:100% !important;font-weight:bold }
        .auto{margin:auto }

    </style>
</head>
<body>

    <div class="head">
        <div class="img-logo">
            <img src="../public/img/logo.png" id="logo" alt="">
        </div>
        <div class="title-document">
            <p>DENTAL FAMILY CARE CLINIC</p>
            <p>Klinik Perawatan Kesehatan Gigi</p>
            <p>Jl. Kebahagian Blok B, No.4, BTP - Makassar</p>
            <p>0812 4440 9161</p>
        </div>
    </div>

    <hr class="clearfloat" style="width:100%; margin: 0 25px; margin-top:15px;margin-bottom:10px; border:1px solid black;">
    
    <div class="desc">
        
        <div class="title-sts center-teks">
            <h1 class="sts"><b>Kwitansi</b></h1>
        </div>
        <table>
            <!-- <tr>
                <td class="name-data"><b>Nomor Kartu</b> </td>
                <td><span>:&nbsp;&nbsp;&nbsp;</span>{{$kartuBerobat->no_kartu}}</td>
            </tr> -->
            <tr>
                <td class="name-data"><b>Tanggal </b> </td>
                <td><span>:&nbsp;&nbsp;&nbsp;</span>{{ $tanggal }}</td>
            </tr>
            <tr>
                <td class="name-data"><b>Dokter Penangung Jawab </b> </td>
                <td><span>:&nbsp;&nbsp;&nbsp;</span>{{$kartuBerobat->dokter->nama}}</td>
            </tr>
            
        </table>
    </div>

    <div class="content-data">
        <table> 
            <thead>
                <tr>
                    <th class="center-teks" style="width:5%">No.</th>
                    <th class="center-teks" style="width:25%">Jenis Pemeriksaan</th>
                    <th class="center-teks" style="">Diagnosa</th>
                    <th class="center-teks" style="width:25%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
            <tr>
                    <td class="center-teks"> 1</td>
                    <td>
                        Pemeriksaan Umum
                    </td>
                    <td>{{$kartuBerobat->diagnosa}}</td>
                    <td style="text-align:right">Rp. {{$kartuBerobat->biaya}}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align:right">Jumlah</th>
                    <th style="text-align:right">Rp  {{$kartuBerobat->biaya}}</th>
                </tr>
            </tfoot>
        </table>
        <div class="desc-data">
           Metode Pembayaran : {{$pembayaran}}.
        </div>
    </div>
    
    <div class="tertanda" style="margin-top:50px;padding-left:10%">
    <div class="col">
            <div class="data-info" >
                <p>Mengetahui,</p>
                <p style="margin-top:0 !important; font-weight:bold"></p>
                <img src="../public/img/tanda_tangan.png" style="height:150" alt="">
            </div>
           
            <div class="data-name">
                <p style="margin-bottom:-5px !important;">{{$kartuBerobat->dokter->nama}}</p>
                <hr style="width: 175px;">
                <p style="margin-bottom:0px !important;">SIP : 446/22.1.14/Drg/DKK/II/2019</p>
                <p style="margin-top:-5px !important "></p>
            </div>
        </div>
        <div class="col">
            <div class="data-info " >
                <!-- <p>&nbsp;</p>
                <p style="margin-top:0 !important; font-weight:bold">aasd</p> -->
            </div>
            <div class="data-name" >
                <!-- <p style="margin-bottom:-5px !important;">asd</p>
                <hr style="width: 175px;">
                <p style="margin-top:-5px !important ">NIP 123</p> -->
            </div>
        </div>
       

    </div>

</body>
</html>