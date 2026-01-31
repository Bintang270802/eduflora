-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 31, 2026 at 01:50 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eduflora_db`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `all_species`
-- (See below for the actual view)
--
CREATE TABLE `all_species` (
`type` varchar(5)
,`id` int
,`nama` varchar(255)
,`nama_ilmiah` varchar(255)
,`deskripsi` mediumtext
,`habitat` varchar(255)
,`asal_daerah` varchar(255)
,`status_konservasi` varchar(13)
,`image` varchar(500)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `fauna`
--

CREATE TABLE `fauna` (
  `id` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nama_ilmiah` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `habitat` varchar(255) NOT NULL,
  `habitat_detail` text,
  `asal_daerah` varchar(255) NOT NULL,
  `status_konservasi` enum('Aman','Terancam','Langka','Kritis','Punah di Alam') DEFAULT 'Aman',
  `makanan` varchar(255) DEFAULT NULL,
  `perilaku` text,
  `ciri_fisik` text,
  `image` varchar(500) DEFAULT 'assets/images/default-fauna.jpg',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fauna`
--

INSERT INTO `fauna` (`id`, `nama`, `nama_ilmiah`, `deskripsi`, `habitat`, `habitat_detail`, `asal_daerah`, `status_konservasi`, `makanan`, `perilaku`, `ciri_fisik`, `image`, `created_at`, `updated_at`) VALUES
(12, 'Orangutan Kalimantan', 'Pongo pygmaeus', 'Primata cerdas yang sebagian besar hidup di atas pohon.', 'Hutan Hujan Tropis', 'Hutan tropis dengan pohon tinggi dan buah melimpah.', 'Kalimantan', 'Terancam', 'cvbnm,', 'Soliter dan sangat cerdas.', 'Soliter dan sangat cerdas.', 'assets/images/fauna_1769349260.jpeg', '2026-01-24 14:29:35', '2026-01-25 13:54:20'),
(14, 'Cenderawasih', 'Paradisaeidae', 'Burung eksotis berwarna cerah dengan bulu indah memikat yang sering dijuluki burung surga dari Papua.', 'Hutan Hujan Tropis', 'Hutan primer Papua dengan pohon tinggi.', 'papua', 'Langka', 'Omnivora', 'Aktif di pagi hari dan memiliki ritual kawin unik.', 'Bulu berwarna cerah dan ekor panjang menjuntai.', 'assets/images/fauna_1769349122_69762002e8bd4.webp', '2026-01-25 13:52:02', '2026-01-25 13:52:02'),
(15, 'Komodo', 'Varanus komodoensis', 'Komodo merupakan kadal terbesar di dunia yang memiliki kemampuan berburu luar biasa dengan bantuan indera penciuman tajam dan air liur yang mengandung bakteri berbahaya.', 'Savana Kering', 'Savana kering, padang rumput, dan semak belukar.', 'Nusa Tenggara Timur', 'Terancam', 'Karnivora', 'Agresif dan dominan sebagai predator.', 'Tubuh besar, kulit bersisik tebal, dan lidah bercabang', 'assets/images/fauna_1769350017_697623812471b.jpeg', '2026-01-25 14:06:57', '2026-01-25 14:06:57'),
(16, 'Tarsius', 'Tarsius spectrum', 'Tarsius adalah primata kecil nokturnal dengan mata besar, mampu melompat jauh, dan hidup di hutan tropis.', 'Hutan Hujan Tropis', 'Hidup di hutan hujan tropis dan hutan sekunder dengan pepohonan rapat untuk melompat.', 'Sulawesi', 'Terancam', 'Karnivora', 'Aktif di malam hari\\r\\n\\r\\nBerkomunikasi dengan suara ultrasonik\\r\\n\\r\\nMelompat antarpohon', 'Mata sangat besar\\r\\n\\r\\nEkor panjang untuk keseimbangan\\r\\n\\r\\nJari panjang dan lentur', 'assets/images/fauna_1769607683.jpeg', '2026-01-28 13:28:16', '2026-01-29 03:30:01'),
(17, 'Badak Jawa', 'Rhinoceros sondaicus', 'Badak Jawa merupakan mamalia bercula satu yang sangat langka, bertubuh besar, dan hidup menyendiri di hutan lebat Taman Nasional Ujung Kulon. Hewan ini jarang terlihat karena bersifat pemalu, aktif di area tertutup, serta menghindari interaksi dengan manusia. Keberadaannya sangat terbatas karena populasinya sedikit dan habitat alaminya semakin sempit.', 'Hutan Hujan Tropis', 'Hidup dekat sungai dan rawa\\r\\n\\r\\nMembutuhkan lumpur untuk berendam\\r\\n\\r\\nTinggal di area hutan yang rapat', 'banten', 'Kritis', 'herbivora', 'Hidup menyendiri (soliter)\\r\\n\\r\\nMenghindari manusia\\r\\n\\r\\nAktif pagi dan sore\\r\\n\\r\\nJarang terlihat langsung', 'Satu cula kecil\\r\\n\\r\\nKulit tebal berlipat\\r\\n\\r\\nTubuh besar dan berat\\r\\n\\r\\nWarna abu-abu gelap', 'assets/images/fauna_1769656888_697ad23841083.webp', '2026-01-29 03:21:28', '2026-01-29 03:21:28'),
(18, 'Kukang Jawa', 'Nycticebus javanicus', 'Kukang Jawa merupakan primata nokturnal yang aktif pada malam hari dan bergerak sangat lambat untuk menghindari predator. Hewan ini memiliki mata besar yang berfungsi menangkap cahaya di kondisi gelap. Kukang juga unik karena dapat menghasilkan racun dari kelenjar di lengannya, yang dicampur dengan air liur sebagai alat pertahanan diri. Racun ini membuatnya berbahaya bagi musuh dan manusia jika diganggu.', 'Hutan Hujan Tropis', '\\r\\nHutan tropis lebat dengan banyak pohon sebagai jalur bergerak.', 'Jawa', 'Terancam', 'Omnivora', 'Aktif malam hari\\r\\n\\r\\nBergerak lambat\\r\\n\\r\\nMengeluarkan racun dari air liur', 'Mata besar, tubuh kecil, bulu tebal.', 'assets/images/fauna_1769657751_697ad5974f697.webp', '2026-01-29 03:35:51', '2026-01-29 03:35:51');

-- --------------------------------------------------------

--
-- Table structure for table `flora`
--

CREATE TABLE `flora` (
  `id` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nama_ilmiah` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `habitat` varchar(255) NOT NULL,
  `habitat_detail` text,
  `asal_daerah` varchar(255) NOT NULL,
  `status_konservasi` enum('Aman','Terancam','Langka','Kritis','Punah di Alam') DEFAULT 'Aman',
  `manfaat` text,
  `ciri_khusus` text,
  `image` varchar(500) DEFAULT 'assets/images/default-flora.jpg',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `flora`
