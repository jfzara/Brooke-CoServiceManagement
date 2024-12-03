// src/app/login/login.component.ts
import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { UserService } from '../core/services/user.service';
import { LoginResponse } from '../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  email: string = '';
  password: string = '';
  errorMessage: string = '';
  loading: boolean = false;

  constructor(
    private userService: UserService,
    private router: Router
  ) {}

  onSubmit(): void {
    if (!this.email || !this.password) {
      this.errorMessage = 'Veuillez remplir tous les champs';
      return;
    }

    this.loading = true;
    this.errorMessage = '';

    const loginData = {
      email: this.email,
      motDePasse: this.password
    };

    this.userService.login(loginData).subscribe({
      next: (response: LoginResponse) => {
        console.log('Réponse du serveur:', response);
        if (response.status === 'success' && response.result?.utilisateur) {
          const user = response.result.utilisateur;
          if (user.Type) {
            switch (user.Type.toLowerCase()) {
              case 'prepose':
                console.log(`Préposé ${user.Email} connecté!`);
                this.router.navigate(['/prepose']);
                break;
              case 'technicien':
                console.log(`Technicien ${user.Email} connecté!`);
                this.router.navigate(['/technicien']);
                break;
              case 'client':
                console.log(`Client ${user.Email} connecté!`);
                this.router.navigate(['/client']);
                break;
              default:
                this.errorMessage = 'Type d\'utilisateur non reconnu';
                this.router.navigate(['/']);
            }
          } else {
            this.errorMessage = 'Type d\'utilisateur manquant';
          }
        } else {
          this.errorMessage = response.message || 'Erreur de connexion';
        }
        this.loading = false;
      },
      error: (error: Error) => {
        console.error('Erreur de connexion:', error);
        this.errorMessage = error.message || 'Une erreur est survenue lors de la connexion';
        this.loading = false;
      }
    });
  }

  loginFacebook(): void {
    this.userService.loginWithFacebook()
      .then((authResponse: { accessToken: string }) => {
        console.log('Réponse de Facebook:', authResponse);
        const accessToken = authResponse.accessToken;
        
        this.userService.loginWithFacebookToken(accessToken).subscribe({
          next: (response: LoginResponse) => {
            console.log('Connexion Facebook réussie:', response);
            if (response.status === 'success' && response.result?.utilisateur) {
              const user = response.result.utilisateur;
              if (user.Type) {
                switch (user.Type.toLowerCase()) {
                  case 'prepose':
                    this.router.navigate(['/prepose']);
                    break;
                  case 'technicien':
                    this.router.navigate(['/technicien']);
                    break;
                  case 'client':
                    this.router.navigate(['/client']);
                    break;
                  default:
                    this.router.navigate(['/']);
                }
              }
            } else {
              this.errorMessage = response.message || 'Erreur lors de la connexion avec Facebook';
            }
          },
          error: (error: Error) => {
            console.error('Erreur de connexion Facebook:', error);
            this.errorMessage = 'Erreur lors de la connexion avec Facebook';
          }
        });
      })
      .catch((error: Error) => {
        console.error('Erreur Facebook:', error);
        this.errorMessage = 'Erreur lors de la connexion avec Facebook';
      });
  }

  loginGoogle(): void {
    // Implémentation à venir
    console.log('Connexion Google non implémentée');
  }
}