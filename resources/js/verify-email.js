// ====================================
// VERIFY EMAIL JS
// ====================================

document.addEventListener('DOMContentLoaded', () => {

  // ====================================
  // PREVENT MULTIPLE SUBMIT
  // ====================================
  const forms = document.querySelectorAll('form');
  
  forms.forEach(form => {
    form.addEventListener('submit', (e) => {
      const submitBtn = form.querySelector('button[type="submit"]');
      
      if (submitBtn && submitBtn.disabled) {
        e.preventDefault();
        return;
      }
      
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
        
        // Guardar HTML original
        const originalHTML = submitBtn.innerHTML;
        
        // Mudar para loading
        submitBtn.innerHTML = '<span>A processar...</span><i class="bi bi-hourglass-split"></i>';
        
        // Re-ativar após 3 segundos (caso haja erro)
        setTimeout(() => {
          submitBtn.disabled = false;
          submitBtn.style.opacity = '1';
          submitBtn.innerHTML = originalHTML;
        }, 3000);
      }
    });
  });

  // ====================================
  // ALERT AUTO-DISMISS
  // ====================================
  const alert = document.querySelector('.alert');
  if (alert) {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-10px)';
      setTimeout(() => alert.remove(), 300);
    }, 10000); // 10 segundos
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
  // COUNTDOWN TIMER (opcional)
  // ====================================
  // Podes adicionar um timer para reenviar email
  let canResend = true;
  const resendBtn = document.querySelector('.btn-submit');
  
  if (resendBtn) {
    resendBtn.addEventListener('click', function() {
      if (!canResend) {
        return;
      }
      
      canResend = false;
      let countdown = 60;
      
      const interval = setInterval(() => {
        countdown--;
        if (countdown > 0) {
          resendBtn.innerHTML = `<span>Aguarda ${countdown}s</span><i class="bi bi-clock"></i>`;
          resendBtn.disabled = true;
          resendBtn.style.opacity = '0.6';
        } else {
          clearInterval(interval);
          resendBtn.innerHTML = '<span>Reenviar email de verificação</span><i class="bi bi-send"></i>';
          resendBtn.disabled = false;
          resendBtn.style.opacity = '1';
          canResend = true;
        }
      }, 1000);
    });
  }

});