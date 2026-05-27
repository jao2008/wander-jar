// ====================================
// RESET PASSWORD JS
// ====================================

document.addEventListener('DOMContentLoaded', () => {

  // ====================================
  // PASSWORD TOGGLE (Mostrar/Esconder) - AMBAS AS PASSWORDS
  // ====================================
  const passwordToggles = document.querySelectorAll('.password-toggle');
  
  passwordToggles.forEach((toggle) => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      
      const wrapper = this.closest('.password-wrapper');
      const input = wrapper.querySelector('.form-input');
      const icon = this.querySelector('i');
      
      if (input.type === 'password') {
        // Mostrar password
        input.type = 'text';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
        this.setAttribute('aria-label', 'Esconder password');
      } else {
        // Esconder password
        input.type = 'password';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
        this.setAttribute('aria-label', 'Mostrar password');
      }
      
      // Feedback visual
      this.style.transform = 'translateY(-50%) scale(0.9)';
      setTimeout(() => {
        this.style.transform = 'translateY(-50%) scale(1)';
      }, 100);
    });
  });

  // ====================================
  // FORM VALIDATION FEEDBACK
  // ====================================
  const form = document.querySelector('.auth-form');
  
  if (form) {
    const inputs = form.querySelectorAll('.form-input');
    
    inputs.forEach(input => {
      // Remove classe de erro ao começar a escrever
      input.addEventListener('input', () => {
        if (input.classList.contains('is-invalid')) {
          input.classList.remove('is-invalid');
          
          const errorMsg = input.parentElement.querySelector('.form-error');
          if (errorMsg) {
            errorMsg.style.opacity = '0';
            setTimeout(() => errorMsg.remove(), 300);
          }
        }
      });
    });
    
    // Prevenir submit múltiplo
    form.addEventListener('submit', (e) => {
      const submitBtn = form.querySelector('.btn-submit');
      
      // Validar se as passwords coincidem
      const password = form.querySelector('input[name="password"]');
      const passwordConfirmation = form.querySelector('input[name="password_confirmation"]');
      
      if (password && passwordConfirmation) {
        if (password.value !== passwordConfirmation.value) {
          e.preventDefault();
          passwordConfirmation.classList.add('is-invalid');
          showError(passwordConfirmation, 'As passwords não coincidem');
          return;
        }
      }
      
      if (submitBtn.disabled) {
        e.preventDefault();
        return;
      }
      
      submitBtn.disabled = true;
      submitBtn.style.opacity = '0.7';
      submitBtn.innerHTML = '<span>A redefinir...</span><i class="bi bi-hourglass-split"></i>';
      
      // Re-ativar após 3 segundos (caso haja erro)
      setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.innerHTML = '<span>Redefinir password</span><i class="bi bi-check-circle"></i>';
      }, 3000);
    });
  }

  function showError(input, message) {
    const parent = input.closest('.form-group');
    let errorEl = parent.querySelector('.form-error');
    
    if (!errorEl) {
      errorEl = document.createElement('span');
      errorEl.className = 'form-error';
      errorEl.innerHTML = `<i class="bi bi-exclamation-circle"></i>${message}`;
      parent.appendChild(errorEl);
    }
  }

  // ====================================
  // AUTO-TRIM EMAIL
  // ====================================
  const emailInput = document.querySelector('input[type="email"]');
  if (emailInput) {
    emailInput.addEventListener('blur', () => {
      emailInput.value = emailInput.value.trim().toLowerCase();
    });
  }

  // ====================================
  // FORM ANIMATION ON LOAD
  // ====================================
  const authCard = document.querySelector('.auth-card');
  
  if (authCard) {
    authCard.style.opacity = '0';
    authCard.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
      authCard.style.transition = 'all 0.6s ease';
      authCard.style.opacity = '1';
      authCard.style.transform = 'translateY(0)';
    }, 100);
  }

  // ====================================
  // FOCUS NO PRIMEIRO INPUT
  // ====================================
  const firstInput = form?.querySelector('.form-input');
  if (firstInput && !firstInput.value) {
    setTimeout(() => firstInput.focus(), 300);
  }

});