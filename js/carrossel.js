document.addEventListener('DOMContentLoaded', () => {
  const servicesGrid = document.getElementById('services-grid');
  const btnPrevServices = document.getElementById('btn-prev-services');
  const btnNextServices = document.getElementById('btn-next-services');

  if (servicesGrid && btnPrevServices && btnNextServices) {

    btnNextServices.addEventListener('click', () => {
      servicesGrid.scrollBy({
        left: servicesGrid.clientWidth,
        behavior: 'smooth'
      });
    });

    btnPrevServices.addEventListener('click', () => {
      servicesGrid.scrollBy({
        left: -servicesGrid.clientWidth,
        behavior: 'smooth'
      });
    });
  }
});