<main class="page">
  <section class="panel">
    <h1 class="title">Kayıtlı Tarlalar</h1>

    <div class="stats">
      <div class="stat">Toplam Tarla <span><?= (int) ($stats['total_tarla'] ?? 0) ?></span></div>
      <div class="stat">Toplam Alan <span><?= number_format((float) ($stats['total_alan'] ?? 0), 2, ',', '.') ?> dönüm</span></div>
      <div class="stat">Detaylı Kayıt <span><?= (int) ($stats['detayli_count'] ?? 0) ?></span></div>
    </div>

    <form method="get" action="/tarlalar" class="search-form">
      <input type="text" name="q" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Tarla adı, lokasyon, ürün veya tür ara...">
      <button class="button" type="submit">Ara</button>
    </form>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Tarla Adı</th>
            <th>Lokasyon</th>
            <th>Alan</th>
            <th>Ürün</th>
            <th>Tür</th>
            <th>Gelir</th>
            <th>Gider</th>
            <th>Kar/Zarar</th>
            <th>İşlem</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($fields)): ?>
            <tr>
              <td colspan="9">Kayıt bulunamadı.</td>
            </tr>
          <?php endif; ?>

          <?php foreach ($fields as $field): ?>
            <?php
              $gelir = (float) $field['verim'] * (float) $field['satis'];
              $gubreFiyat = (float) ($field['gubre_fiyat'] ?? 0);
              $mazotFiyat = (float) ($field['mazot_fiyat'] ?? 0);
              $gider = ((float) $field['gubre'] * $gubreFiyat) + ((float) $field['mazot'] * $mazotFiyat);
              $kar = $gelir - $gider;
            ?>
            <tr>
              <td><?= htmlspecialchars($field['tarla_adi'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($field['lokasyon'] ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= number_format((float) $field['alan'], 2, ',', '.') ?></td>
              <td><?= htmlspecialchars($field['urun'] ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($field['tur'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= number_format($gelir, 2, ',', '.') ?> TL</td>
              <td><?= number_format($gider, 2, ',', '.') ?> TL</td>
              <td class="<?= $kar >= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format($kar, 2, ',', '.') ?> TL</td>
              <td>
                <div class="row-actions">
                  <button
                    class="button button--soft button--sm"
                    type="button"
                    data-field='<?= htmlspecialchars(json_encode($field, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'
                    onclick="openEdit(this)"
                  >Düzenle</button>

                  <a class="button button--soft button--sm" href="/tarlalar/pdf?id=<?= (int) $field['id'] ?>">PDF</a>

                  <form method="post" action="/fields/delete" class="inline-form" onsubmit="return confirm('Bu kaydı silmek istiyor musun?')">
                    <input type="hidden" name="id" value="<?= (int) $field['id'] ?>">
                    <button class="button button--danger button--sm" type="submit">Sil</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="detail" id="editPanel">
      <h2>Tarla Düzenle</h2>
      <form method="post" action="/fields/update">
        <input type="hidden" name="id" id="edit_id">
        <input type="hidden" name="tur" id="edit_tur">

        <div class="form-grid">
          <div class="form-group">
            <label for="edit_tarla_adi">Tarla Adı</label>
            <input type="text" id="edit_tarla_adi" name="tarla_adi" required>
          </div>

          <div class="form-group">
            <label for="edit_lokasyon">Lokasyon</label>
            <input type="text" id="edit_lokasyon" name="lokasyon">
          </div>

          <div class="form-group">
            <label for="edit_alan">Alan (dönüm)</label>
            <input type="number" id="edit_alan" name="alan" min="0" step="0.01">
          </div>

          <div class="form-group">
            <label for="edit_urun">Ürün</label>
            <input type="text" id="edit_urun" name="urun">
          </div>

          <div class="form-group">
            <label for="edit_gubre">Gübre (kg)</label>
            <input type="number" id="edit_gubre" name="gubre" min="0" step="0.01">
          </div>

          <div class="form-group">
            <label for="edit_gubre_fiyat">Gübre Alış Fiyatı (TL/kg)</label>
            <input type="number" id="edit_gubre_fiyat" name="gubre_fiyat" min="0" step="0.01">
          </div>

          <div class="form-group">
            <label for="edit_mazot">Mazot (lt)</label>
            <input type="number" id="edit_mazot" name="mazot" min="0" step="0.01">
          </div>

          <div class="form-group">
            <label for="edit_mazot_fiyat">Mazot Alış Fiyatı (TL/lt)</label>
            <input type="number" id="edit_mazot_fiyat" name="mazot_fiyat" min="0" step="0.01">
          </div>

          <div class="form-group">
            <label for="edit_verim">Verim (ton)</label>
            <input type="number" id="edit_verim" name="verim" min="0" step="0.01">
          </div>

          <div class="form-group">
            <label for="edit_satis">Satış Fiyatı (TL/ton)</label>
            <input type="number" id="edit_satis" name="satis" min="0" step="0.01">
          </div>
        </div>

        <button class="button button--block" type="submit">Güncelle</button>
      </form>
    </div>

    <button class="button button--soft button--block" onclick="go(routes.home)">Ana Sayfaya Dön</button>
  </section>
</main>

<script>
  function openEdit(button) {
    const field = JSON.parse(button.dataset.field);
    const panel = document.getElementById("editPanel");

    document.getElementById("edit_id").value = field.id || "";
    document.getElementById("edit_tur").value = field.tur || "Hızlı Kayıt";
    document.getElementById("edit_tarla_adi").value = field.tarla_adi || "";
    document.getElementById("edit_lokasyon").value = field.lokasyon || "";
    document.getElementById("edit_alan").value = field.alan || 0;
    document.getElementById("edit_urun").value = field.urun || "";
    document.getElementById("edit_gubre").value = field.gubre || 0;
    document.getElementById("edit_gubre_fiyat").value = field.gubre_fiyat || 0;
    document.getElementById("edit_mazot").value = field.mazot || 0;
    document.getElementById("edit_mazot_fiyat").value = field.mazot_fiyat || 0;
    document.getElementById("edit_verim").value = field.verim || 0;
    document.getElementById("edit_satis").value = field.satis || 0;

    panel.style.display = "block";
    panel.scrollIntoView({ behavior: "smooth" });
  }
</script>
