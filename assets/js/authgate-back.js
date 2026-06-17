(function () {
  function applyTheme(root, theme) {
    root.setAttribute('data-theme', theme);
    root.querySelectorAll('[data-authgate-theme]').forEach(function (button) {
      button.classList.toggle('is-active', button.getAttribute('data-authgate-theme') === theme);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var root = document.querySelector('[data-authgate-back]');

    if (!root) {
      return;
    }

    var stored = window.localStorage.getItem('authgateBackTheme') || 'dark';
    applyTheme(root, stored);

    root.querySelectorAll('[data-authgate-theme]').forEach(function (button) {
      button.addEventListener('click', function () {
        var theme = button.getAttribute('data-authgate-theme') || 'dark';
        window.localStorage.setItem('authgateBackTheme', theme);
        applyTheme(root, theme);
      });
    });
  });
})();