--

INSERT INTO `flora` (`id`, `nama`, `nama_ilmiah`, `deskripsi`, `habitat`, `habitat_detail`, `asal_daerah`, `status_konservasi`, `manfaat`, `ciri_khusus`, `image`, `created_at`, `updated_at`) VALUES
(12, 'Rafflesia Arnoldii', 'Rafflesia arnoldii', 'Rafflesia Arnoldii merupakan bunga terbesar di dunia yang tidak memiliki batang, daun, maupun akar sejati, serta hanya mekar dalam waktu singkat.', 'Hutan Hujan Tropis', 'Tumbuh sebagai parasit pada tanaman inang Tetrastigma di hutan tropis lembap dengan curah hujan tinggi.', 'Bengkulu, Sumatra', 'Aman', 'Digunakan sebagai objek penelitian botani dan ikon konservasi flora Indonesia.', 'Diameter bunga bisa mencapai 1 meter dan mengeluarkan bau busuk seperti bangkai.', 'assets/images/flora_1769261305_6974c8f922a57.jpg', '2026-01-24 13:28:25', '2026-01-25 13:41:41'),
(15, 'Anggrek Hitam', 'Coelogyne pandurata', 'Anggrek endemik Kalimantan berkelopak hijau dengan bibir bunga hitam pekat yang sangat unik tropis!!', 'Hutan Hujan Tropis', 'Hidup menempel pada batang pohon besar di area hutan yang lembap dan teduh.', 'Kalimantan Timur', 'Terancam', 'Tanaman hias bernilai tinggi dan simbol kekayaan flora Kalimantan.', 'Warna hitam pada bagian tengah bunga yang sangat jarang ditemukan pada anggrek lain.', 'assets/images/flora_1769350265.webp', '2026-01-25 13:47:47', '2026-01-25 14:11:05'),
(16, 'Edelweiss Jawa', 'Anaphalis javanica', 'Edelweiss Jawa merupakan tanaman khas pegunungan tinggi yang dikenal sebagai bunga keabadian karena mampu bertahan lama meskipun telah dipetik.', 'Pegunungan', 'Tumbuh di tanah berpasir dan berbatu pada ketinggian lebih dari 2.000 meter dengan suhu dingin dan angin kencang.', 'Pegunungan Jawa', 'Terancam', 'Sebagai tanaman konservasi dan simbol ekosistem pegunungan Indonesia.', 'Bunga tidak mudah layu dan memiliki bulu halus pada permukaan kelopak.', 'assets/images/flora_1769349885_697622fd12c9b.jpeg', '2026-01-25 14:04:45', '2026-01-25 14:04:45'),
(18, 'Jamur Pelangi', 'Trametes versicolor', 'Jamur berwarna-warni menyerupai pelangi yang tumbuh berlapis pada kayu mati di kawasan hutan tropis.', 'Hutan Hujan Tropis', 'Tumbuh di batang kayu mati atau lapuk pada hutan lembap dengan sirkulasi udara baik.', 'Asia, Eropa, Amerika', 'Aman', 'Pengurai alami kayu\\r\\n\\r\\nBahan penelitian dan pengobatan tradisional\\r\\n\\r\\nMenjaga keseimbangan ekosistem hutan', 'Warna berlapis menyerupai pelangi\\r\\n\\r\\nTekstur tipis dan keras\\r\\n\\r\\nTumbuh berkelompok seperti kipas', 'assets/images/flora_1769607465_697a11296cc76.jpg', '2026-01-28 13:37:45', '2026-01-28 13:37:45'),
(19, 'Cendana', 'Santalum album', 'Cendana merupakan pohon penghasil kayu wangi bernilai ekonomi tinggi yang tumbuh di daerah kering Nusa Tenggara Timur, terutama pada wilayah hutan monsun dengan musim kemarau panjang. Kayunya menghasilkan aroma khas yang tahan lama dan banyak dimanfaatkan untuk minyak atsiri, parfum, serta keperluan tradisional.', 'Hutan Monsun', 'Tumbuh di tanah kering dan berbatu\\r\\n\\r\\nTahan kekeringan\\r\\n\\r\\nSering hidup di lahan terbuka', 'Nusa Tenggara Timur', 'Aman', 'Bahan minyak atsiri\\r\\n\\r\\nParfum dan kosmetik\\r\\n\\r\\nObat tradisional\\r\\n\\r\\nKerajinan dan ukiran', 'Kayu dan akar beraroma harum\\r\\n\\r\\nBau wangi bertahan puluhan tahun\\r\\n\\r\\nTermasuk tumbuhan setengah parasit', 'assets/images/flora_1769657319_697ad3e7e1bb9.jpg', '2026-01-29 03:28:39', '2026-01-29 03:28:39'),
(20, 'Pandan Laut', 'Pandanus tectorius', 'Pandan laut merupakan tumbuhan pantai yang memiliki akar tunjang kuat untuk menahan terpaan angin laut dan gelombang. Tanaman ini mampu tumbuh di tanah berpasir dengan kadar garam tinggi serta berfungsi melindungi pantai dari abrasi dan erosi. Daunnya yang panjang dan kuat juga dimanfaatkan oleh masyarakat pesisir.', 'Pantai', 'Tumbuh di pasir pantai dan daerah pesisir terbuka yang terkena angin laut.\\r\\n\\r\\n', 'pesisir indonesia', 'Aman', 'Penahan abrasi\\r\\n\\r\\nAnyaman daun\\r\\n\\r\\nTanaman pelindung pantai', 'Akar tunjang besar dan daun panjang berduri.', 'assets/images/flora_1769657897_697ad62957f5f.jpeg', '2026-01-29 03:38:17', '2026-01-29 08:53:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fauna`
--
ALTER TABLE `fauna`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fauna_nama` (`nama`),
  ADD KEY `idx_fauna_status` (`status_konservasi`),
  ADD KEY `idx_fauna_habitat` (`habitat`);

--
-- Indexes for table `flora`
--
ALTER TABLE `flora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_flora_nama` (`nama`),
  ADD KEY `idx_flora_status` (`status_konservasi`),
  ADD KEY `idx_flora_habitat` (`habitat`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fauna`
--
ALTER TABLE `fauna`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `flora`
--
ALTER TABLE `flora`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

-- --------------------------------------------------------

--
-- Structure for view `all_species`
--
DROP TABLE IF EXISTS `all_species`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `all_species`  AS SELECT 'flora' AS `type`, `flora`.`id` AS `id`, `flora`.`nama` AS `nama`, `flora`.`nama_ilmiah` AS `nama_ilmiah`, `flora`.`deskripsi` AS `deskripsi`, `flora`.`habitat` AS `habitat`, `flora`.`asal_daerah` AS `asal_daerah`, `flora`.`status_konservasi` AS `status_konservasi`, `flora`.`image` AS `image`, `flora`.`created_at` AS `created_at` FROM `flora`union all select 'fauna' AS `type`,`fauna`.`id` AS `id`,`fauna`.`nama` AS `nama`,`fauna`.`nama_ilmiah` AS `nama_ilmiah`,`fauna`.`deskripsi` AS `deskripsi`,`fauna`.`habitat` AS `habitat`,`fauna`.`asal_daerah` AS `asal_daerah`,`fauna`.`status_konservasi` AS `status_konservasi`,`fauna`.`image` AS `image`,`fauna`.`created_at` AS `created_at` from `fauna` order by `created_at` desc  ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
