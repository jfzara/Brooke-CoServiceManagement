import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { ApiResponse } from '../../models/api.model';
import { Technicien, TechnicienDisponibilite } from '../../models/technicien.model';

@Injectable({
  providedIn: 'root'
})
export class TechnicienService {
  private apiUrl = 'http://localhost:8000/api.php';

  constructor(private http: HttpClient) { }

  getAllTechniciens(): Observable<ApiResponse<Technicien[]>> {
    return this.http.get<ApiResponse<Technicien[]>>(
      `${this.apiUrl}?action=get_all_techniciens`
    ).pipe(
      tap(response => console.log('Réponse API getAllTechniciens:', response)),
      catchError(error => {
        console.error('Erreur API getAllTechniciens:', error);
        throw error;
      })
    );
  }

  getTechnicienById(id: number): Observable<ApiResponse<Technicien>> {
    return this.http.get<ApiResponse<Technicien>>(
      `${this.apiUrl}?action=get_technicien&id=${id}`
    ).pipe(
      tap(response => console.log('Réponse API getTechnicienById:', response)),
      catchError(error => {
        console.error('Erreur API getTechnicienById:', error);
        throw error;
      })
    );
  }

  getTechnicienDisponibilites(technicienId: number, dateDebut: string, dateFin: string): Observable<ApiResponse<TechnicienDisponibilite>> {
    return this.http.get<ApiResponse<TechnicienDisponibilite>>(
      `${this.apiUrl}?action=get_technicien_disponibilites&technicienId=${technicienId}&dateDebut=${dateDebut}&dateFin=${dateFin}`
    ).pipe(
      tap(response => console.log('Réponse API getTechnicienDisponibilites:', response)),
      catchError(error => {
        console.error('Erreur API getTechnicienDisponibilites:', error);
        throw error;
      })
    );
  }

  getTechniciensDisponibles(dateDebut: string, dateFin: string): Observable<ApiResponse<Technicien[]>> {
    return this.http.get<ApiResponse<Technicien[]>>(
      `${this.apiUrl}?action=get_techniciens_disponibles&dateDebut=${dateDebut}&dateFin=${dateFin}`
    ).pipe(
      tap(response => console.log('Réponse API getTechniciensDisponibles:', response)),
      catchError(error => {
        console.error('Erreur API getTechniciensDisponibles:', error);
        throw error;
      })
    );
  }

  updateDisponibilites(technicienId: number, disponibilites: TechnicienDisponibilite['disponibilites']): Observable<ApiResponse<void>> {
    return this.http.post<ApiResponse<void>>(
      `${this.apiUrl}?action=update_technicien_disponibilites`,
      {
        technicienId,
        disponibilites
      }
    ).pipe(
      tap(response => console.log('Réponse API updateDisponibilites:', response)),
      catchError(error => {
        console.error('Erreur API updateDisponibilites:', error);
        throw error;
      })
    );
  }
}