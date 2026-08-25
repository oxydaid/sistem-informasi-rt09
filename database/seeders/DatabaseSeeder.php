<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\CashAccount;
use App\Models\CashCategory;
use App\Models\Content;
use App\Models\Event;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\OrganizationPeriod;
use App\Models\OrganizationPosition;
use App\Models\PositionMemberPeriod;
use App\Models\User;
use App\Services\FinanceService;
use App\Services\SettingsService;
use App\Services\TiptapContentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Settings
        $settingsService = app(SettingsService::class);
        $settings = [
            'site_name' => 'Sistem Informasi RT 09',
            'site_tagline' => 'Guyub Rukun, Bersih, Aman, dan Transparan',
            'site_description' => 'Portal resmi informasi warga RT 09 / RW 04 Kelurahan Sukamaju. Pusat informasi kegiatan lingkungan, transparansi keuangan kas RT, agenda, dan kepengurusan Karang Taruna.',
            'contact_address' => 'Jl. Melati Indah Blok B, RT 09 / RW 04, Kel. Sukamaju, Kec. Cilodong, Kota Depok, Jawa Barat 16415',
            'contact_phone' => '0812-3456-7890',
            'contact_wa' => '6281234567890',
            'contact_email' => 'pengurus@rt09.id',
            'public_finance' => '1',
            'meta_keywords' => 'RT 09, Karang Taruna, Rukun Tetangga, Informasi Warga, Kas RT, Sukamaju',
            'site_logo' => '/logo.png',
            'site_favicon' => '/favicon.svg',
        ];
        $settingsService->setMany($settings);

        // 2. Organizations
        $rt = Organization::create([
            'name' => 'Rukun Tetangga 09 / RW 04',
            'slug' => 'rt-09',
            'type' => 'rt',
            'code' => 'RT-09-04',
            'description' => 'Rukun Tetangga 09 berkomitmen menciptakan lingkungan perumahan yang aman, asri, nyaman, guyub rukun, dan berkeadaban tinggi dengan gotong royong antar warga.',
            'address' => 'Jl. Melati Indah Blok B, RT 09 / RW 04, Sukamaju',
            'phone' => '0812-3456-7890',
            'email' => 'rt09@sukamaju.id',
            'social_links' => [
                'facebook' => 'https://facebook.com/rt09sukamaju',
                'instagram' => 'https://instagram.com/rt09sukamaju',
                'youtube' => 'https://youtube.com/@rt09sukamaju',
            ],
        ]);

        $kt = Organization::create([
            'name' => 'Karang Taruna Tunas Karya RT 09',
            'slug' => 'karang-taruna-tunas-karya',
            'type' => 'karang_taruna',
            'code' => 'KT-09',
            'description' => 'Wadah pemuda pemudi RT 09 untuk berkarya, berinovasi, mempererat solidaritas, mengabdi pada masyarakat lingkungan, serta mengembangkan bakat olahraga & wirausaha.',
            'address' => 'Balai Warga RT 09, Sukamaju',
            'phone' => '0857-1234-5678',
            'email' => 'karangtaruna@rt09.id',
            'social_links' => [
                'instagram' => 'https://instagram.com/tunaskarya_rt09',
                'tiktok' => 'https://tiktok.com/@tunaskarya_rt09',
            ],
        ]);

        // 3. Users
        $superAdmin = User::create([
            'name' => 'Administrator Sistem',
            'email' => 'superadmin@rt09.id',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '0811-0000-0001',
            'is_active' => true,
        ]);

        $adminRt = User::create([
            'name' => 'Admin Pengurus RT 09',
            'email' => 'admin.rt@rt09.id',
            'password' => Hash::make('password'),
            'role' => 'admin_rt',
            'organization_id' => $rt->id,
            'phone' => '0812-3456-7890',
            'is_active' => true,
        ]);

        $adminKt = User::create([
            'name' => 'Admin Karang Taruna',
            'email' => 'admin.karangtaruna@rt09.id',
            'password' => Hash::make('password'),
            'role' => 'admin_karang_taruna',
            'organization_id' => $kt->id,
            'phone' => '0857-1234-5678',
            'is_active' => true,
        ]);

        // 4. Periods
        $periodRtActive = OrganizationPeriod::create([
            'organization_id' => $rt->id,
            'name' => 'Periode 2024 - 2027',
            'start_year' => 2024,
            'end_year' => 2027,
            'is_active' => true,
            'notes' => 'Kepengurusan RT periode berjalan hasil musyawarah warga.',
        ]);

        $periodRtPast = OrganizationPeriod::create([
            'organization_id' => $rt->id,
            'name' => 'Periode 2021 - 2024',
            'start_year' => 2021,
            'end_year' => 2024,
            'is_active' => false,
            'notes' => 'Kepengurusan periode sebelumnya.',
        ]);

        $periodKtActive = OrganizationPeriod::create([
            'organization_id' => $kt->id,
            'name' => 'Periode 2025 - 2027',
            'start_year' => 2025,
            'end_year' => 2027,
            'is_active' => true,
            'notes' => 'Kepengurusan pemuda Tunas Karya periode aktif.',
        ]);

        // 5. Positions - RT
        $posRtKetua = OrganizationPosition::create([
            'organization_id' => $rt->id,
            'name' => 'Ketua RT',
            'slug' => 'ketua-rt',
            'max_members' => 1,
            'sort_order' => 1,
            'is_default' => true,
        ]);

        $posRtWakil = OrganizationPosition::create([
            'organization_id' => $rt->id,
            'name' => 'Wakil Ketua RT',
            'slug' => 'wakil-ketua-rt',
            'max_members' => 1,
            'sort_order' => 2,
            'is_default' => true,
        ]);

        $posRtSekretaris = OrganizationPosition::create([
            'organization_id' => $rt->id,
            'name' => 'Sekretaris RT',
            'slug' => 'sekretaris-rt',
            'max_members' => 1,
            'sort_order' => 3,
            'is_default' => true,
        ]);

        $posRtBendahara = OrganizationPosition::create([
            'organization_id' => $rt->id,
            'name' => 'Bendahara RT',
            'slug' => 'bendahara-rt',
            'max_members' => 1,
            'sort_order' => 4,
            'is_default' => true,
        ]);

        $posRtHumas = OrganizationPosition::create([
            'organization_id' => $rt->id,
            'name' => 'Seksi Humas & Warga',
            'slug' => 'seksi-humas',
            'max_members' => 2,
            'sort_order' => 5,
            'is_default' => true,
        ]);

        $posRtKeamanan = OrganizationPosition::create([
            'organization_id' => $rt->id,
            'name' => 'Seksi Keamanan & Ronda',
            'slug' => 'seksi-keamanan',
            'max_members' => 2,
            'sort_order' => 6,
            'is_default' => false,
        ]);

        // 5. Positions - Karang Taruna
        $posKtKetua = OrganizationPosition::create([
            'organization_id' => $kt->id,
            'name' => 'Ketua Karang Taruna',
            'slug' => 'ketua-karang-taruna',
            'max_members' => 1,
            'sort_order' => 1,
            'is_default' => true,
        ]);

        $posKtWakil = OrganizationPosition::create([
            'organization_id' => $kt->id,
            'name' => 'Wakil Ketua Karang Taruna',
            'slug' => 'wakil-ketua-kt',
            'max_members' => 1,
            'sort_order' => 2,
            'is_default' => true,
        ]);

        $posKtSekretaris = OrganizationPosition::create([
            'organization_id' => $kt->id,
            'name' => 'Sekretaris Karang Taruna',
            'slug' => 'sekretaris-kt',
            'max_members' => 2, // Sesuai requirement: maks 2 anggota
            'sort_order' => 3,
            'is_default' => true,
        ]);

        $posKtBendahara = OrganizationPosition::create([
            'organization_id' => $kt->id,
            'name' => 'Bendahara Karang Taruna',
            'slug' => 'bendahara-kt',
            'max_members' => 2, // Sesuai requirement: maks 2 anggota
            'sort_order' => 4,
            'is_default' => true,
        ]);

        $posKtHumas = OrganizationPosition::create([
            'organization_id' => $kt->id,
            'name' => 'Seksi Humas & Media',
            'slug' => 'seksi-humas-kt',
            'max_members' => 3,
            'sort_order' => 5,
            'is_default' => true,
        ]);

        // 6. Organization Members & Multi-Period Roles
        // Pak Bambang: Periode 2021-2024 sebagai Sekretaris RT, lalu Periode 2024-2027 sebagai Ketua RT!
        $memberBambang = OrganizationMember::create([
            'organization_id' => $rt->id,
            'name' => 'H. Bambang Sudarmono, S.E.',
            'gender' => 'male',
            'phone' => '0812-9988-7766',
            'email' => 'bambang@rt09.id',
            'bio' => 'Warga RT 09 sejak tahun 2012. Berpengalaman di bidang kepengurusan warga dan keorganisasian kemasyarakatan.',
            'join_year' => 2012,
            'status' => 'active',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberBambang->id,
            'organization_position_id' => $posRtSekretaris->id,
            'organization_period_id' => $periodRtPast->id,
            'notes' => 'Menjabat sekretaris pada kepengurusan sebelumnya.',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberBambang->id,
            'organization_position_id' => $posRtKetua->id,
            'organization_period_id' => $periodRtActive->id,
            'notes' => 'Terpilih sebagai Ketua RT 09.',
        ]);

        // Pak Hendra: Wakil Ketua RT
        $memberHendra = OrganizationMember::create([
            'organization_id' => $rt->id,
            'name' => 'Ir. Hendra Gunawan',
            'gender' => 'male',
            'phone' => '0813-1122-3344',
            'email' => 'hendra@rt09.id',
            'bio' => 'Koordinator ketertiban lingkungan dan infrastruktur.',
            'join_year' => 2015,
            'status' => 'active',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberHendra->id,
            'organization_position_id' => $posRtWakil->id,
            'organization_period_id' => $periodRtActive->id,
        ]);

        // Ibu Ratna: Sekretaris RT aktif
        $memberRatna = OrganizationMember::create([
            'organization_id' => $rt->id,
            'name' => 'Ratna Dewi Kusuma, S.Pd.',
            'gender' => 'female',
            'phone' => '0817-5544-3322',
            'email' => 'ratna@rt09.id',
            'bio' => 'Guru dan penggiat posyandu / administrasi warga.',
            'join_year' => 2018,
            'status' => 'active',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberRatna->id,
            'organization_position_id' => $posRtSekretaris->id,
            'organization_period_id' => $periodRtActive->id,
        ]);

        // Pak Joko: Bendahara RT aktif
        $memberJoko = OrganizationMember::create([
            'organization_id' => $rt->id,
            'name' => 'Joko Santoso, Ak.',
            'gender' => 'male',
            'phone' => '0818-8877-6655',
            'email' => 'joko@rt09.id',
            'bio' => 'Praktisi akuntansi dan pengelola kas warga RT 09.',
            'join_year' => 2016,
            'status' => 'active',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberJoko->id,
            'organization_position_id' => $posRtBendahara->id,
            'organization_period_id' => $periodRtActive->id,
        ]);

        // Pak Agus: Humas RT aktif
        $memberAgus = OrganizationMember::create([
            'organization_id' => $rt->id,
            'name' => 'Agus Priyanto',
            'gender' => 'male',
            'phone' => '0856-7788-9900',
            'email' => 'agus@rt09.id',
            'bio' => 'Humas dan penghubung komunikasi warga via WhatsApp & media.',
            'join_year' => 2020,
            'status' => 'active',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberAgus->id,
            'organization_position_id' => $posRtHumas->id,
            'organization_period_id' => $periodRtActive->id,
        ]);

        // Karang Taruna Members:
        $memberRizky = OrganizationMember::create([
            'organization_id' => $kt->id,
            'name' => 'Rizky Pratama, S.Kom.',
            'gender' => 'male',
            'phone' => '0899-1122-3344',
            'email' => 'rizky@rt09.id',
            'bio' => 'Ketua Karang Taruna Tunas Karya.',
            'join_year' => 2020,
            'status' => 'active',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberRizky->id,
            'organization_position_id' => $posKtKetua->id,
            'organization_period_id' => $periodKtActive->id,
        ]);

        $memberDinda = OrganizationMember::create([
            'organization_id' => $kt->id,
            'name' => 'Dinda Ayu Lestari',
            'gender' => 'female',
            'phone' => '0898-7766-5544',
            'email' => 'dinda@rt09.id',
            'bio' => 'Wakil Ketua Karang Taruna.',
            'join_year' => 2021,
            'status' => 'active',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberDinda->id,
            'organization_position_id' => $posKtWakil->id,
            'organization_period_id' => $periodKtActive->id,
        ]);

        // Sekretaris KT 1 & 2
        $memberSekreKt1 = OrganizationMember::create([
            'organization_id' => $kt->id,
            'name' => 'Faisal Akbar',
            'gender' => 'male',
            'phone' => '0895-4433-2211',
            'email' => 'faisal@rt09.id',
            'bio' => 'Sekretaris 1 Karang Taruna.',
            'join_year' => 2022,
            'status' => 'active',
        ]);

        $memberSekreKt2 = OrganizationMember::create([
            'organization_id' => $kt->id,
            'name' => 'Nadia Putri',
            'gender' => 'female',
            'phone' => '0896-1234-9876',
            'email' => 'nadia@rt09.id',
            'bio' => 'Sekretaris 2 Karang Taruna.',
            'join_year' => 2023,
            'status' => 'active',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberSekreKt1->id,
            'organization_position_id' => $posKtSekretaris->id,
            'organization_period_id' => $periodKtActive->id,
            'notes' => 'Sekretaris I',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberSekreKt2->id,
            'organization_position_id' => $posKtSekretaris->id,
            'organization_period_id' => $periodKtActive->id,
            'notes' => 'Sekretaris II',
        ]);

        // Bendahara KT 1 & 2
        $memberBendaKt1 = OrganizationMember::create([
            'organization_id' => $kt->id,
            'name' => 'Anisa Rahmawati',
            'gender' => 'female',
            'phone' => '0877-3322-1100',
            'email' => 'anisa@rt09.id',
            'bio' => 'Bendahara 1 Karang Taruna.',
            'join_year' => 2021,
            'status' => 'active',
        ]);

        $memberBendaKt2 = OrganizationMember::create([
            'organization_id' => $kt->id,
            'name' => 'Kevin Maulana',
            'gender' => 'male',
            'phone' => '0878-9988-7766',
            'email' => 'kevin@rt09.id',
            'bio' => 'Bendahara 2 Karang Taruna.',
            'join_year' => 2022,
            'status' => 'active',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberBendaKt1->id,
            'organization_position_id' => $posKtBendahara->id,
            'organization_period_id' => $periodKtActive->id,
            'notes' => 'Bendahara I',
        ]);

        PositionMemberPeriod::create([
            'organization_member_id' => $memberBendaKt2->id,
            'organization_position_id' => $posKtBendahara->id,
            'organization_period_id' => $periodKtActive->id,
            'notes' => 'Bendahara II',
        ]);

        // 7. Cash Accounts & Categories
        $accRt = CashAccount::create([
            'organization_id' => $rt->id,
            'name' => 'Kas Utama RT 09',
            'account_number' => 'BCA - 8730991823 a.n Kas RT 09',
            'current_balance' => 0,
            'description' => 'Rekening kas operasional warga RT 09.',
            'is_active' => true,
        ]);

        $accKt = CashAccount::create([
            'organization_id' => $kt->id,
            'name' => 'Kas Karang Taruna Tunas Karya',
            'account_number' => 'Mandiri - 15700889922 a.n KT Tunas Karya',
            'current_balance' => 0,
            'description' => 'Rekening kas kegiatan kepemudaan RT 09.',
            'is_active' => true,
        ]);

        // Categories RT
        $catIuran = CashCategory::create(['organization_id' => $rt->id, 'name' => 'Iuran Warga Bulanan', 'type' => 'income']);
        $catDonasi = CashCategory::create(['organization_id' => $rt->id, 'name' => 'Donasi & Sumbangan', 'type' => 'income']);
        $catRonda = CashCategory::create(['organization_id' => $rt->id, 'name' => 'Operasional Ronda & Keamanan', 'type' => 'expense']);
        $catSampah = CashCategory::create(['organization_id' => $rt->id, 'name' => 'Iuran & Angkut Sampah', 'type' => 'expense']);
        $catPerbaikan = CashCategory::create(['organization_id' => $rt->id, 'name' => 'Pemeliharaan Lampu Jalan & Sarana', 'type' => 'expense']);

        // Categories KT
        $catKtIuran = CashCategory::create(['organization_id' => $kt->id, 'name' => 'Iuran Kas Anggota Pemuda', 'type' => 'income']);
        $catKtEvent = CashCategory::create(['organization_id' => $kt->id, 'name' => 'Sponsorship & Donasi Kegiatan', 'type' => 'income']);
        $catKtOlahraga = CashCategory::create(['organization_id' => $kt->id, 'name' => 'Peralatan Olahraga & Seni', 'type' => 'expense']);
        $catKtKonsumsi = CashCategory::create(['organization_id' => $kt->id, 'name' => 'Konsumsi Rapat & Pelatihan', 'type' => 'expense']);

        // Record Initial Transactions via FinanceService
        $financeService = app(FinanceService::class);

        $financeService->recordTransaction([
            'cash_account_id' => $accRt->id,
            'cash_category_id' => $catIuran->id,
            'type' => 'income',
            'amount' => 4500000,
            'transaction_date' => now()->subDays(20)->toDateString(),
            'description' => 'Penerimaan Iuran Warga Bulan Berjalan (45 KK @ Rp 100.000)',
            'reference_number' => 'TRX-RT-001',
        ], $superAdmin->id);

        $financeService->recordTransaction([
            'cash_account_id' => $accRt->id,
            'cash_category_id' => $catDonasi->id,
            'type' => 'income',
            'amount' => 2000000,
            'transaction_date' => now()->subDays(15)->toDateString(),
            'description' => 'Sumbangan Donatur untuk Pengecatan Gapura RT',
            'reference_number' => 'TRX-RT-002',
        ], $superAdmin->id);

        $financeService->recordTransaction([
            'cash_account_id' => $accRt->id,
            'cash_category_id' => $catRonda->id,
            'type' => 'expense',
            'amount' => 850000,
            'transaction_date' => now()->subDays(10)->toDateString(),
            'description' => 'Pembelian Lampu Senter Patroli, HT, dan Kopi Pos Ronda',
            'reference_number' => 'TRX-RT-003',
        ], $superAdmin->id);

        $financeService->recordTransaction([
            'cash_account_id' => $accRt->id,
            'cash_category_id' => $catSampah->id,
            'type' => 'expense',
            'amount' => 1200000,
            'transaction_date' => now()->subDays(5)->toDateString(),
            'description' => 'Honor Petugas Angkut Sampah Lingkungan Bulan Berjalan',
            'reference_number' => 'TRX-RT-004',
        ], $superAdmin->id);

        // KT transactions
        $financeService->recordTransaction([
            'cash_account_id' => $accKt->id,
            'cash_category_id' => $catKtIuran->id,
            'type' => 'income',
            'amount' => 1200000,
            'transaction_date' => now()->subDays(18)->toDateString(),
            'description' => 'Iuran Wajib Anggota Pemuda Karang Taruna',
            'reference_number' => 'TRX-KT-001',
        ], $superAdmin->id);

        $financeService->recordTransaction([
            'cash_account_id' => $accKt->id,
            'cash_category_id' => $catKtEvent->id,
            'type' => 'income',
            'amount' => 3000000,
            'transaction_date' => now()->subDays(12)->toDateString(),
            'description' => 'Bantuan Dana Kegiatan Olahraga dari Donatur Lingkungan',
            'reference_number' => 'TRX-KT-002',
        ], $superAdmin->id);

        $financeService->recordTransaction([
            'cash_account_id' => $accKt->id,
            'cash_category_id' => $catKtOlahraga->id,
            'type' => 'expense',
            'amount' => 950000,
            'transaction_date' => now()->subDays(7)->toDateString(),
            'description' => 'Pembelian Raket, Shuttlecock, dan Bola Voli untuk Warga',
            'reference_number' => 'TRX-KT-003',
        ], $superAdmin->id);

        // 8. Sample Content (Announcements, Events, Activities, Articles, Custom Page)
        $tiptapService = app(TiptapContentService::class);

        // Announcement 1
        $ann1Doc = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Diberitahukan kepada seluruh warga RT 09 / RW 04 bahwa akan diadakan '],
                        ['type' => 'text', 'marks' => [['type' => 'bold']], 'text' => 'Kerja Bakti Bersih Lingkungan Serentak'],
                        ['type' => 'text', 'text' => ' guna mencegah genangan air dan perkembangbiakan jentik nyamuk demam berdarah menjelang musim hujan.'],
                    ],
                ],
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 3],
                    'content' => [['type' => 'text', 'text' => 'Jadwal & Rincian Kegiatan']],
                ],
                [
                    'type' => 'bulletList',
                    'content' => [
                        [
                            'type' => 'listItem',
                            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Hari/Tanggal: Minggu, 30 Agustus 2026']]]],
                        ],
                        [
                            'type' => 'listItem',
                            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Waktu: Pukul 07.00 WIB s/d selesai']]]],
                        ],
                        [
                            'type' => 'listItem',
                            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Titik Kumpul: Depan Balai Warga RT 09']]]],
                        ],
                        [
                            'type' => 'listItem',
                            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Perlengkapan: Cangkul, sapu lidi, karung sampah, dan sarung tangan']]]],
                        ],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Konsumsi makan siang dan minuman disediakan oleh panitia ibu-ibu PKK RT 09. Kehadiran dan partisipasi bapak/ibu/saudara sekalian sangat kami harapkan demi kebersihan dan kesehatan lingkungan bersama.'],
                    ],
                ],
            ],
        ];

        Content::create([
            'organization_id' => $rt->id,
            'type' => 'announcement',
            'title' => 'Kerja Bakti Bersih Lingkungan & Saluran Air Serentak RT 09',
            'slug' => 'kerja-bakti-bersih-lingkungan-rt09',
            'excerpt' => 'Pemberitahuan gotong royong kerja bakti membersihkan saluran air dan fasilitas umum warga RT 09.',
            'content' => $ann1Doc,
            'content_html' => $tiptapService->renderToHtml($ann1Doc),
            'status' => 'published',
            'published_at' => now()->subDays(3),
            'author_id' => $superAdmin->id,
            'is_featured' => true,
        ]);

        // Announcement 2
        $ann2Doc = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Menindaklanjuti program Puskesmas Kecamatan, tim fogging akan melakukan pengasapan di seluruh blok rumah warga RT 09 pada '],
                        ['type' => 'text', 'marks' => [['type' => 'bold']], 'text' => 'Sabtu, 5 September 2026 pukul 08.00 WIB.'],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Mohon warga menutup makanan, mengamankan hewan peliharaan, dan membuka ventilasi jendela rumah saat proses pengasapan berlangsung.'],
                    ],
                ],
            ],
        ];

        Content::create([
            'organization_id' => $rt->id,
            'type' => 'announcement',
            'title' => 'Jadwal Pengasapan (Fogging) Nyamuk Demam Berdarah',
            'slug' => 'jadwal-fogging-nyamuk-dbd',
            'excerpt' => 'Informasi pelaksanaan fogging nyamuk DBD di lingkungan RT 09 oleh tim kesehatan Puskesmas.',
            'content' => $ann2Doc,
            'content_html' => $tiptapService->renderToHtml($ann2Doc),
            'status' => 'published',
            'published_at' => now()->subDays(1),
            'author_id' => $superAdmin->id,
            'is_featured' => false,
        ]);

        // Event 1 (Upcoming)
        $event1Doc = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Karang Taruna Tunas Karya mengundang seluruh pemuda dan warga RT 09 untuk memeriahkan '],
                        ['type' => 'text', 'marks' => [['type' => 'bold']], 'text' => 'Turnamen Bulutangkis Ganda Putra & Putri Antar Gang.'],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Pendaftaran gratis dan memperebutkan Piala Bergilir Ketua RT serta hadiah menarik lainnya.'],
                    ],
                ],
            ],
        ];

        $eventContent = Content::create([
            'organization_id' => $kt->id,
            'type' => 'event',
            'title' => 'Turnamen Bulutangkis Pemuda & Warga RT 09',
            'slug' => 'turnamen-bulutangkis-warga-rt09',
            'excerpt' => 'Turnamen olahraga bulutangkis antar warga dalam rangka mempererat tali persaudaraan.',
            'content' => $event1Doc,
            'content_html' => $tiptapService->renderToHtml($event1Doc),
            'status' => 'published',
            'published_at' => now()->subDays(2),
            'author_id' => $adminKt->id,
            'is_featured' => true,
        ]);

        Event::create([
            'content_id' => $eventContent->id,
            'start_at' => now()->addDays(7)->setHour(19)->setMinute(30),
            'end_at' => now()->addDays(9)->setHour(23)->setMinute(0),
            'location' => 'Lapangan Olahraga Serbaguna RT 09',
            'maps_url' => 'https://maps.google.com/?q=Lapangan+RT09+Sukamaju',
            'is_online' => false,
        ]);

        // Activity 1 (Completed with gallery)
        $act1Doc = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Peringatan HUT Kemerdekaan RI di lingkungan RT 09 berlangsung meriah dengan berbagai perlombaan anak-anak, jalan sehat keluarga, dan panggung hiburan warga pada malam puncak syukuran.'],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Terima kasih sebesar-besarnya kepada seluruh panitia Karang Taruna dan warga yang telah berpartisipasi serta menyumbangkan tenaga dan dana.'],
                    ],
                ],
            ],
        ];

        $actContent = Content::create([
            'organization_id' => $rt->id,
            'type' => 'activity',
            'title' => 'Dokumentasi Semarak Pesta Rakyat & Lomba 17 Agustus RT 09',
            'slug' => 'dokumentasi-semarak-17-agustus-rt09',
            'excerpt' => 'Liputan kegiatan perlombaan dan malam puncak peringatan HUT Kemerdekaan RI di RT 09.',
            'content' => $act1Doc,
            'content_html' => $tiptapService->renderToHtml($act1Doc),
            'status' => 'published',
            'published_at' => now()->subDays(8),
            'author_id' => $adminRt->id,
            'is_featured' => true,
        ]);

        Activity::create([
            'content_id' => $actContent->id,
            'activity_date' => now()->subDays(8)->toDateString(),
            'location' => 'Balai Warga & Sepanjang Jalan RT 09',
        ]);

        // Article 1
        $art1Doc = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Pengolahan sampah rumah tangga dengan pemilahan sampah organik dan anorganik dari rumah terbukti mampu mengurangi beban tempat pembuangan akhir hingga 60%.'],
                    ],
                ],
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 3],
                    'content' => [['type' => 'text', 'text' => 'Langkah Praktis Pemilahan Sampah']],
                ],
                [
                    'type' => 'orderedList',
                    'content' => [
                        [
                            'type' => 'listItem',
                            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Sediakan 2 tempat sampah terpisah di dapur (Hijau untuk Organik, Kuning untuk Anorganik).']]]],
                        ],
                        [
                            'type' => 'listItem',
                            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Kumpulkan botol plastik, kardus, dan kaleng untuk disetor ke Bank Sampah Karang Taruna.']]]],
                        ],
                        [
                            'type' => 'listItem',
                            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Manfaatkan sisa sayuran untuk kompos tanaman pekarangan.']]]],
                        ],
                    ],
                ],
            ],
        ];

        Content::create([
            'organization_id' => $rt->id,
            'type' => 'article',
            'title' => 'Panduan Bijak Mengelola Sampah Rumah Tangga Mandiri',
            'slug' => 'panduan-mengelola-sampah-mandiri',
            'excerpt' => 'Tips sederhana memilah sampah dari dapur untuk mewujudkan lingkungan RT 09 yang asri dan bebas bau.',
            'content' => $art1Doc,
            'content_html' => $tiptapService->renderToHtml($art1Doc),
            'status' => 'published',
            'published_at' => now()->subDays(4),
            'author_id' => $superAdmin->id,
            'is_featured' => false,
        ]);
    }
}
