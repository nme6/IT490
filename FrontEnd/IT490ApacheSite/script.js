const btnSwitch = document.getElementById('btnSwitch');
  const icon = btnSwitch.querySelector('i');

  // Get the stored theme from local storage, or set it to 'light' by default
  const storedTheme = localStorage.getItem('theme');
  if (storedTheme === 'dark') {
    document.documentElement.setAttribute('data-bs-theme', 'dark');
    icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
    btnSwitch.classList.remove('btn-light');
    btnSwitch.classList.remove('btn-outline-dark');
    btnSwitch.classList.add('btn-dark');
    btnSwitch.classList.add('btn-outline-light');
  } else {
    document.documentElement.setAttribute('data-bs-theme', 'light');
    btnSwitch.classList.remove('btn-dark');
    btnSwitch.classList.remove('btn-outline-light');
    btnSwitch.classList.add('btn-light');
    btnSwitch.classList.add('btn-outline-dark');
  }

  // Set the initial icon based on the theme
  const theme = document.documentElement.getAttribute('data-bs-theme');
  if (theme === 'dark') {
    icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
    btnSwitch.classList.remove('btn-light');
    btnSwitch.classList.add('btn-dark');
  }

  btnSwitch.addEventListener('click', () => {
    const theme = document.documentElement.getAttribute('data-bs-theme');
    if (theme === 'dark') {
      document.documentElement.setAttribute('data-bs-theme', 'light');
      icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
      localStorage.setItem('theme', 'light');
      btnSwitch.classList.remove('btn-dark');
      btnSwitch.classList.remove('btn-outline-light');
      btnSwitch.classList.add('btn-light');
      btnSwitch.classList.add('btn-outline-dark');
    } else {
      document.documentElement.setAttribute('data-bs-theme', 'dark');
      icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
      localStorage.setItem('theme', 'dark');
      btnSwitch.classList.remove('btn-light');
      btnSwitch.classList.remove('btn-outline-dark');
      btnSwitch.classList.add('btn-dark');
      btnSwitch.classList.add('btn-outline-light');
    }
  });

  // Listen for theme changes and update the icon immediately
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
  mediaQuery.addEventListener('change', e => {
    if (e.matches) {
      icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
      localStorage.setItem('theme', 'dark');
      btnSwitch.classList.remove('btn-light');
      btnSwitch.classList.remove('btn-outline-dark');
      btnSwitch.classList.add('btn-dark');
      btnSwitch.classList.add('btn-outline-light');
    } else {
      icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
      localStorage.setItem('theme', 'light');
      btnSwitch.classList.remove('btn-dark');
      btnSwitch.classList.remove('btn-outline-light');
      btnSwitch.classList.add('btn-light');
      btnSwitch.classList.add('btn-outline-dark');
    }
  });