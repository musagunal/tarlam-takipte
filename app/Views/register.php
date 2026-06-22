<main class="page page--center">
  <section class="panel panel--sm">
    <h1 class="title">Kayıt Ol</h1>
    <p class="subtitle">Yeni bir kullanıcı hesabı oluştur.</p>

    <form method="post" action="/register">
      <div class="form-group">
        <label for="username">Kullanıcı Adı</label>
        <input type="text" id="username" name="username" required placeholder="Kullanıcı adınızı girin" value="<?= htmlspecialchars($old['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="email">E-posta</label>
        <input type="email" id="email" name="email" required placeholder="ornek@mail.com" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="password">Şifre</label>
        <input type="password" id="password" name="password" required placeholder="Şifre girin">
      </div>

      <div class="form-group">
        <label for="password2">Şifre Tekrar</label>
        <input type="password" id="password2" name="password2" required placeholder="Şifreyi tekrar girin">
      </div>

      <button class="button button--block" type="submit">Kayıt Ol</button>
    </form>

    <div class="link-row">Zaten hesabın var mı? <a href="/login">Giriş Yap</a></div>
  </section>
</main>
