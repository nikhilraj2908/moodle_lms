/*
    Accordiontopic.js
    Copyright: 2022 ThemesAlmond
*/
var acc = document.getElementsByClassName("accordionalmondb");
var i;
for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener('click', function() {
        this.classList.toggle('almondbactive');
        var panelalmondb = this.nextElementSibling;
        if (panelalmondb.style.display === 'block') {
            panelalmondb.style.display = 'none';
        } else {
            panelalmondb.style.display = 'block';
        }
    });
}
