import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

export interface User {
  UtilisateurID: number;
  Email: string;
  Nom: string;
  Prenom: string;
  Type: string;
  technicienId?: number;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private currentUserSubject: BehaviorSubject<User | null>;
  public currentUser: Observable<User | null>;

  constructor() {
    const storedUser = localStorage.getItem('currentUser');
    this.currentUserSubject = new BehaviorSubject<User | null>(storedUser ? JSON.parse(storedUser) : null);
    this.currentUser = this.currentUserSubject.asObservable();
  }

  public get currentUserValue(): User | null {
    return this.currentUserSubject.value;
  }

  setCurrentUser(user: User) {
    // Si l'utilisateur est un technicien, on récupère son ID de technicien
    if (user.Type.toLowerCase() === 'technicien') {
      // Logique pour récupérer l'ID du technicien si nécessaire
      // Pour l'instant, on utilise l'ID utilisateur
      user.technicienId = user.UtilisateurID;
    }
    
    localStorage.setItem('currentUser', JSON.stringify(user));
    this.currentUserSubject.next(user);
  }

  logout() {
    localStorage.removeItem('currentUser');
    this.currentUserSubject.next(null);
  }

  isAuthenticated(): boolean {
    return !!this.currentUserValue;
  }

  isTechnicien(): boolean {
    return this.currentUserValue?.Type.toLowerCase() === 'technicien';
  }
}