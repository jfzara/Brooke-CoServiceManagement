import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { AuthService, User } from './auth.service';

declare global {
  interface Window {
    FB: {
      init(params: {
        appId: string;
        cookie: boolean;
        xfbml: boolean;
        version: string;
      }): void;
      login(callback: (response: any) => void, params: { scope: string }): void;
      api(path: string, callback: (response: any) => void): void;
    };
  }
}

interface LoginResponse {
  status: string;
  message: string;
  result?: {
    utilisateur: User;
  };
}

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = 'http://localhost:8000/api.php';
  private readonly fbAppId = '931570492178940';

  constructor(
    private http: HttpClient,
    private authService: AuthService
  ) {}

  public login(data: { email: string, motDePasse: string }): Observable<LoginResponse> {
    console.log("Tentative de connexion avec:", data);
    return this.http.post<LoginResponse>(`${this.apiUrl}?action=login`, data).pipe(
      tap((response: LoginResponse) => {
        console.log("Réponse du serveur:", response);
        if (response.status === 'success' && response.result?.utilisateur) {
          this.authService.setCurrentUser(response.result.utilisateur);
        }
      }),
      catchError(error => {
        console.error('Une erreur est survenue:', error);
        throw error.error?.message || 'Une erreur est survenue lors de la connexion';
      })
    );
  }

  public initFacebookSDK(): void {
    if (typeof window.FB === 'undefined') {
      console.error('Facebook SDK is not loaded yet');
      setTimeout(() => this.initFacebookSDK(), 1000);
      return;
    }

    window.FB.init({
      appId: this.fbAppId,
      cookie: true,
      xfbml: true,
      version: 'v21.0'
    });

    console.log('Facebook SDK initialized');
  }

  public loginWithFacebook(): Promise<any> {
    return new Promise((resolve, reject) => {
      window.FB.login((response: any) => {
        if (response.authResponse) {
          console.log('Connexion réussie avec Facebook');
          resolve(response.authResponse);
        } else {
          console.log('L\'utilisateur a annulé la connexion ou il y a eu une erreur.');
          reject('L\'utilisateur a annulé la connexion ou il y a eu une erreur.');
        }
      }, { scope: 'public_profile,email' });
    });
  }

  public loginWithFacebookToken(token: string): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${this.apiUrl}?action=login_facebook`, { token }).pipe(
      tap((response: LoginResponse) => {
        if (response.status === 'success' && response.result?.utilisateur) {
          this.authService.setCurrentUser(response.result.utilisateur);
        }
      }),
      catchError(error => {
        console.error('Erreur lors de la connexion avec Facebook:', error);
        throw error.error?.message || 'Erreur lors de la connexion avec Facebook';
      })
    );
  }

  public getUserInfo(): Promise<any> {
    return new Promise((resolve, reject) => {
      window.FB.api('/me?fields=id,name,email', (response: any) => {
        if (response && !response.error) {
          resolve(response);
        } else {
          console.error('Erreur lors de l\'obtention des informations de l\'utilisateur:', response.error);
          reject('Échec de la récupération des informations utilisateur');
        }
      });
    });
  }
}