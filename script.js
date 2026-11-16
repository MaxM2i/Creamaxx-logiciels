const toggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('.nav');
if (toggle && nav) {
  toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
    toggle.textContent = open ? '×' : '☰';
  });
}
document.querySelectorAll('[data-year]').forEach(el => el.textContent = new Date().getFullYear());
const contactForm = document.querySelector('[data-contact-form]');
if (contactForm) {
  contactForm.addEventListener('submit', event => {
    event.preventDefault();
    const success = contactForm.querySelector('.form-success');
    if (success) success.classList.add('show');
    const submit = contactForm.querySelector('button[type="submit"]');
    if (submit) submit.textContent = 'Formulaire de démonstration';
  });
}
