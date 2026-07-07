<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Kullanıcılar ---
        $users = [
            ['name' => 'Ahmet Yılmaz',  'email' => 'ahmet@muhasebe.test',  'username' => 'ahmetyilmaz',  'password' => bcrypt('demo1234')],
            ['name' => 'Elif Kaya',     'email' => 'elif@muhasebe.test',   'username' => 'elifkaya',      'password' => bcrypt('demo1234')],
            ['name' => 'Murat Demir',   'email' => 'murat@muhasebe.test',  'username' => 'muratdemir',    'password' => bcrypt('demo1234')],
            ['name' => 'Selin Arslan',  'email' => 'selin@muhasebe.test',  'username' => 'selinarslan',   'password' => bcrypt('demo1234')],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }

        // --- İşlemler (transactions) ---
        $transactions = [
            // Gelirler
            ['type' => 'gelir', 'amount' => 45000.00, 'category' => 'Danışmanlık',       'description' => 'Aralık danışmanlık hizmeti - ABC Ltd.',      'date' => '2026-06-02'],
            ['type' => 'gelir', 'amount' => 28500.00, 'category' => 'Yazılım Geliştirme', 'description' => 'Mobil uygulama projesi - 2. milestone',       'date' => '2026-06-05'],
            ['type' => 'gelir', 'amount' => 12750.00, 'category' => 'Eğitim',             'description' => 'Kurumsal eğitim paketi - XYZ A.Ş.',           'date' => '2026-06-09'],
            ['type' => 'gelir', 'amount' => 67000.00, 'category' => 'Yazılım Geliştirme', 'description' => 'E-ticaret entegrasyon projesi tamamlandı',    'date' => '2026-06-12'],
            ['type' => 'gelir', 'amount' => 8900.00,  'category' => 'Destek',             'description' => 'Aylık teknik destek paketi - 3 müşteri',      'date' => '2026-06-15'],
            ['type' => 'gelir', 'amount' => 33400.00, 'category' => 'Danışmanlık',       'description' => 'Strateji danışmanlığı - Ocak faturası',        'date' => '2026-06-18'],
            ['type' => 'gelir', 'amount' => 19800.00, 'category' => 'Tasarım',            'description' => 'UI/UX tasarım paketi - Kurumsal kimlik',      'date' => '2026-06-22'],
            ['type' => 'gelir', 'amount' => 54200.00, 'category' => 'Yazılım Geliştirme', 'description' => 'CRM modülü geliştirme - son taksit',          'date' => '2026-06-26'],
            ['type' => 'gelir', 'amount' => 7500.00,  'category' => 'Destek',             'description' => 'Yıllık bakım sözleşmesi yenilemesi',           'date' => '2026-06-28'],
            ['type' => 'gelir', 'amount' => 41000.00, 'category' => 'Danışmanlık',       'description' => 'Dijital dönüşüm projesi - Faz 1',             'date' => '2026-07-01'],
            ['type' => 'gelir', 'amount' => 22000.00, 'category' => 'Eğitim',             'description' => 'Online bootcamp geliri - Haziran dönemi',      'date' => '2026-07-03'],

            // Giderler
            ['type' => 'gider', 'amount' => 18500.00, 'category' => 'Maaş',              'description' => 'Haziran maaş ödemeleri - 2 çalışan',          'date' => '2026-06-01'],
            ['type' => 'gider', 'amount' => 4200.00,  'category' => 'Kira',              'description' => 'Ofis kirası - Haziran',                        'date' => '2026-06-01'],
            ['type' => 'gider', 'amount' => 1850.00,  'category' => 'Fatura',            'description' => 'Elektrik, su, internet faturaları',            'date' => '2026-06-03'],
            ['type' => 'gider', 'amount' => 3600.00,  'category' => 'Yazılım & Lisans',  'description' => 'Adobe CC + JetBrains lisansları (yıllık)',     'date' => '2026-06-06'],
            ['type' => 'gider', 'amount' => 920.00,   'category' => 'Pazarlama',         'description' => 'Google Ads - Haziran kampanyası',              'date' => '2026-06-08'],
            ['type' => 'gider', 'amount' => 2400.00,  'category' => 'Muhasebe',          'description' => 'Mali müşavir aylık ücreti',                   'date' => '2026-06-10'],
            ['type' => 'gider', 'amount' => 750.00,   'category' => 'Ulaşım',            'description' => 'Müşteri ziyaretleri yakıt & otopark',         'date' => '2026-06-14'],
            ['type' => 'gider', 'amount' => 5800.00,  'category' => 'Ekipman',           'description' => 'Monitör + klavye seti - yeni geliştirici',    'date' => '2026-06-17'],
            ['type' => 'gider', 'amount' => 1100.00,  'category' => 'Pazarlama',         'description' => 'LinkedIn Premium - 3 hesap',                  'date' => '2026-06-20'],
            ['type' => 'gider', 'amount' => 18500.00, 'category' => 'Maaş',              'description' => 'Temmuz maaş ödemeleri - 2 çalışan',           'date' => '2026-07-01'],
            ['type' => 'gider', 'amount' => 4200.00,  'category' => 'Kira',              'description' => 'Ofis kirası - Temmuz',                        'date' => '2026-07-01'],
            ['type' => 'gider', 'amount' => 340.00,   'category' => 'Yazılım & Lisans',  'description' => 'AWS faturası - Haziran',                      'date' => '2026-07-04'],
            ['type' => 'gider', 'amount' => 480.00,   'category' => 'Ofis',              'description' => 'Kırtasiye & sarf malzeme',                    'date' => '2026-07-05'],
        ];

        DB::table('transactions')->insert($transactions);

        // --- Faturalar (invoices) ---
        $invoices = [
            ['invoice_no' => 'FAT-2026-001', 'client_name' => 'ABC Teknoloji Ltd. Şti.',    'amount' => 45000.00, 'tax_rate' => 20, 'status' => 'odendi',    'issue_date' => '2026-06-01', 'due_date' => '2026-06-15', 'description' => 'Aralık ayı danışmanlık hizmeti'],
            ['invoice_no' => 'FAT-2026-002', 'client_name' => 'XYZ Mühendislik A.Ş.',      'amount' => 28500.00, 'tax_rate' => 20, 'status' => 'odendi',    'issue_date' => '2026-06-04', 'due_date' => '2026-06-18', 'description' => 'Mobil uygulama geliştirme - milestone 2'],
            ['invoice_no' => 'FAT-2026-003', 'client_name' => 'Yıldız Holding',             'amount' => 67000.00, 'tax_rate' => 20, 'status' => 'odendi',    'issue_date' => '2026-06-10', 'due_date' => '2026-06-25', 'description' => 'E-ticaret platform entegrasyonu'],
            ['invoice_no' => 'FAT-2026-004', 'client_name' => 'Kartal İnşaat San. Tic.',    'amount' => 12750.00, 'tax_rate' => 20, 'status' => 'odendi',    'issue_date' => '2026-06-08', 'due_date' => '2026-06-22', 'description' => 'Kurumsal eğitim - Excel & veri analizi'],
            ['invoice_no' => 'FAT-2026-005', 'client_name' => 'Deniz Lojistik A.Ş.',       'amount' => 33400.00, 'tax_rate' => 20, 'status' => 'beklemede', 'issue_date' => '2026-06-17', 'due_date' => '2026-07-01', 'description' => 'Strateji danışmanlığı - Ocak dönemi'],
            ['invoice_no' => 'FAT-2026-006', 'client_name' => 'Pınar Gıda San. A.Ş.',      'amount' => 19800.00, 'tax_rate' => 10, 'status' => 'beklemede', 'issue_date' => '2026-06-20', 'due_date' => '2026-07-04', 'description' => 'Kurumsal kimlik & UI/UX tasarım paketi'],
            ['invoice_no' => 'FAT-2026-007', 'client_name' => 'MetaSoft Yazılım Ltd.',      'amount' => 54200.00, 'tax_rate' => 20, 'status' => 'beklemede', 'issue_date' => '2026-06-25', 'due_date' => '2026-07-09', 'description' => 'CRM modülü son teslim & lisans'],
            ['invoice_no' => 'FAT-2026-008', 'client_name' => 'Güneş Enerji Yatırım A.Ş.', 'amount' => 41000.00, 'tax_rate' => 20, 'status' => 'beklemede', 'issue_date' => '2026-06-30', 'due_date' => '2026-07-14', 'description' => 'Dijital dönüşüm - Faz 1 teslim'],
            ['invoice_no' => 'FAT-2026-009', 'client_name' => 'Anadolu Sigorta',            'amount' => 8900.00,  'tax_rate' => 18, 'status' => 'beklemede', 'issue_date' => '2026-07-01', 'due_date' => '2026-07-15', 'description' => 'Aylık teknik destek paketi'],
            ['invoice_no' => 'FAT-2026-010', 'client_name' => 'Global Trade İthalat A.Ş.', 'amount' => 15000.00, 'tax_rate' => 20, 'status' => 'iptal',     'issue_date' => '2026-05-20', 'due_date' => '2026-06-03', 'description' => 'Proje iptal edildi - yazılım lisans'],
        ];

        DB::table('invoices')->insert($invoices);
    }
}
