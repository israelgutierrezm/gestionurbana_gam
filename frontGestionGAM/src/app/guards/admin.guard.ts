import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { GuardsService } from './services/guards.service';

export const adminGuard: CanActivateFn = (route, state) => {
  const guardsService = inject(GuardsService)
  const router = inject(Router)

  return guardsService.verificaJWTGuard(1).then(isValid => {
    if (isValid) {
      return true;
    } else {
      router.navigate(['/login']);
      return false;
    }
  }).catch(error => {
    console.error('Error de sesión:', error);
    router.navigate(['/login']);
    return false;
  });
};
