<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function create()
    {
        $hari_ini = date("Y-m-d");
        $nis = Auth::guard('siswa')->user()->nis;
        $cek = DB::table('absensi')->where('tgl_absen', $hari_ini)->where('nis', $nis)->count();
        return view('absensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $nis = Auth::guard('siswa')->user()->nis;
        $tgl_absensi = date("Y-m-d");
        $jam = date("H:i:s");
        $lokasi = $request->lokasi;
        $image = $request->image;
        $folderpath = "public/uploads/absensi/";
        $formatName = $nis . "-" . $tgl_absensi;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $filename = $formatName . ".png";
        $file = $folderpath . $filename;
        
        $cek = DB::table('absensi')->where('tgl_absen', $tgl_absensi)->where('nis', $nis)->count();
        if($cek > 0) {
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $filename,
                'lokasi_out' => 'sidowangi'
            ];
            $update = DB::table('absensi')->where('tgl_absen', $tgl_absensi)->where('nis', $nis)->update($data_pulang);
            if($update){
                echo "success|Hati-hati di Jalan!|out";
                Storage::put($file,$image_base64);
            }else{
                echo "error|Gagal Absen! Hubungi Operator|out";
            }
        } else {
            $data = [
                'nis' => $nis,
                'tgl_absen' => $tgl_absensi,
                'jam_in' => $jam,
                'foto_in' => $filename,
                'lokasi_in' => 'sidowangi'
            ];
    
            $simpan = DB::table('absensi')->insert($data);
            if($simpan){
                echo "success|Selamat Belajar!|in";
                Storage::put($file,$image_base64);
            }else{
                echo "error|Gagal Absen! Hubungi Operator|in";
            }
        }
    }
}
