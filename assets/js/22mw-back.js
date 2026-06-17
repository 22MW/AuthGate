/*
 * 22MW-BACK base JS
 * Copiar a cada plugin como assets/js/22mw-back.js y extender con JS propio del plugin.
 */
(function () {
  function applyTheme(root, theme) {
    root.setAttribute('data-theme', theme);
    root.querySelectorAll('[data-mw22-back-theme]').forEach(function (button) {
      button.classList.toggle('is-active', button.getAttribute('data-mw22-back-theme') === theme);
    });
  }

  function showToast(root, message, type) {
    var toast = document.createElement('div');
    toast.className = 'mw22-back-toast' + (type === 'error' ? ' is-error' : '');
    toast.textContent = message;
    root.appendChild(toast);
    setTimeout(function () { toast.classList.add('is-hiding'); }, 2800);
    setTimeout(function () { toast.remove(); }, 3200);
  }

  function showUpdatedToast(root) {
    var params = new URLSearchParams(window.location.search);

    if (params.get('updated') !== '1') {
      return;
    }

    showToast(root, root.getAttribute('data-mw22-updated-message') || 'Ajustes guardados.');
  }

  function enhanceSwitches(root) {
    root.querySelectorAll('label input[type="checkbox"]').forEach(function (input) {
      var label = input.closest('label');

      if (!label || label.classList.contains('mw22-back-switch')) {
        return;
      }

      if (label.querySelector('.mw22-back-switch__track')) {
        return;
      }

      var track = document.createElement('span');
      track.className = 'mw22-back-switch__track';
      input.insertAdjacentElement('afterend', track);
      label.classList.add('mw22-back-switch');
    });
  }

  function enhanceSubnav(root) {
    root.querySelectorAll('[data-mw22-back-subnav]').forEach(function (layout) {
      var links = Array.prototype.slice.call(layout.querySelectorAll('.mw22-back-subnav a'));
      var sections = links.map(function (link) {
        return document.querySelector(link.getAttribute('href'));
      }).filter(Boolean);

      links.forEach(function (link) {
        link.addEventListener('click', function (event) {
          var target = document.querySelector(link.getAttribute('href'));

          if (!target) {
            return;
          }

          event.preventDefault();
          links.forEach(function (item) { item.classList.remove('is-active'); });
          link.classList.add('is-active');
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      });

      if (!('IntersectionObserver' in window) || !sections.length) {
        return;
      }

      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }

          links.forEach(function (link) {
            link.classList.toggle('is-active', link.getAttribute('href') === '#' + entry.target.id);
          });
        });
      }, { rootMargin: '-20% 0px -65% 0px', threshold: 0.01 });

      sections.forEach(function (section) {
        observer.observe(section);
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-mw22-back]').forEach(function (root) {
      var storageKey = root.getAttribute('data-mw22-theme-key') || 'mw22BackTheme';
      var stored = window.localStorage.getItem(storageKey) || 'dark';

      applyTheme(root, stored);
      showUpdatedToast(root);
      enhanceSwitches(root);
      enhanceSubnav(root);

      root.querySelectorAll('[data-mw22-back-theme]').forEach(function (button) {
        button.addEventListener('click', function () {
          var theme = button.getAttribute('data-mw22-back-theme') || 'dark';
          window.localStorage.setItem(storageKey, theme);
          applyTheme(root, theme);
        });
      });
    });
  });
})();
