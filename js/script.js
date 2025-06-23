// JS code 

  document.addEventListener('DOMContentLoaded', function () {
    const hasSeenModal = localStorage.getItem('salesModalShown');

    if (!hasSeenModal) {
      const salesModal = new bootstrap.Modal(document.getElementById('salesModal'));
      salesModal.show();

      localStorage.setItem('salesModalShown', 'true');
    }
  });

