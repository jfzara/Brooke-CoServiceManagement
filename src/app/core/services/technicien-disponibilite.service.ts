// src/app/core/services/technicien-disponibilite.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { ApiResponse } from '../../models/api.model';
import { TechnicienDisponibilite, DisponibiliteHebdomadaire } from '../../models/technicien.model';

@Injectable({
  providedIn: 'root'
})
export class TechnicienDisponibiliteService {
  private apiUrl = 'http://localhost:8000/api.php';

  constructor(private http: HttpClient) {}

  getTechnicienDisponibilites(
    technicienId: number, 
    dateDebut: string, 
    dateFin: string
  ): Observable<ApiResponse<DisponibiliteHebdomadaire[]>> {
    return this.http.get<ApiResponse<DisponibiliteHebdomadaire[]>>(
      `${this.apiUrl}?action=get_technicien_disponibilites&technicienId=${technicienId}&dateDebut=${dateDebut}&dateFin=${dateFin}`
    ).pipe(
      tap(response => console.log('Disponibilités technicien:', response)),
      catchError(error => {
        console.error('Erreur récupération disponibilités:', error);
        throw error;
      })
    );
  }

  getTechniciensDisponibles(
    dateDebut: string, 
    dateFin: string
  ): Observable<ApiResponse<TechnicienDisponibilite[]>> {
    return this.http.get<ApiResponse<TechnicienDisponibilite[]>>(
      `${this.apiUrl}?action=get_techniciens_disponibles&dateDebut=${dateDebut}&dateFin=${dateFin}`
    ).pipe(
      tap(response => console.log('Techniciens disponibles:', response)),
      catchError(error => {
        console.error('Erreur récupération techniciens disponibles:', error);
        throw error;
      })
    );
  }

  updateDisponibilites(
    technicienId: number, 
    disponibilites: DisponibiliteHebdomadaire[]
  ): Observable<ApiResponse<void>> {
    return this.http.post<ApiResponse<void>>(
      `${this.apiUrl}?action=update_technicien_disponibilites`,
      { technicienId, disponibilites }
    ).pipe(
      tap(response => console.log('Mise à jour disponibilités:', response)),
      catchError(error => {
        console.error('Erreur mise à jour disponibilités:', error);
        throw error;
      })
    );
  }
}