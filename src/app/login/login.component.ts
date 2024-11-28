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

  // Méthode pour soumettre le formulaire de connexion
  onSubmit(): void {
    if (this.email && this.password) {
      const data = {
        email : this.email,
        motDePasse : this.password
      }
      // Appeler le service login avec les données du formulaire
      this.userService.login(data).subscribe({
        next: (response) => {
          console.log(response)
          // On redirige l'utilisateur après une connexion réussie (si nécessaire)
          if(response.result.utilisateur){
            const user = response.result.utilisateur;
            if(user.Type === "prepose"){
              console.log("Prepose " + user.Email + " connecte!")
              this.router.navigate(['/prepose']);
            } else if(user.Type === "technicien"){
              console.log("Technicien " + user.Email + " connecte!")
              this.router.navigate(['/technicien']);
            } else if(user.Type === "client"){
              console.log("Client " + user.Email + " connecte!")
            }
          }
          //this.router.navigate(['/dashboard']); // Remplace 'dashboard' par la route désirée
        },
        error: (err) => {
          console.log(err)
          // Si une erreur se produit, afficher un message d'erreur
          this.errorMessage = 'Identifiants invalides. Veuillez réessayer.';
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
  
      // Récupérer le token d'accès de Facebook
      const accessToken = authResponse.accessToken;
  
      // Envoi du token au backend pour validation et création de la session
      this.userService.loginWithFacebookToken(accessToken).subscribe(
        (response: any) => {
          console.log('Utilisateur connecté avec succès:', response);
          // Gérer la réponse du serveur ici (par exemple, stocker les informations de l'utilisateur ou rediriger)
          // On redirige l'utilisateur après une connexion réussie (si nécessaire)
          if(response.utilisateur){
            const user = response.utilisateur;
            if(user.Type === "prepose"){
              console.log("Prepose " + user.Email + " connecte!")
              this.router.navigate(['/prepose']);
            } else if(user.Type === "technicien"){
              console.log("Technicien " + user.Email + " connecte!")
              this.router.navigate(['/technicien']);
            } else if(user.Type === "client"){
              console.log("Client " + user.Email + " connecte!")
            }
          }
        },
        (error) => {
          console.error('Erreur lors de la connexion au serveur:', error);
        }
      );
      
    }).catch(error => {
      console.error('Error logging in:', error);
    });
  } 

  loginGoogle() {
    console.log('Method not implemented.');
  }
}