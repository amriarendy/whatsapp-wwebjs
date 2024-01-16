<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "You no have access!!");
    redirect("../login.php");
}

require_once('./templates/header.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-info-circle"></i><b>Information</b></h1>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">App Info</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 40rem;" src="<?= $base_url ?>/public/files/dashboard.png" alt="...">
                    </div>
                    <p><b>Apa Itu Whatsapp Bot</b></p>
                    <p style="text-align: justify; ">WhatsApp Bot adalah program komputer yang menggunakan teknologi kecerdasan buatan (AI) dan otomatisasi untuk berinteraksi dengan pengguna melalui aplikasi WhatsApp. Bot ini dirancang untuk menjawab pertanyaan, memberikan informasi, dan melakukan berbagai tugas sesuai dengan permintaan pengguna. Ide di balik WhatsApp Bot adalah memberikan solusi cepat dan efisien untuk berbagai kebutuhan pengguna, tanpa perlu interaksi langsung dengan manusia.</p>
                    <p style="text-align: justify; "><b>Fitur Whatsapp Bot</b></p>
                    <p style="text-align: justify; ">Pada fitur aplikasi whatsapp bot, yang berfungsi sebagai membantu pekerjaan anda dalam melayani client. Berikut beberapa fitur dan kegunaan dari aplikasi whatsapp bot ini.</p>
                    <p style="text-align: justify; "><b>1. Send Message:</b> Untuk fitur send message ini sebagai pengirim pesan ke whatsapp ke nomor tujuan, sama seperti pengiriman whatsapp biasa, tapi pengiriman pesannya menggunakan aplikasi whatsapp bot ini.</p>
                    <p style="text-align: justify; "><b>2. Send Group Message:</b>&nbsp;Pada fitur send group message ini sama seperti fitur send message diatas, namun fitur ini ditujukan untuk pengiriman ke group saja, dengan menggunakan id_group sebagai parameter targetnya.</p>
                    <p style="text-align: justify; "><b>3. Send Media: </b>Untuk send media, kita bisa mengirim pesan media ke target nomor. Gunakan extension jpg, jpeg, png untuk mengirimkan pesan media.</p>
                    <p><b>4. Send Broadcast: </b>Pengiriman pesan secara masal. Pada fitur send broadcast ini kita harus mengisi terlebih dahulu kontak target dan id_group target, silahkan isi kontak tersebut pada halaman Master Contact di menu navbar. Masukan dengan awalan nomor <b>628xxxx</b> atau <b>08xxxx</b>. untuk id_group&nbsp;<span style="text-align: center;"><b>628******-1654******@g.us</b>. Setelah itu atur waktu pengiriman pesan broadcast.</span></p>
                    <p><span style="text-align: center;"><b>5. Autoreply:&nbsp;</b></span>WhatsApp Bot dapat memberikan respon otomatis untuk pesan teks yang diterima dari pengguna. Respon ini bisa berupa pesan sambutan, konfirmasi penerimaan pesan, atau pesan balasan khusus berdasarkan kata kunci tertentu.</p>
                    <p><b>6. Auto Message:</b> Fungsi fitur ini bisa digunakan sebagai pengiriman kode OTP, kode invoice, kode transaksi atau penggunaan fitur pada aplikasi anda lainya. jadikan aplikasi whatsapp bot ini sebagai api gateway whatsapp.</p>
                    <p><b>7. Whatsapp API Gateway: </b>Jadikan aplikasi whatsapp bot ini sebagai api gateway untuk keperluan bisnis anda, generate api_key dan kirim pesan dari aplikasi anda ke rest api whatsapp bot ini, silahkan&nbsp;<a href="https://goican.id/" target="_blank">baca dokumentasi</a>.&nbsp;</p>
                    <p><b>8. Security CSRF:</b> Aplikasi whatsapp api gateway telah diamankan menggunakan fitur CSRF (Cross Site Request Forgery).</p>
                    <p><b>9. Import Excel:</b> Untuk memudahkan dalam penginputan data master kontak gunakan import excel pada halaman master kontak.</p>
                    <p><b>10. Dashboard:</b> Terdapat grafik pengiriman pesan sukses dan gagal pada halaman awal dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require_once('./templates/footer.php'); ?>
</body>

</html>