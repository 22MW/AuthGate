document.addEventListener('DOMContentLoaded', function () {
  var items = document.querySelectorAll('.reveal');

  if (!items.length) {
    return;
  }

  if (!('IntersectionObserver' in window)) {
    items.forEach(function (item) {
      item.classList.add('in');
    });
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('in');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -40px 0px'
  });

  items.forEach(function (item) {
    observer.observe(item);
  });
});
