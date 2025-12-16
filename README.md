Proyek PBL kami adalah Sistem Informasi dan e-katalog Usaha Muda.
Sistem Informasi ini dirancang untuk membantu toko "Usaha Muda" dalam mengelola aktivitas operasional seperti penjualan, stok barang, manajemen supplier, dan pelaporan keuangan secara digital.Berikut adalah pembagian tugas masing2:

1.Abid Mustaghfirin : Implementasi autentikasi pada layanan user dengan ketentuan: (Memiliki endpoint: register, login, user profile, user CRUD, Adanya validasi input, Adanya error handling, Adanya unit test (minimal 1))

2.Andini Zakira : Membuat satu layanan tambahan (product-service) dengan ketentuan: (Adanya CRUD, Adanya validasi input, Adanya error handling, Adanya unit test (minimal 1))

3.Wahyu Darma : Membuat satu layanan (order-service) yang melakukan call ke layanan user dan call ke layanan pada product-service dan memenuhi ketentuan: (Mengirim dan menerima Correlation ID, Meneruskan Authorization token, Membuat error handling konsisten untuk kegagalan service lain, Unit test minimal 1) dan Membangun middleware Correlation ID di seluruh service

4.Akbar Hidayatullah : Mengimplementasikan logging terdistribusi: (Log context, Logging format konsisten, Proof of distributed tracing (cuplikan log))
