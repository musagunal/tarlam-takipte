<main class="page">
  <section class="panel panel--md">
    <h1 class="title">Hesap Ayarları</h1>

    <div class="detail detail--open">
      <p><strong>Kullanıcı:</strong> <?= htmlspecialchars($user['username'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
      <p><strong>E-posta:</strong> <?= htmlspecialchars($user['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
      <p><strong>Durum:</strong> Aktif</p>
    </div>

    <div class="detail detail--open">
      <h2>Şifre Değiştir</h2>

      <form method="post" action="/account/password">
        <div class="form-group">
          <label for="current_password">Mevcut Şifre</label>
          <input type="password" id="current_password" name="current_password" required>
        </div>

        <div class="form-group">
          <label for="new_password">Yeni Şifre</label>
          <input type="password" id="new_password" name="new_password" required minlength="6">
        </div>

        <div class="form-group">
          <label for="new_password_confirm">Yeni Şifre Tekrar</label>
          <input type="password" id="new_password_confirm" name="new_password_confirm" required minlength="6">
        </div>

        <button class="button button--block" type="submit">Şifreyi Güncelle</button>
      </form>
    </div>

    <button class="button button--soft button--block" onclick="go(routes.home)">Ana Sayfaya Dön</button>
  </section>
</main>
