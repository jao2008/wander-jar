document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('.dashboard-page.dash');

  if (!root) {
    return;
  }

  requestAnimationFrame(() => {
    root.classList.add('is-ready');
  });

  initStatNumbers();
  initActivityChart();
});

function initStatNumbers() {
  const statNumbers = document.querySelectorAll('.stat-number[data-target]');

  if (!statNumbers.length) {
    return;
  }

  const animateNumber = (element, end, duration = 900) => {
    const finalValue = Number.parseInt(end, 10) || 0;
    const startTime = performance.now();

    const tick = (now) => {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const current = Math.floor(finalValue * eased);

      element.textContent = current;

      if (progress < 1) {
        requestAnimationFrame(tick);
        return;
      }

      element.textContent = finalValue;
    };

    requestAnimationFrame(tick);
  };

  if (!('IntersectionObserver' in window)) {
    statNumbers.forEach((element) => {
      animateNumber(element, element.dataset.target || '0');
    });

    return;
  }

  const numbersObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        const element = entry.target;
        const target = element.dataset.target || '0';

        animateNumber(element, target);
        observer.unobserve(element);
      });
    },
    {
      threshold: 0.45,
    }
  );

  statNumbers.forEach((element) => {
    numbersObserver.observe(element);
  });
}

function initActivityChart() {
  const chart = document.getElementById('dashActivityChart');

  if (!chart) {
    return;
  }

  const bars = chart.querySelectorAll('.activity-bar');
  const values = chart.querySelectorAll('.activity-bar-value');

  if (!bars.length) {
    return;
  }

  bars.forEach((bar) => {
    bar.style.setProperty('--animated-height', '0%');
  });

  const animateChart = () => {
    bars.forEach((bar, index) => {
      const finalHeight = bar.style.getPropertyValue('--bar-height') || '10%';

      setTimeout(() => {
        bar.style.setProperty('--animated-height', finalHeight);
        bar.classList.add('is-visible');
      }, index * 75);
    });

    values.forEach((valueElement, index) => {
      setTimeout(() => {
        valueElement.classList.add('is-visible');
      }, 180 + index * 75);
    });
  };

  if (!('IntersectionObserver' in window)) {
    animateChart();
    return;
  }

  const chartObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        animateChart();
        observer.unobserve(entry.target);
      });
    },
    {
      threshold: 0.35,
    }
  );

  chartObserver.observe(chart);
}