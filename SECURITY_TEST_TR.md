# Güvenlik ve Test Kontrol Listesi

## Güvenlik
- [ ] API anahtarı front-end’e **asla** sızmıyor
- [ ] Nonce doğrulaması hatalı isteklere 403 dönüyor
- [ ] IP Rate Limit: varsayılan 5 istek/60 sn, 429 dönüyor
- [ ] Girdi sanitizasyonu: `sanitize_text_field`, uzunluk sınırı
- [ ] Çıktı hijyeni: `esc_html` / `wp_kses_post` (UI’da HTML kabul edilecekse)
- [ ] Hata mesajları ayrıntı sızdırmıyor (stack trace, key vb. yok)
- [ ] Sistem Prompt uygun (tıbbi/finansal uyarılar vs.)
- [ ] WP_Query sadece **publish** içerikleri alıyor
- [ ] Kaynak linkleri doğru URL ve başlıkla dönüyor
- [ ] İzinli model listesi (whitelist) çalışıyor

## İşlevsel Testler
- [ ] TR/EN otomatik dil yanıtı
- [ ] Dil sabitleme (TR/EN) çalışıyor
- [ ] maxChars kesmesi ve “Devamını Göster” davranışı
- [ ] Gutenberg blok ayarları → front-end’e yansıyor
- [ ] Shortcode ve blok çıktıları aynı
- [ ] Boş soru → 400 hatası
- [ ] Sunucu erişim hatası → 500 kontrollü hata

## Performans
- [ ] Uzun içerikte bağlam kırpma çalışıyor
- [ ] Gereksiz büyük context gönderilmiyor
- [ ] (Opsiyonel) CDN üzerinden Bootstrap yüklü

## Notlar
- Canlıda CAPTCHA ve daha sıkı WAF kullanımı önerilir.
