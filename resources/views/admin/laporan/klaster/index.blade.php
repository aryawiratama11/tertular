@extends('admin.layouts.app')


@section('breadcrumb')
    <li class="breadcrumb-item active">Laporan Klaster</li>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <div class="float-left">
            <b>
                Laporan Klaster
            </b>
        </div>
        <div class="float-right">
            <a href="{{ route('admin.laporan.export','klaster') }}" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
        
    <div class="card-body">
        
       

        <table class="table">
            <thead>
                <tr>
                    <th>
                        Klaster
                    </th>
                    <th>
                        Jumlah Kasus
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                    <tr>
                        <td>
                            {{ $data->nama_klaster }}
                        </td>
                        <td>
                            Total Pasien : {{ $data->total_pasien }}
                            <br>
                            Total Kasus Positif : {{ $data->total_kasus_positif }}
                            <br>
                            Total Kasus Sembuh : {{ $data->total_kasus_sembuh }}
                            <br>
                            Total Kasus Meninggal : {{ $data->total_kasus_meninggal }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
           
        </table>
    </div>
</div>
@endsection
