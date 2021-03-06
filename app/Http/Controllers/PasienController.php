<?php

namespace App\Http\Controllers;

use App\Interaksi;
use App\Klaster;
use App\Provinsi;
use App\Pasien;
use App\PasienStatus;
use App\PasiesnSatus;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $s = request()->s ?? "";
        $datas = Pasien::where(function($w)use($s){
            $w->where('nama_lengkap','LIKE','%'.$s.'%')->orWhere('alamat','LIKE','%'.$s.'%')->orWhereHas('provinsi',function($q)use($s){
                $q->where('nama_provinsi','LIKE','%'.$s.'%');
            })->orWhereHas('kota',function($q)use($s){
                $q->where('nama_kota','LIKE','%'.$s.'%');
            });
        })->orderBy('created_at','DESC')->paginate(10);
        return view('admin.pasien.index',compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $provinsis = Provinsi::pluck('nama_provinsi','id');
        $klasters = Klaster::pluck('nama_klaster','id');

        
      
        return view('admin.pasien.create',compact('provinsis','klasters'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        // $val = 1;
        // $cek = str_pad($val,4,"0",STR_PAD_LEFT); 
        // dd(++$cek);
        //dd($request->all());
        $request->validate([
            'nama_lengkap'=>'required|string|max:100',
            'jenis_kelamin'=>'required',
            'alamat'=>'required',
            'tanggal_lahir'=>'required'
        ]);
        
        $no = '00000001';
        $cek = Pasien::orderBy('no','DESC')->first();
        if($cek){
            $val = $cek->no;
            $no = str_pad(++$val,8,"0",STR_PAD_LEFT);
        }

        $pasien = Pasien::create([
            'no'=>$no,
            'nama_lengkap'=>$request->nama_lengkap,
            'jenis_kelamin'=>$request->jenis_kelamin,
            'alamat'=>$request->alamat,
            'keterangan'=>$request->keterangan,
            'tanggal_lahir'=>$request->tanggal_lahir,
            'provinsi_id'=>$request->provinsi_id,
            'kota_id'=>$request->kota_id,
            'kecamatan_id'=>$request->kecamatan_id,
            'kelurahan_id'=>$request->kelurahan_id,
            'lokasi'=>$request->lokasi,
            'koordinat_lokasi'=>$request->koordinat_lokasi,
            'lokasi_provinsi_id'=>$request->provinsi_id,
            'lokasi_kota_id'=>$request->kota_id,
            'lokasi_kecamatan_id'=>$request->kecamatan_id,
            'lokasi_kelurahan_id'=>$request->kelurahan_id,
            'lokasi_tanggal'=>$request->lokasi_tanggal,
            'klaster_id'=>$request->klaster_id,
            // 'status'=>$request->status
        ]);

        PasienStatus::create([
            'status'=>$request->status,
            'keterangan'=>$request->keterangan,
            'keterangan_status'=>$request->lokasi_tanggal,
            'pasien_id'=>$pasien->id
        ]);

        for ($i=0; $i < count($request->interaksi_keterangan); $i++) { 
            $keterangan = $request->interaksi_keterangan[$i];
            $tanggal = $request->interaksi_tanggal[$i];
            $lokasi = $request->interaksi_lokasi[$i];
            $koordinat = $request->interaksi_koordinat_lokasi[$i];
            $provinsi = $request->interaksi_provinsi_id[$i];
            $kota = $request->interaksi_kota_id[$i];
            $kecamatan = $request->interaksi_kecamatan_id[$i];
            $kelurahan = $request->interaksi_kelurahan_id[$i];

        
            Interaksi::create([
                'pasien_id'=>$pasien->id,
                'keterangan'=>$keterangan,
                'tanggal_interaksi'=>$tanggal,
                'lokasi'=>$lokasi,
                'koordinat_lokasi'=>$koordinat,
                'provinsi_id'=>$provinsi,
                'kota_id'=>$kota,
                'kecamatan_id'=>$kecamatan,
                'kelurahan_id'=>$kelurahan,
            ]);

        }

        return redirect(route('admin.pasien.index'))->with(['success'=>'Menambah Data Pasien Baru Dengan Nama : '.$pasien->nama_lengkap]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function show(Pasien $pasien)
    {
        return view('admin.pasien.show',compact('pasien'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function edit(Pasien $pasien)
    {   
      
        $provinsis = Provinsi::pluck('nama_provinsi','id');
        $klasters = Klaster::pluck('nama_klaster','id');
      
        return view('admin.pasien.edit',compact('provinsis','klasters','pasien'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pasien $pasien)
    {   
        $request->validate([
            'nama_lengkap'=>'required|string|max:100',
            'jenis_kelamin'=>'required',
            'alamat'=>'required'
        ]);

        $pasien->update([
            'nama_lengkap'=>$request->nama_lengkap,
            'jenis_kelamin'=>$request->jenis_kelamin,
            'alamat'=>$request->alamat,
            'keterangan'=>$request->keterangan,
            'tanggal_lahir'=>$request->tanggal_lahir,
            'provinsi_id'=>$request->provinsi_id,
            'kota_id'=>$request->kota_id,
            'kecamatan_id'=>$request->kecamatan_id,
            'kelurahan_id'=>$request->kelurahan_id,
            'lokasi'=>$request->lokasi,
            'koordinat_lokasi'=>$request->koordinat_lokasi,
            'lokasi_provinsi_id'=>$request->provinsi_id,
            'lokasi_kota_id'=>$request->lokasi_kota_id,
            'lokasi_kecamatan_id'=>$request->kecamatan_id,
            'lokasi_kelurahan_id'=>$request->kelurahan_id,
            'lokasi_tanggal'=>$request->lokasi_tanggal,
            'klaster_id'=>$request->klaster_id,
            // 'status'=>$request->status
        ]);

        PasienStatus::where('pasien_id',$pasien->id)->delete();

        for ($i=0; $i < count($request->status); $i++) { 
            PasienStatus::create([
                'status'=>$request->status[$i],
                'keterangan_status'=>$request->keterangan_status[$i],
                'tanggal_status'=>$request->tanggal_status[$i],
                'pasien_id'=>$pasien->id
            ]);
        }
        
        Interaksi::where('pasien_id',$pasien->id)->delete();
        for ($i=0; $i < count($request->interaksi_keterangan); $i++) { 
            $keterangan = $request->interaksi_keterangan[$i];
            $tanggal = $request->interaksi_tanggal[$i];
            $lokasi = $request->interaksi_lokasi[$i];
            $koordinat = $request->interaksi_koordinat_lokasi[$i];
            $provinsi = $request->interaksi_provinsi_id[$i];
            $kota = $request->interaksi_kota_id[$i];
            $kecamatan = $request->interaksi_kecamatan_id[$i];
            $kelurahan = $request->interaksi_kelurahan_id[$i];

          
            Interaksi::create([
                'pasien_id'=>$pasien->id,
                'keterangan'=>$keterangan,
                'tanggal_interaksi'=>$tanggal,
                'lokasi'=>$lokasi,
                'koordinat_lokasi'=>$koordinat,
                'provinsi_id'=>$provinsi,
                'kota_id'=>$kota,
                'kecamatan_id'=>$kecamatan,
                'kelurahan_id'=>$kelurahan,
            ]);

        }

        return redirect(route('admin.pasien.index'))->with(['success'=>'Mengupdate Data Pasien Dengan Nama : '.$pasien->nama_lengkap]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pasien $pasien)
    {   

        if($pasien->pasiens()->count() > 0){
            return redirect(route('admin.pasien.index'))->with(['warning'=>'Data Pasien Dengan Nama : '.$pasien->nama_lengkap.' Masih Memiliki Data Pasien']); 
        }

        if($pasien->kotas()->count() > 0){
            return redirect(route('admin.pasien.index'))->with(['warning'=>'Data Pasien Dengan Nama : '.$pasien->nama_lengkap.' Masih Memiliki Data Kota']); 
        }

        $pasien->delete();
        return redirect(route('admin.pasien.index'))->with(['success'=>'Menghapus Data Pasien Dengan Nama : '.$pasien->nama_lengkap]);

        
    }


    public function searchSelect2(Request $request)
    {   
        if ($request->ajax()) {
            $page = $request->page;
            $resultCount = 5;
    
            $offset = ($page - 1) * $resultCount;
    
            $locations = Pasien::where('nama_lengkap', 'LIKE', '%' . $request->term. '%')
                                    ->orderBy('nama_lengkap')
                                    ->skip($offset)
                                    ->take($resultCount)
                                    ->selectRaw('id, nama_lengkap as text')
                                    ->get();
    
            $count = Count(Pasien::where('nama_lengkap', 'LIKE', '%' . $request->term. '%')
                                    ->orderBy('nama_lengkap')
                                    ->selectRaw('id, nama_lengkap as text')
                                    ->get());
    
            $endCount = $offset + $resultCount;
            $morePages = $count > $endCount;
    
            $results = array(
                  "results" => $locations,
                  "pagination" => array(
                      "more" => $morePages
                  )
              );
    
            return response()->json($results);
        }
        return response()->json('oops');
    }
}
