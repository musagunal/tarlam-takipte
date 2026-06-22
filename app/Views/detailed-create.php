<main class="page">
  <section class="panel panel--md">
    <h1 class="title">Detaylı Kayıt</h1>

    <form method="post" action="/fields/store">
      <input type="hidden" name="tur" value="Detaylı Kayıt">

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

      <div class="form-group">
        <label for="urun">Ürün</label>
        <input type="text" id="urun" name="urun">
      </div>

      <div class="form-group">
        <label for="gubre">Gübre (kg)</label>
        <input type="number" id="gubre" name="gubre" min="0" step="0.01">
      </div>

      <div class="form-group">
        <label for="gubre_fiyat">Gübre Alış Fiyatı (TL/kg)</label>
        <input type="number" id="gubre_fiyat" name="gubre_fiyat" min="0" step="0.01">
      </div>

      <div class="form-group">
        <label for="mazot">Mazot (lt)</label>
        <input type="number" id="mazot" name="mazot" min="0" step="0.01">
      </div>

      <div class="form-group">
        <label for="mazot_fiyat">Mazot Alış Fiyatı (TL/lt)</label>
        <input type="number" id="mazot_fiyat" name="mazot_fiyat" min="0" step="0.01">
      </div>

      <div class="form-group">
        <label for="verim">Verim (ton)</label>
        <input type="number" id="verim" name="verim" min="0" step="0.01">
      </div>

      <div class="form-group">
        <label for="satis">Satış Fiyatı (TL/ton)</label>
        <input type="number" id="satis" name="satis" min="0" step="0.01">
      </div>

      <button class="button button--block" type="submit">Kaydet</button>
    </form>
    <button class="button button--soft button--block" onclick="go(routes.home)">Geri Dön</button>
  </section>
</main>
