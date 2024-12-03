import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { map, tap } from 'rxjs/operators';

export interface User {
  id?: number;
  UtilisateurID?: number;
  Email: string;
  Nom?: string;
  Prenom?: string;
  Type?: string;
  technicienId?: number;
}

export interface LoginResponse {
  status: string;
  message: string;
  result?: {
    utilisateur: {
      UtilisateurID: number;
      Email: string;
      Nom: string;
      Prenom: string;
      Type: string;
    };
  };
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api.php';
  private currentUserSubject: BehaviorSubject<User | null>;
  public currentUser: Observable<User | null>;

  constructor(private http: HttpClient) {
    const storedUser = localStorage.getItem('currentUser');
    this.currentUserSubject = new BehaviorSubject<User | null>(storedUser ? JSON.parse(storedUser) : null);
    this.currentUser = this.currentUserSubject.asObservable();
  }

  public get currentUserValue(): User | null {
    return this.currentUserSubject.value;
  }

  public get userProfile() {
    const user = this.currentUserValue;
    return {
      fullName: user ? `${user.Prenom} ${user.Nom}` : 'Technicien',
      email: user?.Email || '',
      type: user?.Type || '',
      technicienId: user?.technicienId || user?.UtilisateurID
    };
  }

  login(email: string, password: string): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${this.apiUrl}?action=login`, { 
      email, 
      motDePasse: password 
    }).pipe(
      tap(response => {
        if (response.status === 'success' && response.result?.utilisateur) {
          const utilisateur = response.result.utilisateur;
          const user: User = {
            ...utilisateur,
            technicienId: utilisateur.Type?.toLowerCase() === 'technicien' ? 
              utilisateur.UtilisateurID : undefined
          };
          localStorage.setItem('currentUser', JSON.stringify(user));
          this.currentUserSubject.next(user);
        }
      })
    );
  }

  loginWithFacebook(token: string): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${this.apiUrl}?action=login_facebook`, { token }).pipe(
      tap(response => {
        if (response.status === 'success' && response.result?.utilisateur) {
          const utilisateur = response.result.utilisateur;
          const user: User = {
            ...utilisateur,
            technicienId: utilisateur.Type?.toLowerCase() === 'technicien' ? 
              utilisateur.UtilisateurID : undefined
          };
          localStorage.setItem('currentUser', JSON.stringify(user));
          this.currentUserSubject.next(user);
        }
      })
    );
  }

  logout(): void {
    localStorage.removeItem('currentUser');
    localStorage.removeItem('token');
    this.currentUserSubject.next(null);
    window.location.href = '/login'; // Ajout de cette ligne pour forcer la redirection
  }

  isAuthenticated(): boolean {
    const currentUser = this.currentUserValue;
    return currentUser !== null && currentUser.Type !== undefined;
  }

  hasRole(role: string): boolean {
    const currentUser = this.currentUserValue;
    return currentUser?.Type?.toLowerCase() === role.toLowerCase();
  }

  checkAuthentication(): boolean {
    const currentUser = this.currentUserValue;
    if (!currentUser) {
      window.location.href = '/login';
      return false;
    }
    return true;
  }
}