<main class="page">
  <section class="panel panel--md">
    <h1 class="title">Hızlı Kayıt</h1>

    <form method="post" action="/fields/store">
      <input type="hidden" name="tur" value="Hızlı Kayıt">

      <div class="form-group">
        <label for="tarla_adi">Tarla Adı</label>
        <input type="text" id="tarla_adi" name="tarla_adi" required>
      </div>

      <div class="form-group">
        <label for="lokasyon">Lokasyon</label>
        <input type="text" id="lokasyon" name="lokasyon">
      </div>

      <div class="form-group">
        <label for="alan">Alan (dönüm)</label>
        <input type="number" id="alan" name="alan" min="0" step="0.01">
      </div>

      <button class="button button--block" type="submit">Kaydet</button>
    </form>
    <button class="button button--soft button--block" onclick="go(routes.home)">Geri Dön</button>
  </section>
</main>
