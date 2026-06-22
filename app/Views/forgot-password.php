<main class="page page--center">
  <section class="panel panel--sm">
    <h1 class="title">Şifremi Unuttum</h1>
    <p class="subtitle">Kayıtlı e-postanı girip yeni şifreni belirle.</p>

    <form method="post" action="/sifremi-unuttum">
      <div class="form-group">
        <label for="email">E-posta</label>
        <input type="email" id="email" name="email" required placeholder="ornek@mail.com" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="new_password">Yeni Şifre</label>
        <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="Yeni şifrenizi girin">
      </div>

      <div class="form-group">
        <label for="new_password_confirm">Yeni Şifre Tekrar</label>
        <input type="password" id="new_password_confirm" name="new_password_confirm" required minlength="6" placeholder="Yeni şifreyi tekrar girin">
      </div>

      <button class="button button--block" type="submit">Şifreyi Yenile</button>
    </form>

    <div class="link-row"><a href="/login">Giriş sayfasına dön</a></div>
  </section>
</main>
