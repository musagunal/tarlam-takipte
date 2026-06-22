<main class="page">
  <section class="panel">
    <div class="topbar">
      <form method="post" action="/logout" class="inline-form">
        <button class="button button--danger" type="submit">Oturumdan Çık</button>
      </form>
      <button class="button button--soft" onclick="go(routes.fields)">Kayıtlı Tarlalar</button>
      <button class="button button--soft" onclick="go(routes.account)">Hesap Ayarları</button>
    </div>

    <h1 class="title">Tarlam Takipte</h1>
    <p class="subtitle">Tarla kayıtlarını, üretim bilgilerini ve maliyetleri tek yerden takip et.</p>

    <div class="grid">
      <article class="action-card" onclick="go(routes.quick)">
        <h2>Hızlı Kayıt</h2>
        <p>Tarla adı, lokasyon ve alan bilgisiyle hızlı kayıt oluştur.</p>
      </article>

      <article class="action-card" onclick="go(routes.normal)">
        <h2>Normal Kayıt</h2>
        <p>Standart tarla bilgilerine ürün bilgisini de ekle.</p>
      </article>

      <article class="action-card" onclick="go(routes.detailed)">
        <h2>Detaylı Kayıt</h2>
        <p>Gübre, mazot, verim ve satış fiyatı ile kar/zarar takibi yap.</p>
      </article>
    </div>

    <footer class="footer">2026 Tarlam Takipte</footer>
  </section>
</main>
