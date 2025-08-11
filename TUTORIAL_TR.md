# GPT-5 Nano Site Asistanı — Kurulum ve Kullanım Rehberi

Bu belge, eklentiyi sıfırdan kurup çalıştırmanız için adım adım bir **tutorial** sağlar.

## 1) Sistem Gereksinimleri
- WordPress 5.9+
- PHP 7.4+
- Dış ağ erişimi (OpenAI API için)

## 2) Kurulum
1. Eklenti ZIP dosyasını **Eklentiler → Yeni Ekle → Eklenti Yükle** ekranından yükleyin ve **Etkinleştir**in.
2. **Ayarlar → GPT-5 Nano Site Assistant** ekranına gidin.
3. **OpenAI API Key** alanına anahtarınızı girin. Alternatif: `wp-config.php` dosyanıza şu satırı ekleyin:
   ```php
   define('GPT5_API_KEY', 'REPLACE_ME'); // demo anahtar
   ```
4. **Allowed Models (CSV)** alanında kullanılabilir modelleri belirleyin (örn. `gpt-5-nano,gpt-5-mini,gpt-5`).
5. Varsayılan model ve dil ayarlarını yapın, **Kaydet**’e basın.

## 3) Sayfaya Ekleme
- Gutenberg editörde **GPT-5 Nano Site Assistant** bloğunu seçip ekleyin.
- Alternatif: Shortcode kullanın:
  ```
  [gpt5_chat]
  ```

## 4) Güvenlik Ayarları
- **Nonce (otomatik)**: WordPress, front-end isteklerine nonce ekler; REST API bu değeri doğrular.
- **Rate Limit**: Varsayılan 60 sn’de 5 istek. Ayarlar → Security bölümünden değiştirebilirsiniz.
- **Yasaklı Kelimeler**: Virgülle ayırarak girin; model cevabında maskeleme yapılır.

## 5) İyi Uygulamalar
- API anahtarını mümkünse **wp-config.php** içinde sabit olarak tutun.
- “System Prompt” alanını sitenizin politikasına göre düzenleyin (ör. “Tıbbi tavsiye verme”).
- “Show Sources” açık kalsın; şeffaflık sağlar.
- Canlıda reCAPTCHA eklemeyi düşünebilirsiniz (3. parti eklentilerle).

## 6) Sorun Giderme
- **403 Invalid nonce**: Sayfayı yenileyin; önbellek eklentisi nonce’ı eskitmiş olabilir.
- **429 Too many requests**: Rate limit değerini yükseltin veya bekleyin.
- **500 Missing API key**: Anahtarı admin panelden veya `wp-config.php`’den tanımlayın.
- **Yanıt gelmiyor**: Sunucunuzdan `api.openai.com` adresine çıkış izni olduğundan emin olun.

## 7) Güncelleme / Kaldırma
- Güncellemelerde ayarlar korunur.
- Kaldırma sırasında verileri temizlemek istiyorsanız önce ayarları sıfırlayın.

Başarılar!
