(function () {
  'use strict';
  var body = document.body;
  var toggle = document.querySelector('.admin-nav-toggle');
  var closers = document.querySelectorAll('[data-admin-nav-close]');
  var search = document.querySelector('[data-admin-nav-search]');

  function setOpen(open) {
    body.classList.toggle('admin-nav-open', open);
    if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  if (toggle) toggle.addEventListener('click', function () { setOpen(!body.classList.contains('admin-nav-open')); });
  Array.prototype.forEach.call(closers, function (closer) {
    closer.addEventListener('click', function () { setOpen(false); });
  });
  document.addEventListener('keydown', function (event) { if (event.key === 'Escape') setOpen(false); });

  if (search) {
    search.addEventListener('input', function () {
      var term = search.value.trim().toLowerCase();
      Array.prototype.forEach.call(document.querySelectorAll('[data-admin-nav-item]'), function (item) {
        item.hidden = term !== '' && item.textContent.toLowerCase().indexOf(term) === -1;
      });
      Array.prototype.forEach.call(document.querySelectorAll('.admin-nav-group'), function (group) {
        group.hidden = !group.querySelector('[data-admin-nav-item]:not([hidden])');
      });
    });
  }
}());
