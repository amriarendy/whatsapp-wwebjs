const phoneNumberFormater = function(number) {
    // menghilangkan karakter selain angka atau karekter lainnya
    let formatted = number.replace(/\D/g, '');
    // menghilangkan angka 0 didepan dengan 62
    if (formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1);
    }
    // jika tidak ada @c.us maka tambahkan @c.us
    if (!formatted.endsWith('@c.us')) {
        formatted += '@c.us';
    }
    return formatted;
}

module.exports = {
    phoneNumberFormater
};