<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        .: DOCUMENT-{{ \Carbon\Carbon::parse($content['tanggal_mulai'] . ' ' . $content['jam_mulai'])->translatedFormat('d F Y H:i:s') }} :.
    </title>

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: rgb(115, 118, 161);
        }

        .title {
            font-size: 16px;
            font-weight: bold;
        }

        .content {
            margin-top: 20px;
        }

        .details {
            margin-top: 20px;
        }

        .details>span {
            font-weight: bold;
        }

        .form-group {
            margin-top: 15px;
        }

        .title-content {
            color: gray;
        }

        .body-content {
            margin-top: 5px;
            color: black;
            font-weight: 500;
        }

        .form-group>.jarak {
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <img src="{{ public_path('images/tnos_logo_mobile.png') }}" alt="">

    <div class="content">
        <div class="title">
            <?php if ($content["tnos_service_id"] == "4" && $content["tnos_subservice_id"] == "1") : ?>
            PAS
            <?php elseif ($content["tnos_service_id"] == "5" && $content["tnos_subservice_id"] == "1") : ?>
            TRIGER
            <?php elseif ($content["tnos_service_id"] == "3") : ?>
            <?php if ($content["tnos_subservice_id"] == "1") : ?>
            Badan Hukum PT
            <?php elseif ($content["tnos_subservice_id"] == "2") : ?>
            Badan Usaha CV
            <?php elseif ($content["tnos_subservice_id"] == "3") : ?>
            Yayasan
            <?php elseif ($content["tnos_subservice_id"] == "4") : ?>
            Perkumpulan
            <?php elseif ($content["tnos_subservice_id"] == "5") : ?>
            Badan Hukum Asosiasi
            <?php elseif ($content["tnos_subservice_id"] == "6") : ?>
            Legalitas Lainnya
            <?php elseif ($content["tnos_subservice_id"] == "7") : ?>
            Komprehensif Solusi Hukum
            <?php elseif ($content["tnos_subservice_id"] == "8") : ?>
            Pembayaran Lainnya
            <?php endif ?>
            <?php endif ?>
        </div>

        <div class="details">

            @if ($content['tnos_invoice_id'] !== null)
            <span>No. Invoice: {{ $content['tnos_invoice_id'] }} </span>

            <br><br>
            @endif

            <span>Detail Layanan</span>

            <?php if ($content["tnos_service_id"] == "3" && $content["tnos_subservice_id"] == "8") : ?>
            <div class="form-group">
                <label class="title-content">
                    Keperluan
                </label> <br>
                <div class="body-content">
                    {{ $content['needs'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Nama Pengguna
                </label> <br>
                <div class="body-content">
                    {{ $content['name'] }}
                </div>
            </div>

            <?php elseif ($content["tnos_service_id"] == "3" && $content["tnos_subservice_id"] == "7") : ?>
            <div class="form-group">
                <label class="title-content">
                    Nama Pengguna
                </label> <br>
                <div class="body-content">
                    {{ $content['name'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Permasalahan Hukum
                </label> <br>
                <div class="body-content">
                    {{ $content['needs'] }}
                </div>
            </div>

            <?php elseif ($content["tnos_service_id"] == "4" || $content["tnos_service_id"] == "5" && $content["tnos_subservice_id"] == "1") : ?>
            <div class="form-group">
                <label class="title-content">
                    Keperluan Pengamanan
                </label> <br>
                <div class="body-content">
                    {{ $content['needs'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Rincian Lokasi
                </label> <br>
                <div class="body-content">
                    {{ $content['location'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Tanggal & Waktu Mulai
                </label> <br>
                <div class="body-content">
                    {{ \Carbon\Carbon::parse($content['tanggal_mulai'] . ' ' . $content['jam_mulai'])->translatedFormat('d F Y H:i:s') }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Penanggung Jawab
                </label> <br>
                <div class="body-content">
                    {{ $content['nama_pic'] }} - {{ $content['nomor_pic'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Jumlah Pengamanan
                </label> <br>
                <div class="body-content">
                    {{ $content['jml_personil'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Durasi Pengamanan
                </label> <br>
                <div class="body-content">
                    {{ $content['durasi_pengamanan'] }} Jam
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Nama Pengguna
                </label> <br>
                <div class="body-content">
                    {{ $content['name'] }}
                </div>
            </div>

            <?php elseif ($content["tnos_service_id"] == 3 && ($content["tnos_subservice_id"] == 1 || $content["tnos_subservice_id"] == 2 || $content["tnos_subservice_id"] == 3 || $content["tnos_subservice_id"] == 4 || $content["tnos_subservice_id"] == 5 || $content["tnos_subservice_id"] == 6 )) : ?>

            <div class="form-group">
                <label class="title-content">
                    Nama Pengguna
                </label> <br>
                <div class="body-content">
                    {{ $content['name'] }}
                </div>
            </div>

            <?php if ($content["tnos_service_id"] == "3" && $content["tnos_subservice_id"] == "6") : ?>
            <div class="form-group jarak">
                <label class="title-content">
                    Keperluan
                </label> <br>
                <div class="body-content">
                    {{ $content['needs'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Dokumen Tambahan
                </label> <br>
                <div class="body-content">
                    @php
                        $data = json_decode($content['file_document'], true);
                    @endphp

                    {{ $data[0]['image_url'] }}
                </div>
            </div>
            <?php endif ?>

            <?php if ($content["tnos_service_id"] == "3" && $content["tnos_subservice_id"] == "1") : ?>
            <div class="form-group jarak">
                <label class="title-content">
                    Jenis Badan Usaha
                </label> <br>
                <div class="body-content">
                    {{ json_decode($content['klasifikasi'], true)['label'] }}
                </div>
            </div>
            <?php endif ?>

            <?php if ($content["tnos_service_id"] == "3" && $content["tnos_subservice_id"] != "6") : ?>
            <div class="form-group jarak">
                <label class="title-content">
                    KTP & NPWP Seluruh
                    <?php if ($content["service_datas"]["id"] === 2 || $content["service_datas"]["id"] === 3) : ?>
                    Pemegang Saham
                    <?php else : ?>
                    Pengurus
                    <?php endif ?>
                </label> <br>
                <div class="body-content">
                    @php
                        $data = json_decode($content['file_document'], true);
                    @endphp

                    <a href="{{ $data[0]['image_url'] }}" target="_blank">
                        {{ $data[0]['image_url'] }}
                    </a>
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Nama
                    <?php if ($content["service_datas"]["id"] === 2 || $content["service_datas"]["id"] === 3) : ?>
                    Usaha
                    <?php elseif ($content["service_datas"]["id"] === 4) : ?>
                    Yayasan
                    <?php elseif ($content["service_datas"]["id"] === 5) : ?>
                    Perkumpulan
                    <?php endif ?>
                </label> <br>
                <div class="body-content">
                    @php
                        $data = json_decode($content['name_badan_hukum'], true);
                    @endphp

                    @foreach ($data as $key => $item)
                        {{ $key + 1 }}. {{ $item['opsi'] }} @if (!$loop->last)
                            <br>
                        @endif
                    @endforeach
                </div>
            </div>

            <?php if ($content["tnos_service_id"] === 3 && $content["tnos_subservice_id"] !== 4) : ?>
            <div class="form-group jarak">
                <label class="title-content">
                    Modal Dasar Perusahaan (Fiktif)
                </label> <br>
                <div class="body-content">
                    IDR {{ number_format($content['modal_dasar'], 0, ',', '.') }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Jumlah modal yang disetor (Min. 25%)
                </label> <br>
                <div class="body-content">
                    {{ $content['modal_disetor'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Susunan Pemegang Saham(Tuan/Nyonya ______ sebanyak ___ %)
                </label> <br>
                <div class="body-content">
                    @php
                        $data = json_decode($content['pemegang_saham'], true);
                        $output = '';

                        foreach ($data as $key => $item) {
                            $nomor_urut = $key + 1;
                            $output .= $nomor_urut . '. ' . $item['name'] . ', ';
                        }

                        $output = rtrim($output, ', ');

                        echo $output;
                    @endphp
                </div>
            </div>
            <?php endif ?>

            <div class="form-group jarak">
                <label class="title-content">
                    Susunan {{ $content["service_datas"]["id"] === 2 || $content["service_datas"]["id"] === 3 ? 'Direksi dan Komisaris' : 'Pengurus' }}
                </label> <br>
                <div class="body-content">
                    @php
                        $data = json_decode($content['susunan_direksi'], true);
                        $output = '';

                        foreach ($data as $key => $item) {
                            $nomor_urut = $key + 1;
                            $output .= $nomor_urut . '. ' . $item['name'] . ', ';
                        }

                        $output = rtrim($output, ', ');

                        echo $output;
                    @endphp
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Bidang Usaha KBLI 2020
                </label> <br>
                <div class="body-content">
                    @php
                        $data = json_decode($content['bidang_usaha'], true);
                        $output = '';

                        foreach ($data as $key => $item) {
                            $nomor_urut = $key + 1;
                            $output .= $nomor_urut . '. ' . $item['label'] . ', ';
                        }

                        $output = rtrim($output, ', ');

                        echo $output;
                    @endphp
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Email
                    <?php if ($content["service_datas"]["id"] === 2 || $content["service_datas"]["id"] === 3) : ?>
                    Usaha
                    <?php elseif ($content["service_datas"]["id"] === 4) : ?>
                    Yayasan
                    <?php elseif ($content["service_datas"]["id"] === 5) : ?>
                    Perkumpulan
                    <?php endif ?>
                </label> <br>
                <div class="body-content">
                    {{ $content['email_badan_hukum'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Nomor HP Penanggung Jawab
                </label> <br>
                <div class="body-content">
                    {{ $content['phone_badan_hukum'] }}
                </div>
            </div>

            <div class="form-group jarak">
                <label class="title-content">
                    Detail Alamat
                </label> <br>
                <div class="body-content">
                    @php
                        $alamat = json_decode($content['alamat_badan_hukum'], true);

                        $domisili_sekarang = $alamat['domisili_sekarang'];
                        $jalan = $alamat['jalan'];
                        $rt = $alamat['rt'];
                        $rw = $alamat['rw'];
                        $label_kelurahan = $alamat['kelurahan']['label'];
                        $label_kecamatan = $alamat['kecamatan']['label'];
                        $label_kabupaten = $alamat['kabupaten']['label'];
                        $label_provinsi = $alamat['provinsi']['label'];
                        $kode_pos = $alamat['kode_pos'];
                    @endphp

                    {{ $domisili_sekarang }}, {{ $jalan }}, RT : {{ $rt }}, RW : {{ $rw }}, {{ $label_kelurahan }},
                    {{ $label_kecamatan }}, {{ $label_kabupaten }}, {{ $label_provinsi }}, {{ $kode_pos }}
                </div>
            </div>
            <?php endif ?>
            <?php endif ?>
            <div class="form-group jarak">
                <label class="title-content">
                    Pendapatan Vendor
                </label> <br>
                <div class="body-content">
                    IDR {{ number_format($content['pendapatan_mitra'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

</body>

</html>
