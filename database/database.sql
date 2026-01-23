-- Database: eduflora_db
-- Sistem Informasi Edukasi Flora dan Fauna Indonesia

CREATE DATABASE IF NOT EXISTS eduflora_db;
USE eduflora_db;

-- Tabel Flora
CREATE TABLE flora (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    nama_ilmiah VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    habitat VARCHAR(255) NOT NULL,
    habitat_detail TEXT,
    asal_daerah VARCHAR(255) NOT NULL,
    status_konservasi ENUM('Aman', 'Terancam', 'Langka', 'Kritis', 'Punah di Alam') DEFAULT 'Aman',
    manfaat TEXT,
    ciri_khusus TEXT,
    image VARCHAR(500) DEFAULT 'assets/images/default-flora.svg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Fauna
CREATE TABLE fauna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    nama_ilmiah VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    habitat VARCHAR(255) NOT NULL,
    habitat_detail TEXT,
    asal_daerah VARCHAR(255) NOT NULL,
    status_konservasi ENUM('Aman', 'Terancam', 'Langka', 'Kritis', 'Punah di Alam') DEFAULT 'Aman',
    makanan VARCHAR(255),
    perilaku TEXT,
    ciri_fisik TEXT,
    image VARCHAR(500) DEFAULT 'assets/images/default-fauna.svg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data Flora
INSERT INTO flora (nama, nama_ilmiah, deskripsi, habitat, habitat_detail, asal_daerah, status_konservasi, manfaat, ciri_khusus, image) VALUES
('Rafflesia Arnoldii', 'Rafflesia arnoldii', 'Rafflesia arnoldii adalah spesies tumbuhan parasit yang terkenal sebagai bunga terbesar di dunia. Bunga ini dapat mencapai diameter hingga 1 meter dan berat hingga 10 kilogram. Rafflesia tidak memiliki daun, batang, atau akar yang terlihat, dan hidup sebagai parasit pada tanaman inang dari genus Tetrastigma.', 'Hutan Hujan Tropis', 'Tumbuh sebagai parasit pada akar tanaman merambat Tetrastigma di hutan hujan dataran rendah dengan kelembaban tinggi', 'Sumatera, Kalimantan', 'Terancam', 'Dalam pengobatan tradisional, beberapa bagian Rafflesia dipercaya memiliki khasiat untuk mengobati demam dan membantu proses persalinan, meskipun penggunaannya sangat terbatas karena kelangkaannya.', 'Bunga terbesar di dunia, tidak memiliki daun dan batang, hidup parasit, mengeluarkan bau busuk untuk menarik lalat sebagai penyerbuk', 'assets/images/rafflesia.svg'),

('Kantong Semar', 'Nepenthes spp.', 'Kantong Semar adalah tumbuhan karnivora yang unik dengan kantong perangkap untuk menangkap serangga. Indonesia memiliki lebih dari 60 spesies Nepenthes, menjadikannya negara dengan keanekaragaman kantong semar terbesar di dunia. Tumbuhan ini memiliki adaptasi khusus untuk hidup di tanah yang miskin nutrisi.', 'Hutan Tropis, Rawa Gambut', 'Tumbuh di tanah asam dan miskin nutrisi, mulai dari dataran rendah hingga pegunungan tinggi, sering ditemukan di tepi hutan dan area terbuka', 'Sumatera, Kalimantan, Sulawesi, Papua', 'Langka', 'Digunakan dalam pengobatan tradisional untuk mengobati batuk, demam, dan gangguan pencernaan. Juga memiliki potensi sebagai tanaman hias eksotik dan untuk penelitian bioteknologi.', 'Memiliki kantong perangkap untuk menangkap serangga, daun berbentuk rosette, kantong berwarna-warni dengan tutup', 'assets/images/nepenthes.svg'),

('Bunga Bangkai', 'Amorphophallus titanum', 'Bunga Bangkai atau Titan Arum adalah tumbuhan endemik Sumatera yang terkenal karena bunganya yang sangat besar dan mengeluarkan bau busuk menyengat. Tinggi bunga dapat mencapai 3 meter, menjadikannya salah satu struktur bunga tertinggi di dunia. Tumbuhan ini memiliki siklus hidup yang unik dan menarik.', 'Hutan Hujan Tropis', 'Tumbuh di lantai hutan hujan dataran rendah dengan tanah yang kaya humus dan drainase yang baik, memerlukan kelembaban tinggi', 'Sumatera', 'Terancam', 'Umbi Amorphophallus dapat diolah menjadi makanan setelah proses pengolahan khusus untuk menghilangkan racun. Juga digunakan dalam pengobatan tradisional dan sebagai tanaman hias eksotik.', 'Bunga tunggal raksasa dengan bau busuk, umbi besar di bawah tanah, daun majemuk yang sangat besar', 'assets/images/amorphophallus.svg'),

('Anggrek Hitam', 'Coelogyne pandurata', 'Anggrek Hitam adalah anggrek endemik Kalimantan yang menjadi flora identitas provinsi Kalimantan Timur. Dinamakan anggrek hitam karena lidah bunganya berwarna hitam dengan garis-garis hijau. Anggrek ini tumbuh sebagai epifit di pohon-pohon besar di hutan hujan tropis.', 'Hutan Hujan Tropis', 'Tumbuh sebagai epifit pada pohon-pohon besar di hutan primer, memerlukan kelembaban tinggi dan cahaya yang tersaring', 'Kalimantan', 'Langka', 'Sebagai tanaman hias bernilai tinggi dan simbol kebanggaan daerah Kalimantan. Memiliki potensi ekonomi dalam industri florikultura dan ekowisata.', 'Bunga berwarna hijau muda dengan lidah hitam bergaris hijau, tumbuh epifit, pseudobulb berbentuk bulat telur', 'assets/images/default-flora.svg'),

('Pohon Ulin', 'Eusideroxylon zwageri', 'Pohon Ulin atau Kayu Besi adalah pohon endemik Kalimantan yang terkenal karena kayunya yang sangat keras dan tahan lama. Pohon ini dapat hidup hingga ratusan tahun dan mencapai tinggi 50 meter. Kayu ulin sangat berharga dan telah digunakan secara tradisional untuk berbagai keperluan konstruksi.', 'Hutan Hujan Tropis', 'Tumbuh di hutan primer dataran rendah hingga ketinggian 400 meter, memerlukan tanah yang subur dan drainase baik', 'Kalimantan', 'Kritis', 'Kayu ulin sangat berharga untuk konstruksi rumah tradisional, jembatan, dan perahu karena sifatnya yang tahan air dan rayap. Juga memiliki nilai ekologis sebagai habitat berbagai satwa.', 'Kayu sangat keras dan berat, kulit batang berwarna coklat kemerahan, daun tunggal berbentuk elips', 'assets/images/default-flora.svg'),

('Edelweis Jawa', 'Anaphalis javanica', 'Edelweis Jawa adalah bunga abadi yang tumbuh di pegunungan tinggi Jawa. Bunga ini menjadi simbol keabadian dan sering dikaitkan dengan legenda cinta. Edelweis memiliki kemampuan bertahan hidup di kondisi ekstrem pegunungan dengan suhu dingin dan angin kencang.', 'Pegunungan Tinggi', 'Tumbuh di padang rumput alpine dan lereng gunung pada ketinggian 2000-3000 meter, tahan terhadap suhu dingin dan angin kencang', 'Jawa', 'Terancam', 'Digunakan dalam pengobatan tradisional untuk mengobati gangguan pernapasan dan sebagai bunga hias. Memiliki nilai simbolis dan budaya yang tinggi bagi masyarakat Jawa.', 'Bunga putih keperakan yang tidak layu, daun berbulu halus, tahan terhadap cuaca ekstrem pegunungan', 'assets/images/default-flora.svg');

-- Insert sample data Fauna
INSERT INTO fauna (nama, nama_ilmiah, deskripsi, habitat, habitat_detail, asal_daerah, status_konservasi, makanan, perilaku, ciri_fisik, image) VALUES
('Orangutan Sumatera', 'Pongo abelii', 'Orangutan Sumatera adalah salah satu dari tiga spesies orangutan yang masih hidup dan merupakan primata endemik Sumatera. Mereka adalah kera besar yang paling arboreal, menghabiskan sebagian besar hidupnya di atas pohon. Orangutan Sumatera memiliki kecerdasan tinggi dan kemampuan menggunakan alat.', 'Hutan Hujan Tropis', 'Hidup di kanopi hutan hujan primer dan sekunder, memerlukan pohon-pohon besar untuk membuat sarang dan mencari makan', 'Sumatera', 'Kritis', 'Buah-buahan (terutama buah ara), daun muda, kulit kayu, serangga, dan kadang telur burung', 'Sebagian besar hidup soliter, aktif di siang hari, membuat sarang baru setiap malam, memiliki kemampuan menggunakan alat sederhana', 'Bulu berwarna merah kecoklatan, lengan sangat panjang, tidak memiliki ekor, jantan dewasa memiliki pipi yang membesar', 'assets/images/orangutan.svg'),

('Harimau Sumatera', 'Panthera tigris sumatrae', 'Harimau Sumatera adalah subspesies harimau terkecil yang masih hidup dan merupakan satu-satunya harimau yang tersisa di Indonesia. Mereka adalah predator puncak yang berperan penting dalam menjaga keseimbangan ekosistem hutan Sumatera. Populasinya sangat terancam akibat perburuan dan hilangnya habitat.', 'Hutan Hujan Tropis', 'Hidup di hutan primer dan sekunder, dari dataran rendah hingga pegunungan, memerlukan wilayah jelajah yang luas', 'Sumatera', 'Kritis', 'Mamalia besar seperti rusa, babi hutan, tapir, dan kadang-kadang primata', 'Soliter dan teritorial, aktif pada malam hari, perenang yang baik, memiliki wilayah jelajah yang luas', 'Tubuh lebih kecil dari harimau lainnya, belang hitam lebih rapat, memiliki selaput renang di kaki', 'assets/images/harimau-sumatera.svg'),

('Komodo', 'Varanus komodoensis', 'Komodo adalah spesies kadal terbesar di dunia dan merupakan hewan endemik Indonesia. Mereka hidup di beberapa pulau di Nusa Tenggara Timur dan merupakan predator puncak di habitatnya. Komodo memiliki gigitan beracun dan kemampuan berburu yang luar biasa.', 'Savana Kering', 'Hidup di padang savana kering, hutan monsun, dan pantai di pulau-pulau kecil dengan iklim kering', 'Nusa Tenggara Timur (Pulau Komodo, Rinca, Flores)', 'Terancam', 'Mamalia besar seperti rusa, babi hutan, kerbau air, dan bangkai', 'Soliter, aktif di siang hari, dapat berlari cepat, perenang yang baik, memiliki indera penciuman yang tajam', 'Tubuh besar dan panjang hingga 3 meter, kulit bersisik, lidah bercabang, gigitan beracun', 'assets/images/komodo.svg'),

('Burung Cenderawasih', 'Paradisaea spp.', 'Burung Cenderawasih adalah kelompok burung endemik Papua yang terkenal karena keindahan bulunya. Terdapat lebih dari 40 spesies cenderawasih dengan berbagai warna dan bentuk bulu yang menakjubkan. Burung jantan memiliki ritual kawin yang spektakuler untuk menarik betina.', 'Hutan Hujan Tropis', 'Hidup di hutan hujan primer dari dataran rendah hingga pegunungan, memerlukan pohon-pohon tinggi untuk bertengger dan bersarang', 'Papua, Papua Barat', 'Terancam', 'Buah-buahan, serangga, nektar, dan kadang-kadang vertebrata kecil', 'Jantan melakukan tarian kawin yang rumit, hidup dalam kelompok kecil, aktif di pagi dan sore hari', 'Bulu jantan sangat berwarna-warni dengan ornamen khusus, betina umumnya berwarna coklat, paruh kuat', 'assets/images/default-fauna.svg'),

('Badak Jawa', 'Rhinoceros sondaicus', 'Badak Jawa adalah salah satu mamalia paling langka di dunia dengan populasi kurang dari 80 individu yang hanya tersisa di Taman Nasional Ujung Kulon. Mereka adalah herbivora besar yang berperan penting dalam ekosistem hutan sebagai penyebar biji dan pembentuk habitat.', 'Hutan Hujan Tropis', 'Hidup di hutan hujan dataran rendah dengan vegetasi yang lebat, dekat dengan sumber air dan area berlumpur', 'Jawa Barat (Taman Nasional Ujung Kulon)', 'Kritis', 'Daun, ranting muda, buah-buahan, dan berbagai jenis tumbuhan hutan', 'Soliter, aktif pada pagi dan sore hari, suka berkubang di lumpur, memiliki wilayah jelajah yang luas', 'Bercula satu, kulit berlipat seperti baju zirah, tubuh besar dan kekar, telinga dapat bergerak bebas', 'assets/images/default-fauna.svg'),

('Anoa', 'Bubalus spp.', 'Anoa adalah kerbau kerdil endemik Sulawesi yang terdiri dari dua spesies: Anoa Dataran Rendah dan Anoa Pegunungan. Mereka adalah mamalia terbesar di Sulawesi dan memiliki peran penting dalam ekosistem hutan sebagai herbivora yang membantu penyebaran biji tumbuhan.', 'Hutan Hujan Tropis', 'Hidup di hutan primer dan sekunder, dari dataran rendah hingga pegunungan, memerlukan sumber air yang bersih', 'Sulawesi', 'Terancam', 'Rumput, daun-daunan, buah-buahan yang jatuh, dan tumbuhan air', 'Hidup dalam kelompok kecil, aktif pada pagi dan sore hari, pemalu dan mudah terkejut, suka berendam di air', 'Tubuh kecil seperti kerbau mini, tanduk lurus dan pendek, bulu berwarna coklat kehitaman', 'assets/images/default-fauna.svg');

-- Create indexes for better performance
CREATE INDEX idx_flora_nama ON flora(nama);
CREATE INDEX idx_flora_status ON flora(status_konservasi);
CREATE INDEX idx_flora_habitat ON flora(habitat);
CREATE INDEX idx_fauna_nama ON fauna(nama);
CREATE INDEX idx_fauna_status ON fauna(status_konservasi);
CREATE INDEX idx_fauna_habitat ON fauna(habitat);

-- Create view for combined species data
CREATE VIEW all_species AS
SELECT 
    'flora' as type,
    id,
    nama,
    nama_ilmiah,
    deskripsi,
    habitat,
    asal_daerah,
    status_konservasi,
    image,
    created_at
FROM flora
UNION ALL
SELECT 
    'fauna' as type,
    id,
    nama,
    nama_ilmiah,
    deskripsi,
    habitat,
    asal_daerah,
    status_konservasi,
    image,
    created_at
FROM fauna
ORDER BY created_at DESC;