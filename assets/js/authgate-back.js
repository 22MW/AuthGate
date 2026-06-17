(function () {
  function applyTheme(root, theme) {
    root.setAttribute('data-theme', theme);
    root.querySelectorAll('[data-authgate-theme]').forEach(function (button) {
      button.classList.toggle('is-active', button.getAttribute('data-authgate-theme') === theme);
    });
  }



  function enhanceSwitches(root) {
    root.querySelectorAll('label input[type="checkbox"]').forEach(function (input) {
      var label = input.closest('label');

      if (!label || label.classList.contains('authgate-back-switch')) {
        return;
      }

      if (label.querySelector('.authgate-back-switch__track')) {
        return;
      }

      var track = document.createElement('span');
      track.className = 'authgate-back-switch__track';
      input.insertAdjacentElement('afterend', track);
      label.classList.add('authgate-back-switch');
    });
  }



  function enhancePagePicker(root) {
    var picker = root.querySelector('[data-authgate-page-picker]');

    if (!picker) {
      return;
    }

    var search = picker.querySelector('[data-authgate-page-search]');
    var results = picker.querySelector('[data-authgate-page-results]');
    var selected = picker.querySelector('[data-authgate-page-selected]');
    var buttons = Array.prototype.slice.call(picker.querySelectorAll('[data-page-id]'));

    function selectedIds() {
      return Array.prototype.slice.call(selected.querySelectorAll('[data-page-id]')).map(function (chip) {
        return chip.getAttribute('data-page-id');
      });
    }

    function renderResults() {
      var term = (search.value || '').toLowerCase().trim();
      var activeIds = selectedIds();
      var visible = 0;

      buttons.forEach(function (button) {
        var title = (button.getAttribute('data-page-title') || '').toLowerCase();
        var show = term.length > 0 && title.indexOf(term) !== -1 && activeIds.indexOf(button.getAttribute('data-page-id')) === -1;
        button.hidden = !show;
        if (show) {
          visible += 1;
        }
      });

      results.hidden = visible === 0;
    }

    function addPage(id, title) {
      if (selected.querySelector('[data-page-id="' + id + '"]')) {
        return;
      }

      var chip = document.createElement('span');
      chip.className = 'authgate-page-picker__chip';
      chip.setAttribute('data-page-id', id);
      chip.innerHTML = '<input type="hidden" name="excluded_pages[]" value="' + id + '">' + title + '<button type="button" aria-label="Quitar ' + title + '" data-authgate-remove-page>×</button>';
      selected.appendChild(chip);
      search.value = '';
      renderResults();
    }

    search.addEventListener('input', renderResults);
    search.addEventListener('focus', renderResults);

    results.addEventListener('click', function (event) {
      var button = event.target.closest('[data-page-id]');

      if (!button) {
        return;
      }

      addPage(button.getAttribute('data-page-id'), button.getAttribute('data-page-title'));
    });

    selected.addEventListener('click', function (event) {
      var remove = event.target.closest('[data-authgate-remove-page]');

      if (!remove) {
        return;
      }

      remove.closest('[data-page-id]').remove();
      renderResults();
    });



  function enhanceSubnav(root) {
    root.querySelectorAll('[data-authgate-subnav]').forEach(function (layout) {
      var links = Array.prototype.slice.call(layout.querySelectorAll('.authgate-back-subnav a'));
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

  document.addEventListener('click', function (event) {
      if (!picker.contains(event.target)) {
        results.hidden = true;
      }
    });
  }



  function enhanceSubnav(root) {
    root.querySelectorAll('[data-authgate-subnav]').forEach(function (layout) {
      var links = Array.prototype.slice.call(layout.querySelectorAll('.authgate-back-subnav a'));
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
    var root = document.querySelector('[data-authgate-back]');

    if (!root) {
      return;
    }

    var stored = window.localStorage.getItem('authgateBackTheme') || 'dark';
    applyTheme(root, stored);
    enhanceSwitches(root);
    enhancePagePicker(root);
    enhanceSubnav(root);

    root.querySelectorAll('[data-authgate-theme]').forEach(function (button) {
      button.addEventListener('click', function () {
        var theme = button.getAttribute('data-authgate-theme') || 'dark';
        window.localStorage.setItem('authgateBackTheme', theme);
        applyTheme(root, theme);
      });
    });
  });
})();
