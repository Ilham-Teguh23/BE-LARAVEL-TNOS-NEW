<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPengamananKorporat;
use App\Models\User;
use App\Notifications\LaravelTelegramNotification;
use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Polyfill\Intl\Idn\Idn;
use Xendit\Xendit;
use \KMLaravel\GeographicalCalculator\Facade\GeoFacade;
use Carbon\CarbonImmutable;

class OrderPass extends Controller
{
    protected $serverKey;


    public function __construct()
    {
        // update
        $this->serverKey = config('xendit.xendit_development_key');
    }

    public function create(Request $request)
    {
        $type = $request->type;
        $biayaMeeting = 0;
        $biayaTransport = 0;
        $harga_total = 0;
        $harga_user = 0;
        $harga_personil = 0;
        $biayaPengamanan = 0;
        $kode = "";
        $biayaMakan = 0;
        $jarak = $request->jarak;
        $masterBiayaMakanHalfDay = 50000;
        $masterBiayaMakanFullDay = 100000;
        $tenagaPengamanan = $request->jumlah_tenaga_pengamanan;
        $biayaPersonilDelapan = 662000;
        $biayaPersonilDuaBelas = 810000;
        $totalBiayaPersonil = 0;
        $feeTNOSDelapan = 90000;
        $feeTNOSDuaBelas = 100000;
        $totalFeeTNOS = 0;

        if ($type === "Triger") {

            $biayaMeeting += 500000;
            if ($request->durasi_pengamanan === 4 || $request->durasi_pengamanan === 5 || $request->durasi_pengamanan === 6 | $request->durasi_pengamanan === 7) {
                $biayaMakan += $masterBiayaMakanHalfDay * $tenagaPengamanan;
            }elseif ($request->durasi_pengamanan >= 8) {
                $biayaMakan += $masterBiayaMakanFullDay * $tenagaPengamanan;
            }

            if ($request->durasi_pengamanan === 4) {
                $halfday = 600000;
                $biayaPengamanan += $halfday * $tenagaPengamanan;
            }

            if ($request->durasi_pengamanan === 8) {
               $fullday = 1100000;
               $biayaPengamanan += $fullday * $tenagaPengamanan;
            }

            if ($jarak <= 10) {
                $biayaTransport += 0;
            }elseif ($jarak > 10) {
               $biayaTransport += 60000 * $tenagaPengamanan;
            }

            $kode = "TRG-PK";

            $harga_personil *= $tenagaPengamanan;
            $totalFeeTNOS = $biayaPengamanan * 0.2;
            $harga_user += $harga_personil + $harga_total + $biayaMakan + $biayaMeeting + $biayaTransport + $biayaPengamanan;

            $totalBiayaPersonil = ($biayaPengamanan * 0.8) + $biayaMakan + $biayaTransport + $biayaMeeting;
        } else if ($type === "PAS") {

            $kode = "PAS-PK";
            if ($request->biaya_survey == "1") {
                $biayaMeeting = 500000;
            }

            if ($request->durasi_pengamanan === 8) {
                $totalBiayaPersonil = $biayaPersonilDelapan * $tenagaPengamanan;
                $totalFeeTNOS = $feeTNOSDelapan * $tenagaPengamanan;
            } else if ($request->durasi_pengamanan === 12) {
                $totalBiayaPersonil =  $biayaPersonilDuaBelas * $tenagaPengamanan;
                $totalFeeTNOS = $feeTNOSDuaBelas * $tenagaPengamanan;
            }

            $biayaPengamanan += $totalBiayaPersonil + $totalFeeTNOS;

            $harga_user += $biayaPengamanan + $biayaMeeting;

            $totalBiayaPersonil = ($biayaPengamanan + $biayaMeeting) - $totalFeeTNOS;
        }

        $data = [
            'tnos_service_id'           => $request->tnos_service_id,
            'tnos_subservice_id'        => $request->tnos_subservice_id,
            'external_id'               => 'TNOS-' . $kode . "-" . time(),
            'user_id'                   => $request->user_id,
            'name'                      => $request->name,
            'email'                     => $request->email,
            'phone'                     => $request->phone,
            'location'                  => $request->location,
            'start_lattitude'           => $request->start_lattitude,
            'start_longitude'           => $request->start_long,
            'end_lattitude'             => $request->end_lattitude,
            'end_longitude'             => $request->end_longitude,
            'jarak'                     => $request->jarak,
            'start_address'             => $request->start_address,
            'end_address'               => $request->end_address ,
            'needs'                     => $request->keperluan_pengamanan,
            'nama_pic'                  => $request->nama_pic,
            'nomor_pic'                 => $request->nomor_pic,
            'tanggal_mulai'             => $request->tanggal_mulai,
            'jam_mulai'                 => $request->jam_mulai,
            'durasi_pengamanan'         => $request->durasi_pengamanan,
            'jumlah_tenaga_pengamanan'  => $tenagaPengamanan,
            'type'                      => "tnos",
            'jenis_layanan'             => $request->jenis_layanan,
            'biaya_tekhnical_meeting'   => $biayaMeeting,
            'biaya_makan'               => empty($biayaMakan) ? 0 : $biayaMakan,
            'biaya_transport'           => empty($biayaTransport) ? 0 : $biayaTransport,
            'biaya_pengamanan'          => empty($biayaPengamanan) ? 0 : $biayaPengamanan,
            'jml_personil'              => $tenagaPengamanan,
            'order_total'               => $harga_user,
            'alamat_badan_hukum'        => $request->location,
            'pendapatan_mitra'          => empty($totalBiayaPersonil) ? 0 : $totalBiayaPersonil,
            'pendapatan_tnos'           => empty($totalFeeTNOS) ? 0 : $totalFeeTNOS,
            'biaya_survey'              => $request->biaya_survey == "1" ? "Ya" : "Tidak"
        ];

        $order = Order::create($data);

        $response = Order::where('id',$order->id)->first();
        return response()->json([
            'code'    => 200,
            'message' => 'success',
            'detail'  => $response
        ]);
    }

    public  function order_invoice($id)
    {
        $order = Order::where('id',$id)->first();

        $namaUsaha = json_decode($order->name_badan_hukum);

        $namabadanhukum = json_decode($order->name_badan_hukum);
        $klasifikasi = json_decode($order->klasifikasi);
        $bidangUsaha = json_decode($order->bidang_usaha);
        $alamat = json_decode($order->alamat_badan_hukum);
        $alamatLengkap = 'Jalan '. $alamat->jalan . ' RT:' . $alamat->rt . ' RW:' . $alamat->rw . '  kelurahan:'.$alamat->kelurahan->label . '  kecamatan: ' .$alamat->kecamatan->label . '  provinsi: ' .$alamat->provinsi->label;

        $data = [
            'opsi1' => $namabadanhukum[0]->opsi,
            'code'      => $order->code,
            'price'     => "Rp." . number_format($order->order_total, 2, ",", "."),
            'alamat'    => $alamatLengkap,
            'namausaha' => $namaUsaha[0]->opsi,
            'deskripsi' => $klasifikasi->label . ' (' . $bidangUsaha[0]->value . ' )',
            'data' => $order,
        ];

        $pdf =  PDF::loadView('invoice', $data); // takes max execution time to load pdf
        return $pdf->download('invoice.pdf');

    }
}
