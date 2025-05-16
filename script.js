const tmenu = document.getElementById('tmenu');
const navLinks = document.querySelector('.nav-links');
const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

tmenu.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    tmenu.classList.toggle('active');
});

dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', (e) => {
        const dropdown = e.target.nextElementSibling;
        dropdown.classList.toggle('active');
        toggle.classList.toggle('active');
    });
});

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function() {
        document.querySelectorAll('.nav-link').forEach(item => item.classList.remove('active'));
        this.classList.add('active');
    });
});
