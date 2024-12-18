Pertemuan 1
Buat proyek Laravel menggunakan perintah berikut:

    composer create-project --prefer-dist laravel/laravel ProjectSIA2

Tambahkan paket dompdf untuk Laravel dengan perintah:

    composer require barryvdh/laravel-dompdf

Pasang paket Sweet Alert menggunakan Composer:

    composer require realrashid/sweet-alert

Pertemuan 2
Konfigurasi Database
Nama Database: db_sia2

Ubah file .env dengan konfigurasi berikut:

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_sia2
    DB_USERNAME=root
    DB_PASSWORD=

Pembuatan Model dan Migrasi
Gunakan perintah berikut untuk membuat model dan migrasi untuk setiap entitas:

    php artisan make:model Barang -m

    php artisan make:model Supplier -m

    php artisan make:model Akun -m
    php artisan make:model Setting -m
    php artisan make:model Pemesanan -m
    php artisan make:model DetailPesan -m
    php artisan make:model Pembelian -m
    php artisan make:model DetailPembelian -m
    php artisan make:model Retur -m
    php artisan make:model DetailRetur -m
    php artisan make:model Jurnal -m
    php artisan make:model Pemesanan_tem
    php artisan make:model Temp_pesan
    php artisan make:model Beli
    php artisan make:model Laporan

Membuat trigger

    php artisan make:migration trigger_bersih_tempesan

    php artisan make:migration trigger_tambah

Membuat Migration
barang

    Schema::create('barang', function (Blueprint $table){
    $table->string('kd_brg',5)->primary();
    $table->string('nm_brg',5);
    $table->integer('harga');
    $table->integer('stok');
    });

akun

    Schema::create('akun', function (Blueprint $table) {
    $table->string('no_akun',5)->primary();
    $table->string('nm_akun',25);
    });

supplier

    Schema::create('supplier', function (Blueprint $table){
    $table->string('kd_supp',5)->primary();
    $table->string('nm_supp',25);
    $table->string('alamat',50);
    $table->string('telepon',13);
    });

pemesanan

    Schema::create('pemesanan', function (Blueprint $table) {
    $table->string('no_pesan', 14)->primary();
    $table->date('tgl_pesan');
    $table->integer('total');
    $table->string('kd_supp', 5);
    });

setting

    Schema::create('setting', function (Blueprint $table) {
    $table->string('id_setting',5)->primary();
    $table->string('no_akun',5);
    $table->string('nama_transaksi',20);
    });

detail_pesan

    Schema::create('detail_pesan', function (Blueprint $table) {
    $table->string('no_pesan', 14);
    $table->string('kd_brg', 5);
    $table->integer('qty_pesan');
    $table->integer('subtotal');
    });

temp_pemesanan

    Schema::create('temp_pemesanan', function (Blueprint $table) {
    $table->string('kd_brg', 5);
    $table->integer('qty_pesan');
    });

pembelian

    Schema::create('pembelian', function (Blueprint $table) {
    $table->string('no_beli',14)->primary();
    $table->date('tgl_beli');
    $table->string('no_faktur',14);
    $table->integer('total_beli');
    $table->string('no_pesan',14);
    });

detail_pembelian

    Schema::create('detail_pembelian', function (Blueprint $table) {
    $table->string('no_beli',14);
    $table->string('kd_brg',5);
    $table->integer('qty_beli');
    $table->integer('sub_beli');
    });

detail_retur

    Schema::create('detail_retur', function (Blueprint $table) {
    $table->string('no_retur',14);
    $table->string('kd_brg',5);
    $table->integer('qty_retur');
    $table->integer('sub_retur');
    });

retur

    Schema::create('retur', function (Blueprint $table) {
    $table->string('no_retur',14)->primary();
    $table->date('tgl_retur');
    $table->integer('total_retur');
    });

jurnal

    Schema::create('jurnal', function (Blueprint $table) {
    $table->string('no_jurnal', 14)->primary();
    $table->date('tgl_jurnal');
    $table->text('keterangan');
    $table->string('no_akun', 5);
    $table->integer('debet');
    $table->integer('kredit');
    });

