/* script.js - JavaScript (client-side)
   BAGIAN JAVASCRIPT: estimasi biaya real-time sebelum submit
*/

function formatRupiahJS(angka) {
    return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function hitungEstimasi() {
    const hargaSewa = parseInt(document.getElementById("harga_sewa")?.value) || 0;
    const lamaSewa = parseInt(document.getElementById("lama_sewa")?.value) || 0;
    const tipeKamar = document.getElementById("tipe_kamar")?.value || "";
    const estimasiBox = document.getElementById("estimasi-biaya");
    if (!estimasiBox) return;

    if (hargaSewa < 100000 || lamaSewa < 1) {
        estimasiBox.style.display = "none";
        return;
    }
    estimasiBox.style.display = "block";

    let totalBiaya = hargaSewa * lamaSewa;
    let persenDiskon = 0;
    let diskon = 0;

    if (tipeKamar === "Deluxe" && lamaSewa >= 12) {
        persenDiskon = 15;
        diskon = totalBiaya * 0.15;
    } else if (tipeKamar === "VIP" && lamaSewa >= 6) {
        persenDiskon = 10;
        diskon = totalBiaya * 0.10;
    } else if (lamaSewa >= 12) {
        persenDiskon = 5;
        diskon = totalBiaya * 0.05;
    }

    document.getElementById("est-total").textContent = formatRupiahJS(totalBiaya);
    document.getElementById("est-diskon").textContent =
        persenDiskon > 0 ? formatRupiahJS(Math.round(diskon)) + " (" + persenDiskon + "%)" : "Tidak ada diskon";
    document.getElementById("est-akhir").textContent = formatRupiahJS(Math.round(totalBiaya - diskon));
}

function isiHargaDariKamar() {
    const select = document.getElementById("no_kamar");
    const hargaInput = document.getElementById("harga_sewa");
    const tipeSelect = document.getElementById("tipe_kamar");
    if (!select || !hargaInput) return;

    const opt = select.options[select.selectedIndex];
    if (opt && opt.dataset.harga) {
        hargaInput.value = opt.dataset.harga;
        if (tipeSelect && opt.dataset.tipe) {
            tipeSelect.value = opt.dataset.tipe;
        }
        hitungEstimasi();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const hargaInput = document.getElementById("harga_sewa");
    const lamaInput = document.getElementById("lama_sewa");
    const tipeSelect = document.getElementById("tipe_kamar");
    const kamarSelect = document.getElementById("no_kamar");

    if (hargaInput) hargaInput.addEventListener("input", hitungEstimasi);
    if (lamaInput) lamaInput.addEventListener("input", hitungEstimasi);
    if (tipeSelect) tipeSelect.addEventListener("change", hitungEstimasi);
    if (kamarSelect) kamarSelect.addEventListener("change", isiHargaDariKamar);

    hitungEstimasi();
});
