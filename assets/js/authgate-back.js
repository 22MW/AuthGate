(function () {
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

    document.addEventListener('click', function (event) {
      if (!picker.contains(event.target)) {
        results.hidden = true;
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var root = document.querySelector('[data-mw22-back].authgate-back');

    if (!root) {
      return;
    }

    enhancePagePicker(root);
  });
})();
