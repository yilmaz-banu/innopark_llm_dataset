# InnoPark AI Assistant 🚀

InnoPark AI Assistant, InnoPark kurumsal bilgi kaynakları (yönetim standartları, girişimci destekleri, vb.) üzerinde çalışan, NotebookLM destekli yapay zeka sohbet asistanıdır.

## 🌟 Özellikler

- **Gelişmiş Chat Arayüzü**: Modern, duyarlı ve Tailwind CSS ile güçlendirilmiş "glassmorphism" tasarım.
- **NotebookLM Entegrasyonu**: Google'ın NotebookLM altyapısını kullanarak doğrudan InnoPark veri tabanından en doğru yanıtları verir.
- **Sohbet Geçmişi**: LocalStorage kullanılarak sohbet geçmişiniz tarayıcınızda güvenle saklanır. Geçmiş sohbetlerinize kolayca dönebilirsiniz.
- **Performanslı Önbellekleme (Cache)**: Sık sorulan sorular için dosya tabanlı bir önbellekleme sistemi kullanır, böylece API limitlerine takılmadan anında cevaplar alırsınız.
- **Akıllı Oturum Yönetimi**: NotebookLM oturumunuzun süresi dolduğunda, asistan bunu algılar ve tek tıklamayla (arka planda `nlm login` tetikleyerek) yeniden oturum açmanızı sağlar.
- **Typewriter (Daktilo) Efekti**: Yanıtlar, tıpkı ChatGPT gibi akıcı ve doğal bir şekilde ekrana yazdırılır.
- **Markdown Desteği**: Yanıtlar gelişmiş markdown biçimlendirmeleri (kalın, italik, liste, kod blokları vb.) içerir.

## 🛠️ Kullanılan Teknolojiler

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, Vanilla JavaScript, Tailwind CSS, Animate.css, Marked.js
- **Yapay Zeka**: NotebookLM (Google) & NLM CLI
- **Veritabanı**: Dosya tabanlı (Cache) & LocalStorage (Sohbet geçmişi)

## 📁 Proje Yapısı

```
/
├── index.php           # Ana sohbet arayüzü
├── api.php             # Backend API (NotebookLM ile iletişim & Caching)
├── cache/              # Önbelleğe alınmış soru-cevap dosyaları
├── README.md           # Proje dokümantasyonu
```

## 🚀 Kurulum & Çalıştırma

1. **Gereksinimler:**
   - PHP destekli bir web sunucusu (Örn: XAMPP, WAMP).
   - [NotebookLM CLI (`nlm`)](https://github.com/notebooklm/nlm-cli) sisteminizde yüklü ve yapılandırılmış olmalıdır.
   - PHP üzerinden `shell_exec` ve `popen` fonksiyonlarının aktif (engellenmemiş) olması gerekmektedir.

2. **Kurulum:**
   Projeyi sunucunuzun (örn: `c:\wamp64\www\`) ilgili dizinine kopyalayın.

3. **Kullanım:**
   Tarayıcınızdan projeye gidin (Örn: `http://localhost/innopark-llm/`).
   Karşınıza çıkacak arayüz üzerinden sorularınızı sorabilirsiniz.
   *Eğer "Oturum Süresi Doldu" hatası alırsanız, ekrandaki "Şimdi Giriş Yap" butonuna tıklayarak Chrome üzerinden yetki verebilirsiniz.*

## 📌 Hızlı Sorgular

Asistan üzerinde yer alan hızlı sorgu butonları ile InnoPark hakkında merak edilen konulara anında ulaşabilirsiniz:
- Ekip Üyeleri
- Firmalar
- Girişimci Desteği
- Standartlar

## 📝 Lisans

Bu proje InnoPark kullanımına özel olarak geliştirilmiştir.
