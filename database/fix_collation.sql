ALTER DATABASE tarlam_takipte
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_turkish_ci;

ALTER TABLE users
  CONVERT TO CHARACTER SET utf8mb4
  COLLATE utf8mb4_turkish_ci;

ALTER TABLE fields
  CONVERT TO CHARACTER SET utf8mb4
  COLLATE utf8mb4_turkish_ci;

ALTER TABLE fields
  MODIFY tur ENUM('Hızlı Kayıt', 'Normal Kayıt', 'Detaylı Kayıt')
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_turkish_ci
  NOT NULL;
