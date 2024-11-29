import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {
  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
    const currentUser = this.authService.currentUserValue;
    
    if (currentUser) {
      // Vérifie si la route a des rôles requis
      if (route.data['roles'] && !route.data['roles'].includes(currentUser.Type?.toLowerCase())) {
        console.log('Accès refusé - Rôle non autorisé');
        this.router.navigate(['/login']);
        return false;
      }

      return true;
    }

    console.log('Accès refusé - Utilisateur non connecté');
    this.router.navigate(['/login']);
    return false;
  }
}