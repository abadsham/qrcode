@extends('layout.app')

@section('content')
    <div class="container">
        <h1>Daftar Sertifikat</h1>

        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr class="text-center">
                    <th scope="col">Nama Peserta</th>
                    <th scope="col">Nama Kursus</th>
                    <th scope="col">QR CODE</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="4">Belum ada data sertifikat</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection