import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

declare var FB: any;

@Injectable({
  providedIn: 'root'
})
export class UserService {


  private apiUrl = 'http://localhost:8000/api.php'
  // ancienne url causant une rerreur  =>   private apiUrl = 'http://localhost:8087/brooke_co/backend/public/api.php';

  constructor(private http: HttpClient) { }

  // Fonction de login classique via l'API
  public login(data: any): Observable<any> {
    console.log(data);
    return this.http.post(`${this.apiUrl}?action=login`, data).pipe(
      catchError(error => {
        console.error('Erreur lors de la connexion:', error);
        return throwError(() => new Error('Erreur de connexion'));
      })
    );
  }

  // Initialisation du SDK Facebook (attend que le SDK soit chargé)
  public initFacebookSDK(): void {
    if (typeof FB === 'undefined') {
      // Facebook SDK n'est pas encore chargé, attendre qu'il soit prêt
      console.error('Facebook SDK is not loaded yet');
      setTimeout(() => this.initFacebookSDK(), 1000); // Réessayer après 1 seconde
      return;
    }

    // Initialiser le SDK Facebook
    FB.init({
      appId: '931570492178940',  // Remplacez par votre App ID (id de votre application Facebook)
      cookie: true,
      xfbml: true,
      version: 'v21.0'  // Utilisez la dernière version disponible du SDK
    });

    console.log('Facebook SDK initialized');
  }

  // Fonction de login avec Facebook
  public loginWithFacebook(): Promise<any> {
    return new Promise((resolve, reject) => {
      /*FB.getLoginStatus((response: any) => {
        if (response.status === 'connected') {
          console.log('Utilisateur déjà connecté à Facebook');
          resolve(response.authResponse);  // Résoudre avec les informations d'authentification
        } else {*/
          FB.login((response: any) => {
            if (response.authResponse) {
              console.log('Connexion réussie avec Facebook');
              resolve(response.authResponse);  // Résoudre avec les informations d'authentification
            } else {
              console.log('L\'utilisateur a annulé la connexion ou il y a eu une erreur.');
              reject('L\'utilisateur a annulé la connexion ou il y a eu une erreur.');
            }
          }, { scope: 'public_profile,email' });
      //  }
      //});
    });
  }

  // Fonction de login avec le token d'authentification Facebook
  public loginWithFacebookToken(token: string): Observable<any> {
    console.log('Envoi du token Facebook au serveur:', token);
    return this.http.post(`${this.apiUrl}?action=login_facebook`, { token: token }).pipe(
      catchError(error => {
        console.error('Erreur lors de l\'authentification avec Facebook:', error);
        throw error;  // Vous pouvez également gérer l'erreur ici
      })
    );
  }

  // Fonction pour obtenir les informations de l'utilisateur Facebook
  public getUserInfo(): Promise<any> {
    return new Promise((resolve, reject) => {
      FB.api('/me?fields=id,name,email', (response: any) => {
        if (response && !response.error) {
          resolve(response);  // Résoudre avec les données utilisateur
        } else {
          console.error('Erreur lors de l\'obtention des informations de l\'utilisateur:', response.error);
          reject('Échec de la récupération des informations utilisateur');
        }
      });
    });
  }
}