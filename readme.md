# GPT-5 Nano Site Asistanı (WordPress Eklentisi)

Bu eklenti, WordPress sitenizde **yalnızca site içeriğine** (yayınlanmış yazılar ve sayfalar) dayanarak cevap veren, **GPT-5-nano** tabanlı bir sohbet asistanı sağlar. Gutenberg **blok** ve **shortcode** ile sayfalara eklenebilir; ön yüzde **Bootstrap 5** arayüzü kullanır.

## Özellikler
- GPT-5-nano varsayılan model; model seçimi (whitelist) ile değiştirilebilir
- Otomatik dil algılama veya sabit dil (Türkçe/İngilizce)
- Sadece site-içi içerikten yanıt (WP_Query tabanlı bağlam)
- Gutenberg blok + `[gpt5_chat]` shortcode
- Bootstrap 5 arayüz, erişilebilirlik (aria-live vb.)
- Güvenlik: CSRF (nonce), IP rate limit (vars. 5/60sn), girdi/çıktı sanitizasyonu
- Yönetici ayarları: API anahtarı, whitelist, varsayılan dil/model, sistem prompt, karakter sınırı, kaynak gösterimi, yasaklı kelimeler

## Hızlı Kurulum
1. **Eklentiyi yükleyin ve etkinleştirin.**
2. **Ayarlar → GPT-5 Nano Site Assistant** sayfasından **OpenAI API key** girin.  
   - İsterseniz `wp-config.php` dosyanıza şu sabiti ekleyin:  
     ```php
     define('GPT5_API_KEY', 'REPLACE_ME'); // demo anahtar, sonradan değiştirin
     ```
   - Eklenti önce bu sabiti kullanır; yoksa admin ayarındaki anahtarı okur.
3. Varsayılan model ve dil ayarlarını yapın, kaydedin.
4. Gutenberg editörde **“GPT-5 Nano Site Assistant”** bloğunu ekleyin veya sayfaya şu shortcode’u koyun:  
   ```
   [gpt5_chat]
   ```

## Kullanım
- Ziyaretçi mesaj yazdığında eklenti, WordPress’te ilgili içerikleri arar, özet bir **bağlam** oluşturur ve yalnızca bu bağlama dayanarak yanıt üretir.
- Kaynak linkleri (başlık + URL) cevabın altında gösterilebilir (ayarlar → açık).
- Blok ayarlarında **model**, **dil**, **maks. karakter**, **kaynak gösterimi** değiştirilebilir.

## Demo API Key ve Değiştirme
- Geliştirme/sunum amacıyla `wp-config.php`’ye `GPT5_API_KEY` sabitini ekleyebilirsiniz.  
- Canlıya geçmeden önce **gerçek anahtarınızla değiştirin** ve admin panelden veya sabit üzerinden yönetin.

## Sık Sorular
**Bu eklenti ChatGPT Plus gerektirir mi?**  
Hayır, API üzerinden çalışır; ChatGPT aboneliği gerekmez.

**Veriler site dışına çıkıyor mu?**  
Sadece kullanıcı sorusu ve site içeriğinden seçilen özet bağlam **OpenAI API**’ye gönderilir. Kişisel veri göndermeyin.

**RAG (vektör arama) var mı?**  
Bu sürüm WP_Query tabanlıdır. İleride vektör tabanlı arama modülü eklenebilir.

## Lisans
GPLv2+
