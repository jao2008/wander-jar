// ====================================
// CONFIRM PASSWORD JS
// ====================================

document.addEventListener('DOMContentLoaded', () => {

  // ====================================
  // PASSWORD TOGGLE (Mostrar/Esconder)
  // ====================================
  const passwordToggle = document.querySelector('.password-toggle');
  
  if (passwordToggle) {
    passwordToggle.addEventListener('click', function(e) {
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
  }

  // ====================================
  // FORM VALIDATION FEEDBACK
  // ====================================
  const form = document.querySelector('.auth-form');
  
  if (form) {
    const passwordInput = form.querySelector('input[type="password"]');
    
    // Remove classe de erro ao começar a escrever
    if (passwordInput) {
      passwordInput.addEventListener('input', () => {
        if (passwordInput.classList.contains('is-invalid')) {
          passwordInput.classList.remove('is-invalid');
          
          const errorMsg = passwordInput.parentElement.querySelector('.form-error');
          if (errorMsg) {
            errorMsg.style.opacity = '0';
            setTimeout(() => errorMsg.remove(), 300);
          }
        }
      });
    }
    
    // Prevenir submit múltiplo
    form.addEventListener('submit', (e) => {
      const submitBtn = form.querySelector('.btn-submit');
      
      if (submitBtn.disabled) {
        e.preventDefault();
        return;
      }
      
      submitBtn.disabled = true;
      submitBtn.style.opacity = '0.7';
      submitBtn.innerHTML = '<span>A confirmar...</span><i class="bi bi-hourglass-split"></i>';
      
      // Re-ativar após 3 segundos (caso haja erro)
      setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.innerHTML = '<span>Confirmar</span><i class="bi bi-check-circle"></i>';
      }, 3000);
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
  // FOCUS NO INPUT
  // ====================================
  const passwordInput = form?.querySelector('input[type="password"]');
  if (passwordInput) {
    setTimeout(() => passwordInput.focus(), 300);
  }

});