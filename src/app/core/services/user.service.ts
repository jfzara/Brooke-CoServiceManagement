import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, from } from 'rxjs';
import { switchMap } from 'rxjs/operators';
import { AuthService, LoginResponse } from './auth.service';

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

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private readonly fbAppId = '931570492178940';

  constructor(
    private authService: AuthService
  ) {}

  public login(credentials: { email: string; motDePasse: string }): Observable<LoginResponse> {
    return this.authService.login(credentials.email, credentials.motDePasse);
  }

  public initFacebookSDK(): void {
    if (typeof window.FB === 'undefined') {
      console.error('Facebook SDK non chargé');
      setTimeout(() => this.initFacebookSDK(), 1000);
      return;
    }

    window.FB.init({
      appId: this.fbAppId,
      cookie: true,
      xfbml: true,
      version: 'v21.0'
    });

    console.log('Facebook SDK initialisé');
  }

  public loginWithFacebook(): Promise<any> {
    return new Promise((resolve, reject) => {
      window.FB.login((response: any) => {
        if (response.authResponse) {
          console.log('Connexion réussie avec Facebook');
          resolve(response.authResponse);
        } else {
          reject('L\'utilisateur a annulé la connexion ou il y a eu une erreur.');
        }
      }, { scope: 'public_profile,email' });
    });
  }

  public loginWithFacebookToken(token: string): Observable<LoginResponse> {
    return this.authService.loginWithFacebook(token);
  }

  public getUserInfo(): Promise<any> {
    return new Promise((resolve, reject) => {
      window.FB.api('/me?fields=id,name,email', (response: any) => {
        if (response && !response.error) {
          resolve(response);
        } else {
          reject('Échec de la récupération des informations utilisateur');
        }
      });
    });
  }
}