## NO LONGER AVAILABLE
Sorry, this script no longer available | reason : https://twitter.com/TwitterDev/status/1649191520250245121<br>
Still work if using Enterprise APIs | reason : https://twitter.com/TwitterDev/status/1649191522485817345<br>


## Bot Autobase Twitter (PHP)

Bot Menfess / Autobase Twitter, using webhooks to receive and deliver real time updates.<br>
Bot untuk menerima DM lalu mempublikasikannya sebagai Tweet, atau yang biasa disebut *menfess*.<br>
*Seperti akun @bdngfess, @bandungfess, @sunda_fess, @codingfess dll*. <br>
:arrow_right:[Example](#example--instruction)
___
:warning: ***dilarang keras digunakan untuk membuat base yang mengandung konten dewasa / alter.***
___
- Coded with :smoking: by @senggolbaok

## Required (*)
- [x] Akun Developer Twitter sudah di-Approve *Elevated Access*nya
- [x] Set Up *Account Activity API* di [Dev Environments](https://developer.twitter.com/en/account/environments)
- [x] App permissions pilih yang *Read and write and Direct message*

## Installation
1. Isi `CONSUMER KEY, dll` di file `config.php`
2. Jalankan `setWebhook.php`
3. Done~

## Features
- [x] Mengirim Pesan otomatis ketika follback seseorang
- [x] Filter text dari kata tidak pantas
- [x] Filter akun sender tidak bisa mengirim *menfess* jika :
  - [x] followers kurang dari 10
  - [ ] akun belum lebih dari 1 bulan *(will be updated soon)* :x:
  - [ ] tweet kurang dari 500 *(will be updated soon)* :x:
- [x] Menfess status dengan gambar
- [x] Menfess lebih dari 280 karakter
- [x] Mengirim [quick reply](https://developer.twitter.com/en/docs/twitter-api/v1/direct-messages/quick-replies/api-reference/options) button
- [x] Tambahkan kata `OFF` di **Bio** untuk menonaktifkan Base. (Jika tidak ada kata `OFF` di Bio, maka Bot akan berfungsi)
- [x] Unsend menfess
  - Command `/unsend` untuk menghapus menfess yang terakhir dikirimnya.
  - Atau command `/unsend url` *(menambahkan url manual)* 
- [x] Command khusus admin : 
   - `/delete url` untuk menghapus menfess.
   - `/cari url` untuk mengetahui siapa *Sender* dari menfess *yang dimaksud*
   - `/unfollow url` untuk menghapus menfess sekaligus mengunfollow sendernya

## Bugs
- [ ] Not support video/gif. *(will be updated soon)*. :x:

## License
This open-source software is distributed under the MIT License. See [License](LICENSE)

## Contributing
All kinds of contributions are welcome.
- Bug reports.
- Fix bugs / add new features.

## Example & Instruction

#### - Send Automated Messages when Follback someone
![This is an image](contoh/git1.png)
#### - Filter message
![This is an image](contoh/git2.png)
#### - Quick reply button
![This is an image](contoh/git3.png)
#### - Send Message when success posted as Tweet
![This is an image](contoh/git4.png)
#### - More than 280 Character (w/ Image)
![This is an image](contoh/git5.png)
<br><br>![This is an image](contoh/git6.png)
#### - More than 280 Character (no Image)
![This is an image](contoh/git11.png)
#### - Unsend menfess
![This is an image](contoh/git7.png)
#### Command *Admin Only*
![This is an image](contoh/git8.png)
___
#### Instruction - /logs
Setiap ada yang mengirim atau unsend menfess, logs-nya akan disimpan dalam bentuk json. Jika logs dihapus maka (admin dan sender) tidak akan bisa menghapus menfess (menggunakan *Command*). <br><br>
File `shadowLog.php` dibuat untuk melihat, dan membersihkan logs. <br><br>
![This is an image](contoh/git9.png)

## Reference & Library

> Ref : https://developer.twitter.com/en/docs/twitter-api/enterprise/account-activity-api/guides/getting-started-with-webhooks

> Lib : [php-twitter-webhook-account-activity-api](https://github.com/sadaimudiNaadhar/php-twitter-webhook-account-activity-api)

> Lib : [twitteroauth](https://github.com/abraham/twitteroauth)