membuat trigger
clear_tem_pesan

    DB::unprepared('
    CREATE TRIGGER clear_tem_pesan AFTER INSERT ON detail_pesan
    FOR EACH ROW
    BEGIN
    DELETE FROM temp_pemesanan;
    END
    ');

detail_pembelian

    DB::unprepared('
    CREATE TRIGGER after INSERT ON detail_pembelian
    FOR EACH ROW BEGIN
    UPDATE barang
    SET stok = stok + NEW.qty_beli
    WHERE
    kd_brg = NEW.kd_brg;
    END
    ');

Setelah menjalankan perintah di atas, Anda dapat melakukan migrasi dengan perintah:

    php artisan migrate:refresh --seed

Membuat view, jalankan query SQL berikut pada database Anda:
temp_pemesanan

    CREATE VIEW after AS SELECT temp_pemesanan.kd_brg AS kd_brg, concat(barang.nm_brg,barang.harga) AS nm_brg,temp_pemesanan.qty_pesan AS qty_pesan, barang.harga\* temp_pemesanan.qty_pesan AS sub_total FROM (temp_pemesanan join barang) WHERE temp_pemesanan.kd_brg = barang.kd_brg ;

tampil_pemesanan

    CREATE VIEW tampil_pemesanan AS SELECT detail_pesan.kd_brg AS kd_brg, detail_pesan.no_pesan AS no_pesan, barang.nm_brg AS nm_brg, detail_pesan.qty_pesan AS qty_pesan, detail_pesan.subtotal AS sub_total FROM (barang join detail_pesan) WHERE detail_pesan.kd_brg = barang.kd_brg ;

tampil_pembelian

    CREATE VIEW tampil_pembelian AS (select barang.kd_brg AS kd_brg,detail_pembelian.no_beli AS no_beli,barang.nm_brg AS nm_brg,barang.harga AS harga,detail_pembelian.qty_beli AS qty_beli from (barang join detail_pembelian) where barang.kd_brg = detail_pembelian.kd_brg) ;

lap_jurnal

    CREATE VIEW lap_jurnal AS SELECT akun.nm_akun AS nm_akun, jurnal.tgl_jurnal AS tgl, sum(jurnal.debet) AS debet, sum(jurnal.kredit) AS kredit FROM (akun join jurnal) WHERE akun.no_akun = jurnal.no_akun GROUP BY jurnal.no_jurnal ;

lap_stok

    CREATE VIEW lap_stok AS (select barang.kd_brg AS kd_brg,barang.nm_brg AS nm_brg,barang.harga AS harga,barang.stok AS stok,sum(detail_pembelian.qty_beli) AS beli,sum(detail_retur.qty_retur) AS retur from ((barang join detail_retur) join detail_pembelian) where barang.kd_brg = detail_retur.kd_brg and barang.kd_brg = detail_pembelian.kd_brg group by barang.kd_brg) ;

Pertemuan 3
Install Laravel UI
composer require laravel/ui

    php artisan ui vue --auth
    npm install
    npm run dev
    npm run build

    php artisan serve

    php artisan ui vue
    npm install
    npm run dev
    npm run build
    php artisan migrate

https://github.com/aleckrh/laravel-sb-admin-2

Pertemuan 4
Menambahkan role permission

    composer require spatie/laravel-permission

    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

Menambahkan spatie ke model User

    use Spatie\Permission\Traits\HasRoles;

    use HasApiTokens, HasFactory, Notifiable, HasRoles;

Membuat Role Seeder

    php artisan make:seeder RoleSeeder

// Update File database/seeder/RoleSeeder.php

    use Spatie\Permission\Models\Role;

    Role::create([
    'name' => 'admin',
    'guard_name' => 'web'
    ]);

    Role::create([
    'name' => 'user',
    'guard_name' => 'web'
    ]);

Membuat User Seeder

    php artisan make:seeder UserSeeder

// Update File database/seeder/UserSeeder.php

    use App\Models\User;

    $admin = User::create([
        'name' => 'admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
    ]);
    $admin->assignRole('admin');

    $user = User::create([
        'name' => 'user',
        'email' => 'user@test.com',
        'password' => bcrypt('password'),
    ]);
    $user->assignRole('user');

Membuat Database Seeder
// Update File database/seeder/databaseSeeder.php

    use Database\Seeders\RoleSeeder;
    use Database\Seeders\UserSeeder;

    $this->call(RoleSeeder::class);
    $this->call(UserSeeder::class);

jalankan perintah migration dan seeder

    php artisan migrate:fresh --seed

Membuat Middleware
update file app/Http/Kernel.php

    protected $middlewareAliases = [
    'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ];

Membuat permission menu
//Update file admin.blade.php -> menu Profile

    @role('admin')
    //isi menu
    @endrole

Menambahkan role ke register

    $user->assignRole('user');

Pertemuan 5
Memecah Layout

// tambahkan view di

    resources/views/layouts/partials/footer.blade.php
    resources/views/layouts/partials/navbar.blade.php
    resources/views/layouts/partials/sidebar.blade.php
