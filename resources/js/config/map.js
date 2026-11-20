export const mapConfig = {
    // Penyedia peta dipusatkan di sini supaya mudah diganti saat produksi
    tileUrl: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    defaultCenter: [-2.5, 118], // tampilan awal: seluruh Indonesia
    defaultZoom: 5,
    pointZoom: 17, // zoom saat titik sudah dipilih
};
