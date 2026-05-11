<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
@page { size: A4 landscape; margin: 1.2cm 1.5cm; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #111; }

.header { text-align: center; border-bottom: 2px solid #111; padding-bottom: 8px; margin-bottom: 12px; }
.header h1 { font-size: 13pt; font-weight: bold; margin-bottom: 2px; }
.header h2 { font-size: 10pt; font-weight: normal; color: #444; }

.info-block { margin-bottom: 12px; }
.info-block table { font-size: 8.5pt; }
.info-block td { padding: 2px 4px; vertical-align: top; }
.info-block td.label { font-weight: bold; width: 130px; white-space: nowrap; }
.info-block td.sep { width: 8px; }

table.matrix { width: 100%; border-collapse: collapse; font-size: 7pt; margin-top: 4px; }
table.matrix th { background-color: #dce6f1; border: 1px solid #555; padding: 3px 2px; text-align: center; font-weight: bold; font-size: 7pt; }
table.matrix td { border: 1px solid #888; padding: 3px 2px; text-align: center; vertical-align: middle; }
table.matrix td.nama { text-align: left; padding-left: 5px; font-size: 7.5pt; }
table.matrix td.nim { font-size: 7pt; }
table.matrix tbody tr:nth-child(even) { background-color: #f5f5f5; }

.st-H { color: #166534; font-weight: bold; }
.st-S { color: #1d4ed8; font-weight: bold; }
.st-I { color: #92400e; font-weight: bold; }
.st-A { color: #b91c1c; font-weight: bold; }
.pct-ok  { color: #166534; font-weight: bold; }
.pct-low { color: #b91c1c; font-weight: bold; }

.legend { margin-top: 10px; font-size: 7.5pt; color: #555; border-top: 1px solid #ccc; padding-top: 6px; }
.sign-row { margin-top: 28px; }
.sign-row table { width: 100%; }
.sign-box { text-align: center; font-size: 8.5pt; }
.sign-box .line { margin-top: 44px; border-top: 1px solid #555; padding-top: 3px; font-weight: bold; }
</style>
</head>
<body>

<div class="header">
    <h1>LAPORAN PRESENSI MAHASISWA</h1>
    <h2>Sistem Presensi Hybrid &mdash; Universitas Mulawarman</h2>
</div>

<div class="info-block">
    <table>
        <tr>
            <td class="label">Mata Kuliah</td>
            <td class="sep">:</td>
            <td>{{ $kelas->mataKuliah->nama_mk ?? '-' }} ({{ $kelas->mataKuliah->kode_mk ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="sep">:</td>
            <td>{{ $kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td class="label">Dosen Pengampu</td>
            <td class="sep">:</td>
            <td>{{ $kelas->dosen->nama_dosen ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tahun Ajaran</td>
            <td class="sep">:</td>
            <td>{{ $kelas->tahunAjaran->tahun_ajaran ?? '-' }} &mdash; {{ $kelas->tahunAjaran->semester ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Pertemuan</td>
            <td class="sep">:</td>
            <td>{{ $sesiList->count() }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Mahasiswa</td>
            <td class="sep">:</td>
            <td>{{ $mahasiswas->count() }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Cetak</td>
            <td class="sep">:</td>
            <td>{{ now()->translatedFormat('d F Y') }}</td>
        </tr>
    </table>
</div>

<table class="matrix">
    <thead>
        <tr>
            <th style="width:22px">No</th>
            <th style="text-align:left; padding-left:5px; min-width:120px">Nama Mahasiswa</th>
            <th style="min-width:70px">NIM</th>
            @foreach($sesiList as $i => $sesi)
            <th style="width:20px; font-size:6.5pt">
                P{{ $i+1 }}<br>
                <span style="font-weight:normal; font-size:6pt">{{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('d/m') }}</span>
            </th>
            @endforeach
            <th style="width:22px">H</th>
            <th style="width:22px">A</th>
            <th style="width:28px">%</th>
        </tr>
    </thead>
    <tbody>
        @forelse($mahasiswas as $no => $m)
        @php $row = $matrix[$m->id] ?? ['hadir' => 0, 'alpa' => 0, 'pct' => 0, 'sesi' => []]; @endphp
        <tr>
            <td>{{ $no + 1 }}</td>
            <td class="nama">{{ $m->nama_lengkap }}</td>
            <td class="nim">{{ $m->nim }}</td>
            @foreach($sesiList as $sesi)
            @php $st = $row['sesi'][$sesi->id] ?? 'A'; @endphp
            <td><span class="st-{{ $st }}">{{ $st }}</span></td>
            @endforeach
            <td><span class="st-H">{{ $row['hadir'] }}</span></td>
            <td><span class="st-A">{{ $row['alpa'] }}</span></td>
            <td><span class="{{ $row['pct'] >= 75 ? 'pct-ok' : 'pct-low' }}">{{ $row['pct'] }}%</span></td>
        </tr>
        @empty
        <tr>
            <td colspan="{{ 6 + $sesiList->count() }}" style="text-align:center; padding:12px; color:#888; font-style:italic">
                Tidak ada data mahasiswa terdaftar.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="legend">
    Keterangan: &nbsp;
    <strong class="st-H">H</strong> = Hadir &nbsp;&nbsp;
    <strong class="st-S">S</strong> = Sakit &nbsp;&nbsp;
    <strong class="st-I">I</strong> = Izin &nbsp;&nbsp;
    <strong class="st-A">A</strong> = Alpa &nbsp;&nbsp;|&nbsp;&nbsp;
    <strong class="pct-low">%</strong> merah = kehadiran di bawah 75%
</div>

<div class="sign-row">
    <table>
        <tr>
            <td style="width:65%"></td>
            <td class="sign-box">
                Samarinda, {{ now()->translatedFormat('d F Y') }}
                <div class="line">{{ $kelas->dosen->nama_dosen ?? 'Dosen Pengampu' }}</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
