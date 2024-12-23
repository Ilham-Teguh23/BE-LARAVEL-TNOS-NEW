// Menimpa fungsi console agar tidak ada log yang terlihat
console.log = function () { };
console.warn = function () { };
console.error = function () { };
console.info = function () { };

// Cegah akses Inspect Element dengan keyboard
document.addEventListener("keydown", function (event) {
    // Cek jika tombol F12, Ctrl+Shift+I, Ctrl+Shift+C, atau Ctrl+U ditekan
    if (
        event.key === "F12" ||
        (event.ctrlKey && event.shiftKey && event.key === "I") ||
        (event.ctrlKey && event.shiftKey && event.key === "C") ||
        (event.ctrlKey && event.key === "U")
    ) {
        event.preventDefault();
        alert("Inspect Element tidak diizinkan!");
    }
});

// Nonaktifkan klik kanan untuk mencegah akses menu konteks
document.addEventListener("contextmenu", function (event) {
    event.preventDefault();
    alert("Klik kanan tidak diizinkan!");
});

// Deteksi jika Developer Tools dibuka
(function () {
    const devtools = /./;
    devtools.toString = function () {
        alert("Jangan coba-coba membuka Developer Tools!");
        return "Developer Tools Detected";
    };
    console.log(devtools);
})();
