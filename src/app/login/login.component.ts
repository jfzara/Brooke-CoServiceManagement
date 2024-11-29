import { Component, OnInit } from '@angular/core';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { Router } from '@angular/router';
import { UserService } from '../services/user.service';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [MatFormFieldModule, MatInputModule, MatButtonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
})
export class LoginComponent implements OnInit {
  email: string = '';
  password: string = '';
  errorMessage: string = '';

  constructor(private userService: UserService, private router: Router) {}

  ngOnInit(): void {
    this.userService.initFacebookSDK();
  }

  onSubmit(): void {
    if (this.email && this.password) {
      const data = {
        email: this.email,
        motDePasse: this.password
      };
      
      this.userService.login(data).subscribe({
        next: (response) => {
          console.log('Réponse du serveur:', response);
          if (response.status === 'success' && response.result?.utilisateur) {
            const user = response.result.utilisateur;
            switch (user.Type.toLowerCase()) {
              case 'prepose':
                console.log(`Prepose ${user.Email} connecté!`);
                this.router.navigate(['/prepose']);
                break;
              case 'technicien':
                console.log(`Technicien ${user.Email} connecté!`);
                this.router.navigate(['/technicien']);
                break;
              case 'client':
                console.log(`Client ${user.Email} connecté!`);
                break;
              default:
                this.router.navigate(['/']);
            }
          } else {
            this.errorMessage = response.message || 'Erreur de connexion';
          }
        },
        error: (err) => {
          console.error('Erreur de connexion:', err);
          this.errorMessage = err.message || 'Identifiants invalides. Veuillez réessayer.';
        }
      });
    } else {
      this.errorMessage = 'Veuillez remplir tous les champs.';
    }
  }

  loginFacebook(): void {
    console.log("Connexion avec Facebook");
    this.userService.loginWithFacebook().then(authResponse => {
      console.log(authResponse);
      const accessToken = authResponse.accessToken;
      
      this.userService.loginWithFacebookToken(accessToken).subscribe(
        (response: any) => {
          console.log('Utilisateur connecté avec succès:', response);
          if (response.status === 'success' && response.result?.utilisateur) {
            const user = response.result.utilisateur;
            switch (user.Type.toLowerCase()) {
              case 'prepose':
                console.log(`Prepose ${user.Email} connecté!`);
                this.router.navigate(['/prepose']);
                break;
              case 'technicien':
                console.log(`Technicien ${user.Email} connecté!`);
                this.router.navigate(['/technicien']);
                break;
              case 'client':
                console.log(`Client ${user.Email} connecté!`);
                break;
              default:
                this.router.navigate(['/']);
            }
          } else {
            this.errorMessage = response.message || 'Erreur lors de la connexion avec Facebook';
          }
        },
        (error) => {
          console.error('Erreur lors de la connexion au serveur:', error);
          this.errorMessage = 'Erreur lors de la connexion avec Facebook';
        }
      );
    }).catch(error => {
      console.error('Error logging in:', error);
      this.errorMessage = 'Erreur lors de la connexion avec Facebook';
    });
  }

  loginGoogle() {
    console.log('Method not implemented.');
  }
}