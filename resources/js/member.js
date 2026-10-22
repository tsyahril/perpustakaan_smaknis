import Alpine from 'alpinejs';
import axios from 'axios';

window.Alpine = Alpine;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common.Accept = 'application/json';

// Data awal dari server (lihat layouts/member.blade.php).
const boot = window.memberBoot ?? {};

Alpine.store('member', {
    login: Boolean(boot.login),
    urlLogin: boot.urlLogin ?? '/login',
    favorit: boot.favorit ?? 0,
    keranjang: boot.keranjang ?? 0,
    toasts: [],

    init() {
        if (boot.flash?.pesan) {
            this.toast(boot.flash.pesan, boot.flash.tipe);
        }
    },

    toast(pesan, tipe = 'sukses') {
        const id = `${Date.now()}-${Math.random()}`;
        this.toasts.push({ id, pesan, tipe });
        setTimeout(() => this.tutup(id), 4000);
    },

    tutup(id) {
        this.toasts = this.toasts.filter((t) => t.id !== id);
    },

    /**
     * Tangani galat axios: sesi habis diarahkan ke login, sisanya jadi toast.
     */
    galat(e) {
        if (e.response?.status === 401) {
            window.location.href = this.urlLogin;
            return;
        }

        this.toast(e.response?.data?.pesan ?? 'Terjadi kesalahan, coba lagi sebentar.', 'galat');
    },
});

Alpine.data('tombolFavorit', (url, aktif) => ({
    aktif,
    memuat: false,

    async ubah() {
        const member = Alpine.store('member');

        if (!member.login) {
            window.location.href = member.urlLogin;
            return;
        }

        if (this.memuat) return;
        this.memuat = true;

        try {
            const { data } = await axios.post(url);
            this.aktif = data.aktif;
            member.favorit = data.jumlah;
            member.toast(data.pesan);
        } catch (e) {
            member.galat(e);
        } finally {
            this.memuat = false;
        }
    },
}));

Alpine.data('tombolKeranjang', (url, sudah = false) => ({
    sudah,
    memuat: false,

    async tambah(langsung = false) {
        const member = Alpine.store('member');

        if (!member.login) {
            window.location.href = member.urlLogin;
            return;
        }

        if (this.memuat) return;
        this.memuat = true;

        try {
            const { data } = await axios.post(url, { langsung: langsung ? 1 : 0 });
            this.sudah = true;
            member.keranjang = data.jumlah;

            if (langsung) {
                window.location.href = data.url_keranjang;
                return;
            }

            member.toast(data.pesan);
        } catch (e) {
            member.galat(e);
        } finally {
            this.memuat = false;
        }
    },
}));

Alpine.data('pencarian', (urlSaran, awal = '') => ({
    q: awal,
    buka: false,
    hasil: { buku: [], kategori: [] },
    aktif: -1,
    memuat: false,
    timer: null,
    permintaan: 0,

    get daftar() {
        return [
            ...this.hasil.kategori.map((k) => ({ ...k, tipe: 'kategori' })),
            ...this.hasil.buku.map((b) => ({ ...b, tipe: 'buku' })),
        ];
    },

    ketik() {
        clearTimeout(this.timer);

        if (this.q.trim().length < 2) {
            this.hasil = { buku: [], kategori: [] };
            this.buka = false;
            return;
        }

        this.timer = setTimeout(() => this.ambil(), 250);
    },

    async ambil() {
        const nomor = ++this.permintaan;
        this.memuat = true;

        try {
            const { data } = await axios.get(urlSaran, { params: { q: this.q.trim() } });

            // Abaikan jawaban lama kalau pengguna sudah mengetik lagi.
            if (nomor !== this.permintaan) return;

            this.hasil = data;
            this.aktif = -1;
            this.buka = this.daftar.length > 0;
        } catch (e) {
            this.buka = false;
        } finally {
            if (nomor === this.permintaan) this.memuat = false;
        }
    },

    turun() {
        if (!this.buka) return;
        this.aktif = (this.aktif + 1) % this.daftar.length;
    },

    naik() {
        if (!this.buka) return;
        this.aktif = (this.aktif - 1 + this.daftar.length) % this.daftar.length;
    },

    kirim(event) {
        if (this.buka && this.aktif >= 0) {
            event.preventDefault();
            window.location.href = this.daftar[this.aktif].url;
        }
    },

    tutup() {
        this.buka = false;
        this.aktif = -1;
    },
}));

Alpine.data('hero', (jumlah, jeda = 6500) => ({
    i: 0,
    timer: null,

    init() {
        this.mulai();
    },

    mulai() {
        this.berhenti();

        if (jumlah < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        this.timer = setInterval(() => this.maju(), jeda);
    },

    berhenti() {
        clearInterval(this.timer);
    },

    maju() {
        this.i = (this.i + 1) % jumlah;
    },

    mundur() {
        this.i = (this.i - 1 + jumlah) % jumlah;
    },
}));

Alpine.data('rak', () => ({
    kiri: false,
    kanan: false,

    init() {
        this.$nextTick(() => this.cek());
        window.addEventListener('resize', () => this.cek(), { passive: true });
    },

    cek() {
        const el = this.$refs.jalur;
        this.kiri = el.scrollLeft > 4;
        this.kanan = el.scrollLeft + el.clientWidth < el.scrollWidth - 4;
    },

    geser(arah) {
        const el = this.$refs.jalur;
        el.scrollBy({ left: arah * el.clientWidth * 0.85, behavior: 'smooth' });
    },
}));

Alpine.data('bagikan', (judul) => ({
    async bagikan() {
        const member = Alpine.store('member');

        if (navigator.share) {
            try {
                await navigator.share({ title: judul, url: window.location.href });
                return;
            } catch (e) {
                if (e.name === 'AbortError') return;
            }
        }

        try {
            await navigator.clipboard.writeText(window.location.href);
            member.toast('Tautan buku disalin.');
        } catch (e) {
            member.toast('Tautan tidak bisa disalin otomatis.', 'galat');
        }
    },
}));

Alpine.start();
