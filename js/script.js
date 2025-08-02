// JS code can be added here later

  document.addEventListener('DOMContentLoaded', function () {

    const hasSeenModal = localStorage.getItem('salesModalShown');



    if (!hasSeenModal) {

      const salesModal = new bootstrap.Modal(document.getElementById('salesModal'));

      salesModal.show();



      localStorage.setItem('salesModalShown', 'true');

    }

// JS code can be added here later

  document.addEventListener('DOMContentLoaded', function () {
    const hasSeenModal = localStorage.getItem('salesModalShown');

    if (!hasSeenModal) {
      const salesModal = new bootstrap.Modal(document.getElementById('salesModal'));
      salesModal.show();

      localStorage.setItem('salesModalShown', 'true');
    }
  });
  });