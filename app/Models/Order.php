<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Masterprice;
use App\Models\OrderStatusDeka;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Xendit\Xendit;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'b2b_orders';

    protected $keyType = "string";

    protected $fillable = [
        'id', 'tnos_invoice_id', 'tnos_service_id', 'tnos_subservice_id', 'external_id', 'invoice_id', 'partner_id', 'user_id', 'type', 'needs', 'name', 'email', 'phone', 'time',
        'duration', 'location', 'klasifikasi', 'jml_personil', 'file_document', 'code', 'name_badan_hukum',
        'modal_dasar', 'modal_disetor', 'alamat_badan_hukum', 'pemegang_saham', 'susunan_direksi',
        'bidang_usaha', 'email_badan_hukum', 'phone_badan_hukum', 'waktu_kerja', 'order_total', 'pendapatan_tnos',
        'pendapatan_mitra', 'pendapatan_partner', 'status_order', 'payment_status', 'payment_method', 'payment_channel',
        'paid_amount', 'tracking_status', 'paid_at', 'expiry_date', 'start_lattitude', 'start_longitude', 'end_lattitude', 'end_longitude', 'jarak',
        'start_address', 'end_address', 'biaya_pengamanan', 'biaya_tekhnical_meeting', 'biaya_makan', 'biaya_transport', 'keperluan_pengamanan', 'nama_pic', 'nomor_pic', 'tanggal_mulai',
        'jam_mulai', 'durasi_pengamanan', 'jumlah_tenaga_pengamanan', 'jenis_layanan', 'deleted_at', 'created_at', 'updated_at', 'biaya_survey',
        'finished_at', 'no_referensi', 'date_paid_vendor', 'note_paid_vendor', 'bank_paid_vendor', 'report_status', 'notes'
    ];

    public function history_pwan()
    {
        return $this->hasOne(History::class, "b2b_orders_id", "id");
    }

    public function partnerdeka()
    {
        return $this->belongsTo('App\Models\Partnerdeka', 'partner_id', 'id');
    }

    public static function generate_distance($endLat, $endLng, $startLat, $startLang)
    {
        $earthRadius = 3958.75;

        $lat1 = $startLat;
        $lat2 = $endLat;
        $lng1 = $startLang;
        $lng2 = $endLng;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);


        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $dist = $earthRadius * $c;

        // from miles
        $meterConversion = 1609;
        $geopointDistance = $dist * $meterConversion / 1000;

        return floor($geopointDistance);
    }

    public static function generate_lat_lang($location)
    {

        // $mapsaddress = \GoogleMaps::load('geocoding')
        //     ->setParam(['address' =>  $location])
        //     ->get();

        // $maps = json_decode($mapsaddress);
        // foreach ($maps as $map) {
        //     foreach ($map as $mapdatas) {
        //         return $mapdatas->geometry->location;
        //     }
        // }
    }

    public static function data_badan_hukun($request,$data,$harga_user,$pendapatan_mitra,$pendapatan_tnos,$type, $pendapatan_partner, $layanan, $description, $kode)
    {
        $date_ym = date('ym');
        $date_between = [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59')];

        $dataOrders = Order::select('code')
            ->where('type', 'deka')
            ->whereBetween('created_at', $date_between)
            ->orderBy('code', 'desc')
            ->first();
        //   return $dataOrders;
        //      die;
        if (is_null($dataOrders)) {
            $nowcode = '00001';
        } else {
            $lastcode = $dataOrders->code;
            $lastcode1 = intval(substr($lastcode, -5)) + 1;
            $nowcode = str_pad($lastcode1, 5, '0', STR_PAD_LEFT);
        }

        $develop = false;
        if ($develop) {
            $outputDevelop = "TESTING-";
        } else {
            $outputDevelop = "";
        }

        $dataBadanHukum = [
            'tnos_service_id' => $request->tnos_service_id,
            'tnos_subservice_id' => $request->tnos_subservice_id,
            'external_id' => $outputDevelop . 'TNOS-' . $kode . "-" . time(),
            'needs' => !empty($description) ? $description : ($request->needs ? $request->needs : ""),
            'code'  => 'No.2023/DLINV/' . $date_ym . $nowcode,
            'tracking_status'   => 'Belum Bayar',
            'user_id' => $request->user_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'file_document' => $data ? json_encode($data) : null,
            'name_badan_hukum' => $request->name_badan_hukum ? json_encode($request->name_badan_hukum) : null,
            'modal_dasar' => $request->modal_dasar ? $request->modal_dasar : 0,
            'modal_disetor' => $request->modal_disetor ? $request->modal_disetor : 0,
            'alamat_badan_hukum' => $request->alamat_badan_hukum ? json_encode($request->alamat_badan_hukum) : $request->alamat_badan_hukum,
            'pemegang_saham' => $request->pemegang_saham ? json_encode($request->pemegang_saham) : null,
            'susunan_direksi' => $request->susunan_direksi ? json_encode($request->susunan_direksi) : null,
            'bidang_usaha' => $request->bidang_usaha ? json_encode($request->bidang_usaha) : null,
            'email_badan_hukum' => $request->email_badan_hukum ? $request->email_badan_hukum : 0,
            'phone_badan_hukum' => $request->phone_badan_hukum ? $request->phone_badan_hukum : 0,
            'klasifikasi' => $request->klasifikasi ? json_encode($request->klasifikasi) : null,
            'order_total' => $harga_user,
            'pendapatan_mitra' => $pendapatan_mitra,
            'pendapatan_tnos' => $pendapatan_tnos,
            'status_order' => '001',
            'payment_status' => 'ORDER',
            'type'  => !empty($type === "deka") ? "deka" : "tnos",
            'partner_id' => empty($request->partner["value"]) ? 0 : $request->partner['value'],
            'pendapatan_partner' => $pendapatan_partner,
            // 'updated_at' => ""
        ];

        return $dataBadanHukum;
    }

    public static function inorder_deka_badan_hukum($request, $type, $datas)
    {
        $klasifikasi = $request->klasifikasi ? $request->klasifikasi['value'] : null;

        DB::beginTransaction();
        try {
            $harga_dasar = 0;
            $harga_total = 0;
            $harga_user = 0;
            $pendapatan_tnos = 0;
            $pendapatan_mitra = 0;

            $description = "";
            $kode = "";
            if ($datas['tnos_service_id'] === "2") {
                if ($datas['tnos_subservice_id'] === "2") {
                    $kode = "PK";
                } else {
                    $kode = "";
                }
            } elseif ($datas['tnos_service_id'] === "3") {
                if ($datas['tnos_subservice_id'] === "1") {
                    $kode = "PT";
                    if ($klasifikasi == '1') {
                        $harga_dasar = 8000000;
                    } elseif ($klasifikasi == '2') {
                        $harga_dasar = 6000000;
                    } elseif ($klasifikasi == '3') {
                        $harga_dasar = 5500000;
                    } elseif ($klasifikasi == '4') {
                        $harga_dasar = 1500000;
                    } else {
                        $harga_dasar = 3500000;
                    }

                    $description = "Pembuatan Badan Usaha PT";
                } elseif ($datas['tnos_subservice_id'] === "2") {
                    $kode = "CV";
                    $harga_dasar = 3500000;
                    $description = "Pembuatan Badan Usaha CV";
                } elseif ($datas['tnos_subservice_id'] === "3") {
                    $kode = "YA";
                    $harga_dasar = 5000000;
                    $description = "Pembuatan Badan Hukum Yayasan";
                } elseif ($datas['tnos_subservice_id'] === "4") {
                    $kode = "PN";
                    $harga_dasar = 5000000;
                    $description = "Pembuatan Badan Hukum Perkumpulan";
                } elseif ($datas['tnos_subservice_id'] === "5") {
                    $kode = "AS";
                    $harga_dasar = 3500000;
                    $description = "Pembuatan Badan Hukum Asosiasi";
                } elseif ($datas['tnos_subservice_id'] === "6") {
                    $kode = "LN";
                    $harga_dasar = $request->harga_total;
                    $description = $request->needs;
                } elseif ($datas['tnos_subservice_id'] === "7") {
                    $kode = "SH";
                    $harga_dasar = 5000000;
                    $description = $request->needs;
                } else {
                    $kode = "";
                }
            } else {
                $kode = "ERR";
            }

            $harga_total = $harga_dasar;
            $harga_user = $harga_dasar;
            $pendapatan_tnos = $harga_total * 0.1;
            $pendapatan_mitra = $harga_total * 0.9;


            $data = [];
            if ($request->file('file_document')) {
                foreach ($request->file('file_document') as $file) {
                    $getImage = $file;
                    $imageName = rand(10000000, 99999999) . time() .  '.' . $getImage->getClientOriginalExtension();
                    $path = public_path() . '/images/' . $kode . '/';
                    $imagePath = URL::to('/') . '/images/' . $kode . '/' . $imageName;
                    $getImage->move($path, $imageName);

                    $image = array(
                        "image_name" => $imageName,
                        "image_url" => $imagePath,
                        "mime" => $getImage->getClientMimeType()
                    );

                    $data[] = $image;
                }
                $data = $data ? $data : [];
            }

            $pendapatan_partner = 0;
            $layanan = "";

            $data_hukum = Order::data_badan_hukun($request,$data,$harga_user,$pendapatan_mitra,$pendapatan_tnos,$type, $pendapatan_partner, $layanan, $description, $kode);
            $order = Order::create($data_hukum);

            $tracking = OrderStatusDeka::create([
                'id_order'  => $order['id'],
                'status'    => 'belum bayar',
                'datetime'  => date('Y-m-d H:i:s'),
                'deskripsi' => 'pesanan telah di buat'
            ]);
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong (Try Catch)',
                'error' => $e,
                'request_all' => $request->all()
            ], 500);
        }
    }

    public static function in_order_badan_hukum($request,$type)
    {
        // dd($request->all());
        $klasifikasi = $request->klasifikasi ? $request->klasifikasi['value'] : null;
        DB::beginTransaction();
        try {
            $harga_dasar = 0;
            $harga_total = 0;
            $harga_user = 0;
            $pendapatan_tnos = 0;
            $pendapatan_mitra = 0;
            $partner_percent = 0;

            $kode = "";
            $layanan = "";
            $description = "";
            if ($request->tnos_service_id === "2") {
                if ($request->tnos_subservice_id === "2") {
                    $kode = "PK";
                } else {
                    $kode = "";
                }
            } elseif ($request->tnos_service_id === "3") {
                if ($request->tnos_subservice_id === "1") {
                    $kode = "PT";
                    if ($klasifikasi == '1') {
                        $harga_dasar = 8000000;
                    } elseif ($klasifikasi == '2') {
                        $harga_dasar = 6000000;
                    } elseif ($klasifikasi == '3') {
                        $harga_dasar = 5500000;
                    } elseif ($klasifikasi == '4') {
                        $harga_dasar = 1500000;
                    } else {
                        $harga_dasar = 3500000;
                    }
                    $layanan = "Badan Usaha PT";
                    $description = "Pembuatan Badan Usaha PT";
                } elseif ($request->tnos_subservice_id === "2") {
                    $kode = "CV";
                    $harga_dasar = 3500000;

                    $layanan = "Badan Usaha CV";
                    $description = "Pembuatan Badan Usaha CV";
                } elseif ($request->tnos_subservice_id === "3") {
                    $kode = "YA";
                    $harga_dasar = 5000000;

                    $layanan = "Badan Hukum Yayasan";
                    $description = "Pembuatan Badan Hukum Yayasan";
                } elseif ($request->tnos_subservice_id === "4") {
                    $kode = "PN";
                    $harga_dasar = 5000000;

                    $layanan = "Badan Hukum Perkumpulan";
                    $description = "Pembuatan Badan Hukum Perkumpulan";
                } elseif ($request->tnos_subservice_id === "5") {
                    $kode = "AS";
                    $harga_dasar = 3500000;

                    $layanan = "Badan Hukum Asosiasi";
                    $description = "Pembuatan Badan Hukum Asosiasi";
                } elseif ($request->tnos_subservice_id === "6") {
                    $kode = "LN";
                    $harga_dasar = $request->harga_total;

                    $layanan = "Legalitas Lainnya";
                    $description = $request->needs;
                } elseif ($request->tnos_subservice_id === "7") {
                    $kode = "SH";
                    $harga_dasar = 5000000;

                    $layanan = "Solusi Hukum";
                    $description = $request->needs;
                } elseif ($request->tnos_subservice_id === "8") {
                    $kode = "PL";
                    $harga_dasar = $request->harga_total;

                    $layanan = "Pembayaran Lainnya";
                    $description = $request->needs;
                } else {
                    $kode = "";
                }
            } else {
                $kode = "ERR";
            }

            $harga_total = $harga_dasar;
            $harga_user = $harga_dasar;
            $percenTnos = 0;
            $percenMitra = 0;

            if ($request->tnos_service_id == "3" && $request->tnos_subservice_id == "8") {
                $percenTnos = 0;
                $percenMitra = 0;
            } else {
                if (!empty($request->partner["value"])) {
                    $cek_partner = Partnerdeka::where("id", $request->partner["value"])->first();

                    $partner_percent = $cek_partner->komisi_percent_partner / 100;
                    $percenTnos = $cek_partner->tnos_percent / 100;
                    $percenMitra = $cek_partner->deka_percent / 100;
                } else {
                    $percenTnos = 20 / 100;
                    $percenMitra = 80 / 100;
                }

            }

            $pendapatan_tnos = $harga_total * $percenTnos;
            $pendapatan_mitra = $harga_total * $percenMitra;
            $pendapatan_partner = $harga_user * $partner_percent;

            $data = [];
            if ($request->file('file_document')) {
                foreach ($request->file('file_document') as $file) {
                    $getImage = $file;
                    $imageName = rand(10000000, 99999999) . time() .  '.' . $getImage->getClientOriginalExtension();
                    $path = public_path() . '/images/' . $kode . '/';
                    $imagePath = URL::to('/') . '/images/' . $kode . '/' . $imageName;
                    $getImage->move($path, $imageName);

                    $image = array(
                        "image_name" => $imageName,
                        "image_url" => $imagePath,
                        "mime" => $getImage->getClientMimeType()
                    );

                    $data[] = $image;
                }
                $data = $data ? $data : [];
            }

            $data_hukum = Order::data_badan_hukun($request,$data,$harga_user,$pendapatan_mitra,$pendapatan_tnos,$type, $pendapatan_partner, $layanan, $description, $kode);
            $order = Order::create($data_hukum);
            return $order;

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'request_all' => $request->all()
            ], 422);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage(),
                'request_all' => $request->all()
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
                'request_all' => $request->all()
            ], 500);
        }
    }

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (Order $data) {
            $data->id = Str::uuid()->toString();
        });
    }
}
