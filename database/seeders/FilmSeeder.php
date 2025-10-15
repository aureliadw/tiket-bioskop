<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Film;
use Carbon\Carbon;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Film::create([
            'judul' => 'Andai Ibu Tidak Menikah dengan Ayah',
            'deskripsi' => 'Saat beasiswa kuliah kedokterannya terancam dicabut, Alin (Amanda Rawles) yang merantau terpaksa kembali ke rumah. Setelah kembali ke rumah, ia kemudian menyadari bahwa kehidupan keluarganya kini makin susah, sementara Ayahnya (Bucek) jarang ada di rumah. Adik (Nayla Purnama)dan Kakaknya (Eva Celia) juga harus menanggung banyak beban di keluarga hingga mengorbankan diri dan mimpi-mimpi mereka. Alin juga tanpa sengaja menemukan buku harian milik ibunya. Isi buku harian tersebut penuh dengan memori masa muda ibunya, termasuk mimpi-mimpinya. Ini membuat Alin bertanya-tanya, andai ibu tidak menikah dengan ayah, akankah hidup ibunya lebih bahagia? Pertanyaan itu pun membuat Alin berpikir apakah Irfan (Indian Akbar), pasangannya, adalah pasangan yang tepat untuk dirinya?',
            'genre' => 'Drama',
            'durasi' => 120,
            'sutradara' => 'Rina Putri',
            'produser' => 'Satria Films',
            'produksi' => 'Nusantara Pictures',
            'penulis' => 'Ahmad Fauzi',
            'pemain' => 'Amanda Manopo, Reza Rahadian',
            'poster_image' => 'images/film8.jpg',
            'trailer_video' => 'https://youtu.be/IfkNOBDzFVM?si=BEO7RFJb4ChgT0FW',
            'rating' => 8.0,
            'status' => 'sedang_tayang',
            'tanggal_rilis' => Carbon::parse('2025-01-10'),
        ]);

        Film::create([
            'judul' => 'Maryam: Janji dan Jiwa yang Terikat',
            'deskripsi' => 'Maryam, seorang wanita yang sejak lahir hidup dalam teror dan terikat oleh janji kelam dengan sosok tak kasat mata yang mengikutinya setiap saat. Ia menerima surat misterius yang tak bisa dihancurkan dan mendengar bisikan gaib yang hanya ia yang bisa mendengar, membuatnya kehilangan pegangan atas dirinya sendiri. Film ini menggabungkan unsur horor supranatural dan psikologis, dengan kisahnya yang diangkat dari pengalaman nyata berdasarkan podcast viral "Lentera Malam". ',
            'genre' => 'Drama, Thriller',
            'durasi' => 115,
            'sutradara' => 'Hanung Bramantyo',
            'produser' => 'Starvision',
            'produksi' => 'IndoCinema',
            'penulis' => 'Nurul Aini',
            'pemain' => 'Tissa Biani, Refal Hady',
            'poster_image' => 'images/film9.jpg',
            'trailer_video' => 'https://youtu.be/tTMGGd9I3pg?si=irvIaEUL1nZ07VSu',
            'rating' => 7.5,
            'status' => 'sedang_tayang',
            'tanggal_rilis' => Carbon::parse('2025-02-01'),
        ]);

        Film::create([
            'judul' => 'Sukma',
            'deskripsi' => 'Kepindahan Arini (Luna Maya) dan keluarganya ke kota kecil untuk memulai hidup baru, justru berbalik menjadi petaka setelah mereka menemukan sebuah cermin kuno di ruang rahasia. Serangkaian keanehan terjadi. Suara dan penampakan yang tidak diduga membuat Arini cemas akan keselamatan dirinya dan keluarganya. Ditambah lagi munculnya sosok misterius Ibu Sri (Christine Hakim). Waktu terus berjalan, dan Arini harus mengungkap masa lalu dan misteri cermin tersebut - sebelum semuanya terlambat dan Arini kehilangan semuanya.',
            'genre' => 'Horror, Mystery',
            'durasi' => 105,
            'sutradara' => 'Joko Anwar',
            'produser' => 'Visinema',
            'produksi' => 'Rapi Films',
            'penulis' => 'Joko Anwar',
            'pemain' => 'Tara Basro, Ario Bayu',
            'poster_image' => 'images/film10.jpg',
            'trailer_video' => 'https://youtube.com/watch?v=sukma',
            'rating' => 8.2,
            'status' => 'sedang_tayang',
            'tanggal_rilis' => Carbon::parse('2025-04-12'),
        ]);

        Film::create([
            'judul' => 'The Conjuring',
            'deskripsi' => 'menceritakan kasus terakhir Ed dan Lorraine Warren saat mereka membantu keluarga Smurl di West Pittston, Pennsylvania, yang telah diteror selama bertahun-tahun oleh gangguan supranatural intensif, termasuk serangan fisik dan paranormal.',
            'genre' => 'Horror, Supernatural',
            'durasi' => 112,
            'sutradara' => 'James Wan',
            'produser' => 'Peter Safran',
            'produksi' => 'Warner Bros',
            'penulis' => 'Chad Hayes, Carey W. Hayes',
            'pemain' => 'Vera Farmiga, Patrick Wilson',
            'poster_image' => 'images/film3.jpg',
            'trailer_video' => 'https://youtube.com/watch?v=conjuring',
            'rating' => 8.0,
            'status' => 'sedang_tayang',
            'tanggal_rilis' => Carbon::parse('2013-07-19'),
        ]);

        Film::create([
            'judul' => 'Rangga dan Cinta',
            'deskripsi' => 'Jakarta, 2001. Hidup Cinta ((Leya Princy)) kelihatan nyaris sempurna. Dia punya sahabat yang solid di SMA, keluarga yang penyayang, dan status sebagai cewek paling populer di sekolah. Segala hal terasa aman dan seru—sampai suatu hari, Cinta kalah lomba puisi dari cowok super misterius bernama Rangga (El Putra Sarira). Rangga beda dari siapa pun yang pernah Cinta temui. Dia tidak suka keramaian, lebih suka membaca buku di pojokan, dan kata-katanya bisa membuat hati orang yang dengar bergetar. Awalnya Cinta kesal, tapi lama-lama penasaran… Masalahnya, dunia Rangga jauh berbeda dari dunia Cinta. Satu penuh tawa dan drama sahabat, satu lagi sepi tapi dalam. Dan di masa remaja yang serba cepat, kadang perasaan datang sebelum kita siap buat mengerti. Akibat sebuah kejadian pada sahabatnya, Cinta harus memilih antara sahabat-sahabat yang selalu mendukungnya atau cinta pertamanya.',
            'genre' => 'Musical, Drama, Romance',
            'durasi' => 119,
            'sutradara' => 'Riri Riza',
            'produser' => 'Mira Lesmana, Nicholas Saputra, Toto Prasetyanto',
            'produksi' => 'Miles Films',
            'penulis' => 'Mira Lesmana, Titien Wattimena',
            'pemain' => 'Vera Farmiga, Patrick Wilson',
            'poster_image' => 'images/film7.jpg',
            'trailer_video' => 'https://youtu.be/otTjJZUFGl0?si=2MTtvcT9bm_uSuLr',
            'rating' => 8.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-11-19'),
        ]);

        Film::create([
            'judul' => 'Kang Solah',
            'deskripsi' => 'menceritakan kasus terakhir Ed dan Lorraine Warren saat mereka membantu keluarga Smurl di West Pittston, Pennsylvania, yang telah diteror selama bertahun-tahun oleh gangguan supranatural intensif, termasuk serangan fisik dan paranormal.',
            'genre' => 'Horror, Comedy',
            'durasi' => 116,
            'sutradara' => 'Herwin Novianto',
            'produser' => 'Frederica',
            'produksi' => 'Falcon Pictures',
            'penulis' => 'Chad Hayes, Carey W. Hayes',
            'pemain' => 'Andre Taulany, Rigen Rakelna, Indra Jegel, Indro Warkop, Tora Sudiro, Davina Karamoy, Asri Welas, Indy Barends, Kenzy Taulany',
            'poster_image' => 'images/film6.jpg',
            'trailer_video' => 'https://youtu.be/vm_N0vdsDYU?si=bdXZzySBVx7mrKjo',
            'rating' => 8.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-11-19'),
        ]);

        Film::create([
            'judul' => 'Chainsaw Man',
            'deskripsi' => 'Dalam perang brutal antara iblis, pemburu, dan musuh rahasia, seorang gadis misterius bernama Reze (Reina Ueda) masuk ke dunia Denji (Kikunosuke Toya). Denji menghadapi pertempuran paling mematikan yang pernah ada, dipicu oleh cinta di dunia di mana bertahan hidup tidak mengenal aturan.',
            'genre' => 'Horror, Supernatural',
            'durasi' => 100,
            'sutradara' => 'Tatsuya Yoshihara',
            'produser' => 'Keisuke Seshimo, Makoto Kimura, Manabu Otsuka',
            'produksi' => 'Columbia Pictures',
            'penulis' => 'Hiroshi Seko, Tatsuki Fujimoto',
            'pemain' => 'Kikunosuke Toya, Reina Ueda, Tomori Kusunoki, Shiori Izawa, Shogo Sakata, Ai Fairouz, Karin Takahashi',
            'poster_image' => 'images/film4.jpg',
            'trailer_video' => 'https://youtu.be/EPaoHkV0dYw',
            'rating' => 8.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2013-11-19'),
        ]);

        Film::create([
            'judul' => 'Jembatan Shirotul Mustaqim',
            'deskripsi' => 'Paska bencana tsunami, dan adanya isu penggelapan dana bantuan, Arya (Raihan Khan) kerap diteror melihat sebuah tempat yang diduga sebagai Jembatan Shiratal Mustaqim, jembatan paling ditakuti di akhirat yang menghubungkan ke surga dan terbentang di atas neraka. Arya dan ibunya (Imelda Therinne) lantas berusaha menyelidiki kaitan antara penggelapan dana dan Jembatan Shiratal Mustaqim. Namun, usaha mereka itu harus bertaruh dengan nyawa.',
            'genre' => 'Horror',
            'durasi' => 116,
            'sutradara' => 'Bounty Umbara',
            'produser' => 'Dheeraj Kalwani',
            'produksi' => 'Dee Company',
            'penulis' => 'Erwanto Alphadullah',
            'pemain' => 'Imelda Therine, Raihan Khan, Agus Kuncoro, Mike Lucock, Eduwart Manalu, Rory Asyari, Khalif Al Juna',
            'poster_image' => 'images/film11.jpg',
            'trailer_video' => 'https://youtu.be/EPaoHkV0dYw',
            'rating' => 8.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-11-20'),
        ]);

        Film::create([
            'judul' => 'Tukar Takdir',
            'deskripsi' => 'Penerbangan Jakarta Airways 79 hilang kontak dan ketika ditemukan, RAWA (Nicholas Saputra) adalah satu-satunya penumpang yang selamat membawa pulang luka-luka dan trauma. Selain menjadi saksi dalam investigasi jatuhnya pesawat, Rawa juga menjadi penyambung duka maupun amarah putri tunggal dari pilot, ZAHRA (Adhisty Zara) dan istri penumpang yang bertukar tempat duduk dengannya, DITA (Marsha Timothy).',
            'genre' => 'Drama',
            'durasi' => 107,
            'sutradara' => 'Mouly Surya',
            'produser' => 'Chand Parwez Servia, Riza, Rama Adi, Mithu Nisar',
            'produksi' => 'Starvision, Cinesurya, Legacy Pictures',
            'penulis' => 'Mouly Surya',
            'pemain' => 'Nicholas Saputra, Marsha Timothy, Adhisty Zara, Meriam Bellina, Marcella Zalianty, Teddy Syach, Roy Sungkono, Ariyo Wahab, Revalkdo, Hannah Al Rashid, Ayez Kassar, Devi Permatasari, Ringgo Ag',
            'poster_image' => 'images/film13.jpg',
            'trailer_video' => 'https://youtu.be/Mk6myHKi3MY?si=Ee0tTZcm1Jhfjcgi',
            'rating' => 7.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-11-20'),
        ]);

        Film::create([
            'judul' => 'Getih Ireng',
            'deskripsi' => 'Pram (Darius Sinathrya) dan Rina (Titi Kamal) adalah pasangan suami istri yang sangat mendambakan anak. Tapi mereka mengalami gangguan gaib yang membuat Rina terus keguguran dan terancam tidak akan pernah bisa punya anak selamanya.',
            'genre' => 'Horror',
            'durasi' => 107,
            'sutradara' => 'Tommy Dewo',
            'produser' => 'Rocky Soraya',
            'produksi' => 'Hitmaker Studios',
            'penulis' => 'Riheam Junianti',
            'pemain' => 'Titi Kamal, Darius Sinathrya, Sara Wijayanto, Nungki Kusumastuti, Egy Fedly, Ivonne Dahler, Agus Firmansyah, Tenno Ali, Bambang Oeban, Shafira Doyle, Muhammad Segaf, Bonifasius Jose',
            'poster_image' => 'images/film14.jpg',
            'trailer_video' => 'https://youtu.be/vbnON9APWvE?si=5N69uw1U8Kz31LB2',
            'rating' => 7.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-11-20'),
        ]);

        Film::create([
            'judul' => 'Yakin Nikah',
            'deskripsi' => 'Perjalanan NIKEN (Enzy Storia) menempuh realita hubungan masa kini, yang penuh ujian dari masa lalu, keluarga, ekspektasi sosial, hingga mimpi pribadi yang belum tercapai, melalui konflik yang kocak, awkward, atau bahkan menyakitkan. Ekspektasi akan pernikahan yang sempurna membuatnya semakin dilema Yakin Nikah? atau Yakin Nikah.',
            'genre' => 'Drama, Romance',
            'durasi' => 108,
            'sutradara' => 'Pritagita Arianegara',
            'produser' => 'Shierly Kosasih, Ervina Isleyen',
            'produksi' => 'Adhya Pictures',
            'penulis' => 'Bene Dion Rajagukguk, Sigit Sulistyo, Erwin Wu',
            'pemain' => 'Enzy Storia, Maxime Bouttier, Jourdy Pranata, Amanda Rigby, Tora Sudiro, Ersa Mayori, Agnes Naomi, Lukman Sardi, Arya Vasco, Nadine Emanuella, Izabel Jahja, Mike Lucock, Jerome Kurnia, Indian',
            'poster_image' => 'images/film15.jpg',
            'trailer_video' => 'https://youtu.be/zP5dvsKV4Ko?si=U2SvHHymCxQUtDEc',
            'rating' => 7.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-12-08'),
        ]);

        Film::create([
            'judul' => 'Si Paling Actor',
            'deskripsi' => 'Gilang (Jourdy Pranata) sudah belasan tahun jadi figuran, suatu hari ketika sedang syuting film horror ia diculik bersama pemeran utama laki-laki, pemeran utama perempuan, dan sutradara film tersebut. Para penculik berniat menghabisi mereka setelah meminta tebusan. Untungnya, bekal pengalaman Gilang selama menjadi figuran memberinya harapan untuk selamat!',
            'genre' => 'Comedy',
            'durasi' => 108,
            'sutradara' => 'Ody Harahap',
            'produser' => 'Manoj Punjabi',
            'produksi' => 'MD Pictures',
            'penulis' => 'Adhitya Mulya',
            'pemain' => 'Jourdy Pranata sebagai Gilang, Beby Tsabina sebagai Rachel Hesington, dan Kevin Julio sebagai Kevin Sumitro',
            'poster_image' => 'images/film16.jpg',
            'trailer_video' => 'https://youtu.be/IUMPOj9NcNQ?si=noK2ZUd7hdU-8rB5',
            'rating' => 7.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-12-23'),
        ]);

        Film::create([
            'judul' => 'Shutter',
            'deskripsi' => 'DARWIN (Vino G Bastian), seorang fotografer senior yang konsisten menggunakan kamera manual, terkejut ketika menemukan adanya penampakan bayangan perempuan di dalam setiap foto yang dia ambil. Tak lama, sosok perempuan itu pun hadir secara nyata meneror dirinya. PIA (Anya Geraldine), kekasih Darwin, memutuskan untuk menyelidiki identitas sosok perempuan tersebut. Ternyata perempuan itu adalah LILIES (Niken Anjani), mahasiswi berprestasi yang pernah kuliah di kampus mereka. Ketika satu persatu teman-teman Darwin tewas. Pia yakin kalau ini ada hubungannya dengan dosa yang pernah dilakukan Darwin di masa lalu.',
            'genre' => 'Drama, Horror',
            'durasi' => 108,
            'sutradara' => 'Herwin Novianto',
            'produser' => 'Frederica',
            'produksi' => 'Falcon Pictures',
            'penulis' => 'Alim Sudio',
            'pemain' => 'Vino G Bastian, Anya Geraldine, Niken Anjani, Nugie, Donny Alamsyah, Andri Mashadi, Rangga Natra, Dewi Gita, Rukman Rosadi',
            'poster_image' => 'images/film17.jpg',
            'trailer_video' => 'https://youtu.be/sOx_Nm-9XyM?si=rtqY0it4JtbwIRyc',
            'rating' => 7.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-12-11'),
        ]);

        Film::create([
            'judul' => 'Legenda Kelam Malin Kundang',
            'deskripsi' => 'Seorang pelukis yang dikenal lewat karya-karya micro painting yang mendunia, baru saja pulih dari kecelakaan. Ketika ia berusaha kembali menjalani hidupnya, seorang perempuan tua tiba-tiba datang dan mengaku sebagai ibunya. Tapi dia tidak ingat wajah ibu yang dia tinggalkan 18 tahun yang lalu. Alif (Rio Dewanto) terseret masuk ke dalam sebuah rahasia kelam. Terinspirasi dari folklore paling ikonik di Indonesia, Malin Kundang, film ini menafsirkan kembali cerita rakyat dalam balutan drama misteri yang mencekam. 27 November 2025 di Bioskop.',
            'genre' => 'Drama, Mystery',
            'durasi' => 108,
            'sutradara' => 'Rafki Hidayat, Kevin Rahardjo',
            'produser' => 'Tia Hasibuan, Joko Anwar',
            'produksi' => 'Come and See Pictures, Rapi Films, Legacy Pictures',
            'penulis' => 'Joko Anwar, Aline Djayasukmana, Rafki Hidayat',
            'pemain' => 'Rio Dewanto, Faradina Mufti, Vonny Anggraini, Nova Eliza, Gambit Saifullah, Jordan Omar, Sulthan Hamonangan, Tony Merle',
            'poster_image' => 'images/film18.jpg',
            'trailer_video' => 'https://youtu.be/sOx_Nm-9XyM?si=rtqY0it4JtbwIRyc',
            'rating' => 7.0,
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-12-11'),
        ]);

        Film::create([
            'judul' => 'Tak Kenal Maka Taaruf',
            'poster_image' => 'images/film19.jpg',
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2025-12-25'),
        ]);

        Film::create([
            'judul' => 'Pangku',
            'poster_image' => 'images/film20.jpg',
            'status' => 'akan_tayang',
            'tanggal_rilis' => Carbon::parse('2026-01-05'),
        ]);
    }
}
