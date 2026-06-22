<main class="page page--center">
  <section class="panel panel--sm">
    <h1 class="title">Giriş Yap</h1>
    <p class="subtitle">Tarlam Takipte hesabınla devam et.</p>

    <form method="post" action="/login">
      <div class="form-group">
        <label for="email">E-posta</label>
        <input type="email" id="email" name="email" required placeholder="ornek@mail.com" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="password">Şifre</label>
        <input type="password" id="password" name="password" required placeholder="Şifrenizi girin">
      </div>

      <button class="button button--block" type="submit">Giriş Yap</button>
    </form>

    <div class="link-row">Hesabın yok mu? <a href="/register">Kayıt Ol</a></div>
    <div class="link-row"><a href="/sifremi-unuttum">Şifremi Unuttum</a></div>
  </section>
</main>
