<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Tarlam Takipte', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
  <?php if (!empty($flash['success'])): ?>
    <div class="flash flash--success" data-flash><?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <?php if (!empty($flash['error'])): ?>
    <div class="flash flash--error" data-flash><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <?= $content ?>
  <script src="/assets/js/app.js"></script>
  <script>
    setTimeout(function () {
      document.querySelectorAll("[data-flash]").forEach(function (item) {
        item.classList.add("flash--hide");
      });
    }, 3200);
  </script>
</body>
</html>
