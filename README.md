# Autobase Twitter Webhook

Bot Autobase Twitter Using webhooks to receive and deliver real time updates.<br>
  > Ref : https://developer.twitter.com/en/docs/twitter-api/enterprise/account-activity-api/guides/getting-started-with-webhooks

Bot untuk menerima DM lalu mempublikasikannya sebagai Tweet, atau yang biasa disebut *menfess*.<br>
*Seperti akun @bdngfess, @bandungfess, @sunda_fess, @codingfess dll*.<br><br>
dilarang keras digunakan untuk membuat base yang mengandung konten dewasa / alter.
___
  - :smoking: [Give me cigarette here](https://trakteer.id/setandarisurga/tip) 
  - Follow me on Twitter [@senggolbaok](https://twitter.com/senggolbaok)

## Required (*)
- [x] Akun Developer Twitter sudah di-Approve *Elevated Access*nya
- [x] Set Up *Account Activity API* di [Dev Environments](https://developer.twitter.com/en/account/environments)
- [x] App permissions pilih yang *Read and write and Direct message*

## Installation
1. Isi `CONSUMER KEY, dll` di file `config.php`
2. Jalankan `setWebhook.php`

## Features
- [x] Mengirim Pesan otomatis ketika follback seseorang
- [x] Filter text dari kata tidak pantas
- [x] Filter akun sender tidak bisa mengirim *menfess*
  - [x] jika followers kurang dari 10
  - [ ] ~akun belum lebih dari 1 bulan~ *(will be updated soon)*
  - [ ] ~tweet kurang dari 500~ *(will be updated soon)*
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
- [ ] Not support video/gif. *(will be updated soon)*.

## Example & Instruction
#### Example :
[Klik disini untuk contoh Bot](contoh/)
___
#### Instruction - /logs
Setiap ada yang mengirim atau unsend menfess, logs-nya akan disimpan dalam bentuk json. Jika logs dihapus maka (admin dan sender) tidak akan bisa menghapus menfess (menggunakan *Command*). <br><br>
File `shadowLog.php` dibuat untuk melihat, dan membersihkan logs. [Klik disini untuk contoh logs](contoh/git9.png)

## Contributing
Contributions of all kinds are welcome.
- Bug reports
- Fix bugs / add new features

## License
This open-source software is distributed under the MIT License. See [License](LICENSE)
