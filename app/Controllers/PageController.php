<?php

class PageController extends Controller
{
    public function login(): void
    {
        if ($this->currentUser()) {
            $this->redirect('/anasayfa');
        }

        $this->view('login', ['title' => 'Giriş Yap | Tarlam Takipte']);
    }

    public function loginPost(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->flash('error', 'E-posta veya şifre yanlış!');
            $this->old(['email' => $email]);
            $this->redirect('/login');
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
        ];

        $this->redirect('/anasayfa');
    }

    public function forgotPassword(): void
    {
        if ($this->currentUser()) {
            $this->redirect('/anasayfa');
        }

        $this->view('forgot-password', ['title' => 'Şifremi Unuttum | Tarlam Takipte']);
    }

    public function forgotPasswordPost(): void
    {
        $email = trim($_POST['email'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        $newPasswordConfirm = $_POST['new_password_confirm'] ?? '';
        $user = User::findByEmail($email);

        if (!$user) {
            $this->flash('error', 'Bu e-posta ile kayıtlı hesap bulunamadı.');
            $this->old(['email' => $email]);
            $this->redirect('/sifremi-unuttum');
        }

        if (strlen($newPassword) < 6) {
            $this->flash('error', 'Yeni şifre en az 6 karakter olmalı.');
            $this->old(['email' => $email]);
            $this->redirect('/sifremi-unuttum');
        }

        if ($newPassword !== $newPasswordConfirm) {
            $this->flash('error', 'Yeni şifreler uyuşmuyor.');
            $this->old(['email' => $email]);
            $this->redirect('/sifremi-unuttum');
        }

        User::updatePassword((int) $user['id'], $newPassword);
        $this->flash('success', 'Şifren yenilendi. Yeni şifrenle giriş yapabilirsin.');
        $this->redirect('/login');
    }

    public function register(): void
    {
        if ($this->currentUser()) {
            $this->redirect('/anasayfa');
        }

        $this->view('register', ['title' => 'Kayıt Ol | Tarlam Takipte']);
    }

    public function registerPost(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $this->flash('error', 'Tüm alanları doldurmalısın.');
            $this->old($_POST);
            $this->redirect('/register');
        }

        if ($password !== $password2) {
            $this->flash('error', 'Şifreler uyuşmuyor!');
            $this->old($_POST);
            $this->redirect('/register');
        }

        if (User::findByEmail($email)) {
            $this->flash('error', 'Bu e-posta zaten kayıtlı!');
            $this->old($_POST);
            $this->redirect('/register');
        }

        User::create($username, $email, $password);
        $this->flash('success', 'Kayıt başarılı. Şimdi giriş yapabilirsin.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }

    public function home(): void
    {
        $this->requireAuth();
        $this->view('home', ['title' => 'Ana Sayfa | Tarlam Takipte']);
    }

    public function quickCreate(): void
    {
        $this->requireAuth();
        $this->view('quick-create', [
            'title' => 'Hızlı Kayıt | Tarlam Takipte',
            'mode' => 'quick',
        ]);
    }

    public function normalCreate(): void
    {
        $this->requireAuth();
        $this->view('normal-create', [
            'title' => 'Normal Kayıt | Tarlam Takipte',
            'mode' => 'normal',
        ]);
    }

    public function detailedCreate(): void
    {
        $this->requireAuth();
        $this->view('detailed-create', [
            'title' => 'Detaylı Kayıt | Tarlam Takipte',
            'mode' => 'detailed',
        ]);
    }

    public function fields(): void
    {
        $user = $this->requireAuth();
        $search = trim($_GET['q'] ?? '');

        $this->view('fields', [
            'title' => 'Kayıtlı Tarlalar | Tarlam Takipte',
            'fields' => Field::allForUser($user['id'], $search),
            'stats' => Field::statsForUser($user['id']),
            'search' => $search,
        ]);
    }

    public function fieldsPdf(): void
    {
        $user = $this->requireAuth();
        $fieldId = (int) ($_GET['id'] ?? 0);
        $field = Field::findForUser($fieldId, $user['id']);

        if (!$field) {
            $this->flash('error', 'PDF için tarla kaydı bulunamadı.');
            $this->redirect('/tarlalar');
        }

        $fields = [$field];
        $pdf = new SimplePdf();

        $pdf->text(40, 42, 'Tarlam Takipte - Tarla Raporu', 18, [0.08, 0.42, 0.20]);
        $pdf->text(40, 66, 'Kullanici: ' . ($user['username'] ?? '-'), 10, [0.20, 0.26, 0.22]);
        $pdf->text(40, 82, 'Tarih: ' . date('d.m.Y H:i'), 10, [0.20, 0.26, 0.22]);

        $totals = $this->fieldTotals($fields);
        $pdf->rect(40, 106, 515, 86, [0.93, 0.98, 0.94]);
        $pdf->text(56, 128, 'Tarla: ' . ($field['tarla_adi'] ?? '-'), 12, [0.05, 0.30, 0.15]);
        $pdf->text(56, 150, 'Lokasyon: ' . ($field['lokasyon'] ?: '-'), 10, [0.20, 0.26, 0.22]);
        $pdf->text(250, 150, 'Urun: ' . ($field['urun'] ?: '-'), 10, [0.20, 0.26, 0.22]);
        $pdf->text(410, 150, 'Alan: ' . $this->money((float) $field['alan']) . ' donum', 10, [0.20, 0.26, 0.22]);
        $pdf->text(56, 174, 'Gelir: ' . $this->money($totals['gelir']) . ' TL', 10, [0.10, 0.45, 0.20]);
        $pdf->text(210, 174, 'Gider: ' . $this->money($totals['gider']) . ' TL', 10, [0.55, 0.16, 0.16]);
        $pdf->text(390, 174, 'Kar/Zarar: ' . $this->money($totals['kar']) . ' TL', 10, $totals['kar'] >= 0 ? [0.10, 0.45, 0.20] : [0.60, 0.12, 0.12]);

        $this->drawSingleFieldChart($pdf, $field, 40, 230, 515, 190);
        $this->drawFieldTable($pdf, $fields, 470);

        $slug = preg_replace('/[^a-z0-9]+/i', '-', $field['tarla_adi'] ?? 'tarla');
        $fileName = 'tarla-raporu-' . trim($slug, '-') . '-' . date('Y-m-d') . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        echo $pdf->output();
        exit;
    }

    public function account(): void
    {
        $user = $this->requireAuth();
        $this->view('account', [
            'title' => 'Hesap Ayarları | Tarlam Takipte',
            'user' => $user,
        ]);
    }

    private function drawProfitChart(SimplePdf $pdf, array $fields, float $x, float $y, float $w, float $h): void
    {
        $pdf->text($x, $y - 18, 'Kar/Zarar Grafigi', 13, [0.08, 0.42, 0.20]);
        $pdf->line($x, $y + ($h / 2), $x + $w, $y + ($h / 2), [0.60, 0.70, 0.62], 0.6);
        $pdf->line($x, $y, $x, $y + $h, [0.60, 0.70, 0.62], 0.6);

        if (empty($fields)) {
            $pdf->text($x + 160, $y + 90, 'Grafik icin kayit bulunamadi.', 11, [0.35, 0.40, 0.36]);
            return;
        }

        $items = array_slice($fields, 0, 10);
        $profits = array_map(fn($field) => $this->fieldProfit($field), $items);
        $maxAbs = max(1, max(array_map('abs', $profits)));
        $barGap = 8;
        $barWidth = max(12, (($w - 18) / count($items)) - $barGap);
        $baseline = $y + ($h / 2);

        foreach ($items as $index => $field) {
            $profit = $profits[$index];
            $barHeight = (abs($profit) / $maxAbs) * (($h / 2) - 24);
            $barX = $x + 12 + ($index * ($barWidth + $barGap));
            $barY = $profit >= 0 ? $baseline - $barHeight : $baseline;
            $color = $profit >= 0 ? [0.22, 0.75, 0.40] : [0.88, 0.25, 0.25];
            $pdf->rect($barX, $barY, $barWidth, max(2, $barHeight), $color);
            $pdf->text($barX, $y + $h + 15, substr($field['tarla_adi'] ?? '-', 0, 8), 7, [0.20, 0.26, 0.22]);
        }
    }

    private function drawSingleFieldChart(SimplePdf $pdf, array $field, float $x, float $y, float $w, float $h): void
    {
        $pdf->text($x, $y - 18, 'Gelir / Gider / Kar Grafigi', 13, [0.08, 0.42, 0.20]);

        $gelir = (float) $field['verim'] * (float) $field['satis'];
        $gider = ((float) $field['gubre'] * (float) ($field['gubre_fiyat'] ?? 0)) + ((float) $field['mazot'] * (float) ($field['mazot_fiyat'] ?? 0));
        $kar = $gelir - $gider;
        $items = [
            ['label' => 'Gelir', 'value' => $gelir, 'color' => [0.22, 0.75, 0.40]],
            ['label' => 'Gider', 'value' => $gider, 'color' => [0.88, 0.34, 0.25]],
            ['label' => 'Kar/Zarar', 'value' => $kar, 'color' => $kar >= 0 ? [0.22, 0.75, 0.40] : [0.88, 0.25, 0.25]],
        ];
        $maxAbs = max(1, max(array_map(fn($item) => abs($item['value']), $items)));
        $baseline = $y + ($h / 2);

        $pdf->line($x, $baseline, $x + $w, $baseline, [0.60, 0.70, 0.62], 0.6);
        $pdf->line($x, $y, $x, $y + $h, [0.60, 0.70, 0.62], 0.6);

        foreach ($items as $index => $item) {
            $barWidth = 72;
            $barX = $x + 70 + ($index * 140);
            $barHeight = (abs($item['value']) / $maxAbs) * (($h / 2) - 24);
            $barY = $item['value'] >= 0 ? $baseline - $barHeight : $baseline;
            $pdf->rect($barX, $barY, $barWidth, max(2, $barHeight), $item['color']);
            $pdf->text($barX, $y + $h + 16, $item['label'], 9, [0.20, 0.26, 0.22]);
            $pdf->text($barX, $item['value'] >= 0 ? $barY - 8 : $barY + $barHeight + 14, $this->money($item['value']) . ' TL', 8, [0.20, 0.26, 0.22]);
        }
    }

    private function drawFieldTable(SimplePdf $pdf, array $fields, float $startY): void
    {
        $y = $startY;
        $pdf->text(40, $y, 'Kayit Tablosu', 13, [0.08, 0.42, 0.20]);
        $y += 22;
        $this->drawTableHeader($pdf, $y);
        $y += 20;

        foreach ($fields as $field) {
            if ($y > 790) {
                $pdf->addPage();
                $y = 48;
                $this->drawTableHeader($pdf, $y);
                $y += 20;
            }

            $gelir = (float) $field['verim'] * (float) $field['satis'];
            $gider = ((float) $field['gubre'] * (float) ($field['gubre_fiyat'] ?? 0)) + ((float) $field['mazot'] * (float) ($field['mazot_fiyat'] ?? 0));
            $kar = $gelir - $gider;

            $pdf->text(42, $y, substr($field['tarla_adi'] ?? '-', 0, 18), 8);
            $pdf->text(150, $y, substr($field['urun'] ?: '-', 0, 14), 8);
            $pdf->text(235, $y, $this->money((float) $field['alan']), 8);
            $pdf->text(305, $y, $this->money($gelir), 8);
            $pdf->text(385, $y, $this->money($gider), 8);
            $pdf->text(465, $y, $this->money($kar), 8, $kar >= 0 ? [0.10, 0.45, 0.20] : [0.60, 0.12, 0.12]);
            $pdf->line(40, $y + 8, 555, $y + 8, [0.86, 0.91, 0.87], 0.4);
            $y += 18;
        }
    }

    private function drawTableHeader(SimplePdf $pdf, float $y): void
    {
        $pdf->rect(40, $y - 13, 515, 18, [0.90, 0.97, 0.91]);
        $pdf->text(42, $y, 'Tarla', 8, [0.05, 0.30, 0.15]);
        $pdf->text(150, $y, 'Urun', 8, [0.05, 0.30, 0.15]);
        $pdf->text(235, $y, 'Alan', 8, [0.05, 0.30, 0.15]);
        $pdf->text(305, $y, 'Gelir', 8, [0.05, 0.30, 0.15]);
        $pdf->text(385, $y, 'Gider', 8, [0.05, 0.30, 0.15]);
        $pdf->text(465, $y, 'Kar/Zarar', 8, [0.05, 0.30, 0.15]);
    }

    private function fieldTotals(array $fields): array
    {
        $totals = ['gelir' => 0.0, 'gider' => 0.0, 'kar' => 0.0];

        foreach ($fields as $field) {
            $gelir = (float) $field['verim'] * (float) $field['satis'];
            $gider = ((float) $field['gubre'] * (float) ($field['gubre_fiyat'] ?? 0)) + ((float) $field['mazot'] * (float) ($field['mazot_fiyat'] ?? 0));
            $totals['gelir'] += $gelir;
            $totals['gider'] += $gider;
            $totals['kar'] += $gelir - $gider;
        }

        return $totals;
    }

    private function fieldProfit(array $field): float
    {
        $gelir = (float) $field['verim'] * (float) $field['satis'];
        $gider = ((float) $field['gubre'] * (float) ($field['gubre_fiyat'] ?? 0)) + ((float) $field['mazot'] * (float) ($field['mazot_fiyat'] ?? 0));

        return $gelir - $gider;
    }

    private function money(float $value): string
    {
        return number_format($value, 2, ',', '.');
    }

    public function changePassword(): void
    {
        $sessionUser = $this->requireAuth();
        $user = User::findById($sessionUser['id']);
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $newPasswordConfirm = $_POST['new_password_confirm'] ?? '';

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $this->flash('error', 'Mevcut şifre yanlış.');
            $this->redirect('/hesap');
        }

        if (strlen($newPassword) < 6) {
            $this->flash('error', 'Yeni şifre en az 6 karakter olmalı.');
            $this->redirect('/hesap');
        }

        if ($newPassword !== $newPasswordConfirm) {
            $this->flash('error', 'Yeni şifreler uyuşmuyor.');
            $this->redirect('/hesap');
        }

        User::updatePassword($sessionUser['id'], $newPassword);
        $this->flash('success', 'Şifre başarıyla değiştirildi.');
        $this->redirect('/hesap');
    }

    public function storeField(): void
    {
        $user = $this->requireAuth();
        Field::create($user['id'], $_POST);
        $this->flash('success', 'Tarla kaydı eklendi.');
        $this->redirect('/tarlalar');
    }

    public function updateField(): void
    {
        $user = $this->requireAuth();
        $id = (int) ($_POST['id'] ?? 0);
        Field::update($id, $user['id'], $_POST);
        $this->flash('success', 'Tarla kaydı güncellendi.');
        $this->redirect('/tarlalar');
    }

    public function deleteField(): void
    {
        $user = $this->requireAuth();
        $id = (int) ($_POST['id'] ?? 0);
        Field::delete($id, $user['id']);
        $this->flash('success', 'Tarla kaydı silindi.');
        $this->redirect('/tarlalar');
    }
}
